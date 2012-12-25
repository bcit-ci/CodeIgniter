<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2012 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mustache Tokenizer class.
 *
 * This class is responsible for turning raw template source into a set of Mustache tokens.
 */
class Mustache_Tokenizer
{

    // Finite state machine states
    const IN_TEXT     = 0;
    const IN_TAG_TYPE = 1;
    const IN_TAG      = 2;

    // Token types
    const T_SECTION      = '#';
    const T_INVERTED     = '^';
    const T_END_SECTION  = '/';
    const T_COMMENT      = '!';
    const T_PARTIAL      = '>';
    const T_PARTIAL_2    = '<';
    const T_DELIM_CHANGE = '=';
    const T_ESCAPED      = '_v';
    const T_UNESCAPED    = '{';
    const T_UNESCAPED_2  = '&';
    const T_TEXT         = '_t';

    // Valid token types
    private static $tagTypes = array(
        self::T_SECTION      => true,
        self::T_INVERTED     => true,
        self::T_END_SECTION  => true,
        self::T_COMMENT      => true,
        self::T_PARTIAL      => true,
        self::T_PARTIAL_2    => true,
        self::T_DELIM_CHANGE => true,
        self::T_ESCAPED      => true,
        self::T_UNESCAPED    => true,
        self::T_UNESCAPED_2  => true,
    );

    // Interpolated tags
    private static $interpolatedTags = array(
        self::T_ESCAPED      => true,
        self::T_UNESCAPED    => true,
        self::T_UNESCAPED_2  => true,
    );

    // Token properties
    const TYPE   = 'type';
    const NAME   = 'name';
    const OTAG   = 'otag';
    const CTAG   = 'ctag';
    const INDEX  = 'index';
    const END    = 'end';
    const INDENT = 'indent';
    const NODES  = 'nodes';
    const VALUE  = 'value';

    private $state;
    private $tagType;
    private $tag;
    private $buffer;
    private $tokens;
    private $seenTag;
    private $lineStart;
    private $otag;
    private $ctag;

    /**
     * Scan and tokenize template source.
     *
     * @param string $text       Mustache template source to tokenize
     * @param string $delimiters Optionally, pass initial opening and closing delimiters (default: null)
     *
     * @return array Set of Mustache tokens
     */
    public function scan($text, $delimiters = null)
    {
        $this->reset();

        if ($delimiters = trim($delimiters)) {
            list($otag, $ctag) = explode(' ', $delimiters);
            $this->otag = $otag;
            $this->ctag = $ctag;
        }

        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            switch ($this->state) {
                case self::IN_TEXT:
                    if ($this->tagChange($this->otag, $text, $i)) {
                        $i--;
                        $this->flushBuffer();
                        $this->state = self::IN_TAG_TYPE;
                    } else {
                        if ($text[$i] == "\n") {
                            $this->filterLine();
                        } else {
                            $this->buffer .= $text[$i];
                        }
                    }
                    break;

                case self::IN_TAG_TYPE:

                    $i += strlen($this->otag) - 1;
                    if (isset(self::$tagTypes[$text[$i + 1]])) {
                        $tag = $text[$i + 1];
                        $this->tagType = $tag;
                    } else {
                        $tag = null;
                        $this->tagType = self::T_ESCAPED;
                    }

                    if ($this->tagType === self::T_DELIM_CHANGE) {
                        $i = $this->changeDelimiters($text, $i);
                        $this->state = self::IN_TEXT;
                    } else {
                        if ($tag !== null) {
                            $i++;
                        }
                        $this->state = self::IN_TAG;
                    }
                    $this->seenTag = $i;
                    break;

                default:
                    if ($this->tagChange($this->ctag, $text, $i)) {
                        $this->tokens[] = array(
                            self::TYPE  => $this->tagType,
                            self::NAME  => trim($this->buffer),
                            self::OTAG  => $this->otag,
                            self::CTAG  => $this->ctag,
                            self::INDEX => ($this->tagType == self::T_END_SECTION) ? $this->seenTag - strlen($this->otag) : $i + strlen($this->ctag)
                        );

                        $this->buffer = '';
                        $i += strlen($this->ctag) - 1;
                        $this->state = self::IN_TEXT;
                        if ($this->tagType == self::T_UNESCAPED) {
                            if ($this->ctag == '}}') {
                                $i++;
                            } else {
                                // Clean up `{{{ tripleStache }}}` style tokens.
                                $lastName = $this->tokens[count($this->tokens) - 1][self::NAME];
                                if (substr($lastName, -1) === '}') {
                                    $this->tokens[count($this->tokens) - 1][self::NAME] = trim(substr($lastName, 0, -1));
                                }
                            }
                        }
                    } else {
                        $this->buffer .= $text[$i];
                    }
                    break;
            }
        }

        $this->filterLine(true);

        return $this->tokens;
    }

    /**
     * Helper function to reset tokenizer internal state.
     */
    private function reset()
    {
        $this->state     = self::IN_TEXT;
        $this->tagType   = null;
        $this->tag       = null;
        $this->buffer    = '';
        $this->tokens    = array();
        $this->seenTag   = false;
        $this->lineStart = 0;
        $this->otag      = '{{';
        $this->ctag      = '}}';
    }

    /**
     * Flush the current buffer to a token.
     */
    private function flushBuffer()
    {
        if (!empty($this->buffer)) {
            $this->tokens[] = array(self::TYPE  => self::T_TEXT, self::VALUE => $this->buffer);
            $this->buffer   = '';
        }
    }

    /**
     * Test whether the current line is entirely made up of whitespace.
     *
     * @return boolean True if the current line is all whitespace
     */
    private function lineIsWhitespace()
    {
        $tokensCount = count($this->tokens);
        for ($j = $this->lineStart; $j < $tokensCount; $j++) {
            $token = $this->tokens[$j];
            if (isset(self::$tagTypes[$token[self::TYPE]])) {
                if (isset(self::$interpolatedTags[$token[self::TYPE]])) {
                    return false;
                }
            } elseif ($token[self::TYPE] == self::T_TEXT) {
                if (preg_match('/\S/', $token[self::VALUE])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Filter out whitespace-only lines and store indent levels for partials.
     *
     * @param bool $noNewLine Suppress the newline? (default: false)
     */
    private function filterLine($noNewLine = false)
    {
        $this->flushBuffer();
        if ($this->seenTag && $this->lineIsWhitespace()) {
            $tokensCount = count($this->tokens);
            for ($j = $this->lineStart; $j < $tokensCount; $j++) {
                if ($this->tokens[$j][self::TYPE] == self::T_TEXT) {
                    if (isset($this->tokens[$j+1]) && $this->tokens[$j+1][self::TYPE] == self::T_PARTIAL) {
                        $this->tokens[$j+1][self::INDENT] = $this->tokens[$j][self::VALUE];
                    }

                    $this->tokens[$j] = null;
                }
            }
        } elseif (!$noNewLine) {
            $this->tokens[] = array(self::TYPE => self::T_TEXT, self::VALUE => "\n");
        }

        $this->seenTag   = false;
        $this->lineStart = count($this->tokens);
    }

    /**
     * Change the current Mustache delimiters. Set new `otag` and `ctag` values.
     *
     * @param string $text  Mustache template source
     * @param int    $index Current tokenizer index
     *
     * @return int New index value
     */
    private function changeDelimiters($text, $index)
    {
        $startIndex = strpos($text, '=', $index) + 1;
        $close      = '='.$this->ctag;
        $closeIndex = strpos($text, $close, $index);

        list($otag, $ctag) = explode(' ', trim(substr($text, $startIndex, $closeIndex - $startIndex)));
        $this->otag = $otag;
        $this->ctag = $ctag;

        return $closeIndex + strlen($close) - 1;
    }

    /**
     * Test whether it's time to change tags.
     *
     * @param string $tag   Current tag name
     * @param string $text  Mustache template source
     * @param int    $index Current tokenizer index
     *
     * @return boolean True if this is a closing section tag
     */
    private function tagChange($tag, $text, $index)
    {
        return substr($text, $index, strlen($tag)) === $tag;
    }
}
