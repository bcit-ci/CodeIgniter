<?php

namespace PhpParser\Lexer;

use PhpParser\Parser\Tokens;

/**
 * ATTENTION: This code is WRITE-ONLY. Do not try to read it.
 */
class Emulative extends \PhpParser\Lexer
{
    protected $newKeywords;
    protected $inObjectAccess;

    const T_ELLIPSIS   = 1001;
    const T_POW        = 1002;
    const T_POW_EQUAL  = 1003;
    const T_COALESCE   = 1004;
    const T_SPACESHIP  = 1005;
    const T_YIELD_FROM = 1006;

    const PHP_7_0 = '7.0.0dev';
    const PHP_5_6 = '5.6.0rc1';
    const PHP_5_5 = '5.5.0beta1';

    public function __construct(array $options = array()) {
        parent::__construct($options);

        $newKeywordsPerVersion = array(
            self::PHP_5_5 => array(
                'finally'       => Tokens::T_FINALLY,
                'yield'         => Tokens::T_YIELD,
            ),
        );

        $this->newKeywords = array();
        foreach ($newKeywordsPerVersion as $version => $newKeywords) {
            if (version_compare(PHP_VERSION, $version, '>=')) {
                break;
            }

            $this->newKeywords += $newKeywords;
        }

        if (version_compare(PHP_VERSION, self::PHP_7_0, '>=')) {
            return;
        }
        $this->tokenMap[self::T_COALESCE]   = Tokens::T_COALESCE;
        $this->tokenMap[self::T_SPACESHIP]  = Tokens::T_SPACESHIP;
        $this->tokenMap[self::T_YIELD_FROM] = Tokens::T_YIELD_FROM;

        if (version_compare(PHP_VERSION, self::PHP_5_6, '>=')) {
            return;
        }
        $this->tokenMap[self::T_ELLIPSIS]  = Tokens::T_ELLIPSIS;
        $this->tokenMap[self::T_POW]       = Tokens::T_POW;
        $this->tokenMap[self::T_POW_EQUAL] = Tokens::T_POW_EQUAL;
    }

    public function startLexing($code) {
        $this->inObjectAccess = false;

        $preprocessedCode = $this->preprocessCode($code);
        parent::startLexing($preprocessedCode);
        if ($preprocessedCode !== $code) {
            $this->postprocessTokens();
        }

        // Set code property back to the original code, so __halt_compiler()
        // handling and (start|end)FilePos attributes use the correct offsets
        $this->code = $code;
    }

    /*
     * Replaces new features in the code by ~__EMU__{NAME}__{DATA}__~ sequences.
     * ~LABEL~ is never valid PHP code, that's why we can (to some degree) safely
     * use it here.
     * Later when preprocessing the tokens these sequences will either be replaced
     * by real tokens or replaced with their original content (e.g. if they occurred
     * inside a string, i.e. a place where they don't have a special meaning).
     */
    protected function preprocessCode($code) {
        if (version_compare(PHP_VERSION, self::PHP_7_0, '>=')) {
            return $code;
        }

        $code = str_replace('??', '~__EMU__COALESCE__~', $code);
        $code = str_replace('<=>', '~__EMU__SPACESHIP__~', $code);
        $code = preg_replace_callback('(yield[ \n\r\t]+from)', function($matches) {
            // Encoding $0 in order to preserve exact whitespace
            return '~__EMU__YIELDFROM__' . bin2hex($matches[0]) . '__~';
        }, $code);

        if (version_compare(PHP_VERSION, self::PHP_5_6, '>=')) {
            return $code;
        }

        $code = str_replace('...', '~__EMU__ELLIPSIS__~', $code);
        $code = preg_replace('((?<!/)\*\*=)', '~__EMU__POWEQUAL__~', $code);
        $code = preg_replace('((?<!/)\*\*(?!/))', '~__EMU__POW__~', $code);

        return $code;
    }

    /*
     * Replaces the ~__EMU__...~ sequences with real tokens or their original
     * value.
     */
    protected function postprocessTokens() {
        // we need to manually iterate and manage a count because we'll change
        // the tokens array on the way
        for ($i = 0, $c = count($this->tokens); $i < $c; ++$i) {
            // first check that the following tokens are of form ~LABEL~,
            // then match the __EMU__... sequence.
            if ('~' === $this->tokens[$i]
                && isset($this->tokens[$i + 2])
                && '~' === $this->tokens[$i + 2]
                && T_STRING === $this->tokens[$i + 1][0]
                && preg_match('(^__EMU__([A-Z]++)__(?:([A-Za-z0-9]++)__)?$)', $this->tokens[$i + 1][1], $matches)
            ) {
                if ('ELLIPSIS' === $matches[1]) {
                    $replace = array(
                        array(self::T_ELLIPSIS, '...', $this->tokens[$i + 1][2])
                    );
                } else if ('POW' === $matches[1]) {
                    $replace = array(
                        array(self::T_POW, '**', $this->tokens[$i + 1][2])
                    );
                } else if ('POWEQUAL' === $matches[1]) {
                    $replace = array(
                        array(self::T_POW_EQUAL, '**=', $this->tokens[$i + 1][2])
                    );
                } else if ('COALESCE' === $matches[1]) {
                    $replace = array(
                        array(self::T_COALESCE, '??', $this->tokens[$i + 1][2])
                    );
                } else if ('SPACESHIP' === $matches[1]) {
                    $replace = array(
                        array(self::T_SPACESHIP, '<=>', $this->tokens[$i + 1][2]),
                    );
                } else if ('YIELDFROM' === $matches[1]) {
                    $content = hex2bin($matches[2]);
                    $replace = array(
                        array(self::T_YIELD_FROM, $content, $this->tokens[$i + 1][2] - substr_count($content, "\n"))
                    );
                } else {
                    throw new \RuntimeException('Invalid __EMU__ sequence');
                }

                array_splice($this->tokens, $i, 3, $replace);
                $c -= 3 - count($replace);
            // for multichar tokens (e.g. strings) replace any ~__EMU__...~ sequences
            // in their content with the original character sequence
            } elseif (is_array($this->tokens[$i])
                      && 0 !== strpos($this->tokens[$i][1], '__EMU__')
            ) {
                $this->tokens[$i][1] = preg_replace_callback(
                    '(~__EMU__([A-Z]++)__(?:([A-Za-z0-9]++)__)?~)',
                    array($this, 'restoreContentCallback'),
                    $this->tokens[$i][1]
                );
            }
        }
    }

    /*
     * This method is a callback for restoring EMU sequences in
     * multichar tokens (like strings) to their original value.
     */
    public function restoreContentCallback(array $matches) {
        if ('ELLIPSIS' === $matches[1]) {
            return '...';
        } else if ('POW' === $matches[1]) {
            return '**';
        } else if ('POWEQUAL' === $matches[1]) {
            return '**=';
        } else if ('COALESCE' === $matches[1]) {
            return '??';
        } else if ('SPACESHIP' === $matches[1]) {
            return '<=>';
        } else if ('YIELDFROM' === $matches[1]) {
            return hex2bin($matches[2]);
        } else {
            return $matches[0];
        }
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $token = parent::getNextToken($value, $startAttributes, $endAttributes);

        // replace new keywords by their respective tokens. This is not done
        // if we currently are in an object access (e.g. in $obj->namespace
        // "namespace" stays a T_STRING tokens and isn't converted to T_NAMESPACE)
        if (Tokens::T_STRING === $token && !$this->inObjectAccess) {
            if (isset($this->newKeywords[strtolower($value)])) {
                return $this->newKeywords[strtolower($value)];
            }
        } else {
            // keep track of whether we currently are in an object access (after ->)
            $this->inObjectAccess = Tokens::T_OBJECT_OPERATOR === $token;
        }

        return $token;
    }
}
