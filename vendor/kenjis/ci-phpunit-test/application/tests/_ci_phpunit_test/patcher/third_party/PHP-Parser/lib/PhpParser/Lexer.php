<?php

namespace PhpParser;

use PhpParser\Parser\Tokens;

class Lexer
{
    protected $code;
    protected $tokens;
    protected $pos;
    protected $line;
    protected $filePos;

    protected $tokenMap;
    protected $dropTokens;

    protected $usedAttributes;

    /**
     * Creates a Lexer.
     *
     * @param array $options Options array. Currently only the 'usedAttributes' option is supported,
     *                       which is an array of attributes to add to the AST nodes. Possible attributes
     *                       are: 'comments', 'startLine', 'endLine', 'startTokenPos', 'endTokenPos',
     *                       'startFilePos', 'endFilePos'. The option defaults to the first three.
     *                       For more info see getNextToken() docs.
     */
    public function __construct(array $options = array()) {
        // map from internal tokens to PhpParser tokens
        $this->tokenMap = $this->createTokenMap();

        // map of tokens to drop while lexing (the map is only used for isset lookup,
        // that's why the value is simply set to 1; the value is never actually used.)
        $this->dropTokens = array_fill_keys(array(T_WHITESPACE, T_OPEN_TAG), 1);

        // the usedAttributes member is a map of the used attribute names to a dummy
        // value (here "true")
        $options += array(
            'usedAttributes' => array('comments', 'startLine', 'endLine'),
        );
        $this->usedAttributes = array_fill_keys($options['usedAttributes'], true);
    }

    /**
     * Initializes the lexer for lexing the provided source code.
     *
     * @param string $code The source code to lex
     *
     * @throws Error on lexing errors (unterminated comment or unexpected character)
     */
    public function startLexing($code) {
        $scream = ini_set('xdebug.scream', '0');

        $this->resetErrors();
        $this->tokens = @token_get_all($code);
        $this->handleErrors();

        if (false !== $scream) {
            ini_set('xdebug.scream', $scream);
        }

        $this->code = $code; // keep the code around for __halt_compiler() handling
        $this->pos  = -1;
        $this->line =  1;
        $this->filePos = 0;
    }

    protected function resetErrors() {
        if (function_exists('error_clear_last')) {
            error_clear_last();
        } else {
            // set error_get_last() to defined state by forcing an undefined variable error
            set_error_handler(function() { return false; }, 0);
            @$undefinedVariable;
            restore_error_handler();
        }
    }

    protected function handleErrors() {
        $error = error_get_last();
        if (null === $error) {
            return;
        }

        if (preg_match(
            '~^Unterminated comment starting line ([0-9]+)$~',
            $error['message'], $matches
        )) {
            throw new Error('Unterminated comment', (int) $matches[1]);
        }

        if (preg_match(
            '~^Unexpected character in input:  \'(.)\' \(ASCII=([0-9]+)\)~s',
            $error['message'], $matches
        )) {
            throw new Error(sprintf(
                'Unexpected character "%s" (ASCII %d)',
                $matches[1], $matches[2]
            ));
        }

        // PHP cuts error message after null byte, so need special case
        if (preg_match('~^Unexpected character in input:  \'$~', $error['message'])) {
            throw new Error('Unexpected null byte');
        }
    }

    /**
     * Fetches the next token.
     *
     * The available attributes are determined by the 'usedAttributes' option, which can
     * be specified in the constructor. The following attributes are supported:
     *
     *  * 'comments'      => Array of PhpParser\Comment or PhpParser\Comment\Doc instances,
     *                       representing all comments that occurred between the previous
     *                       non-discarded token and the current one.
     *  * 'startLine'     => Line in which the node starts.
     *  * 'endLine'       => Line in which the node ends.
     *  * 'startTokenPos' => Offset into the token array of the first token in the node.
     *  * 'endTokenPos'   => Offset into the token array of the last token in the node.
     *  * 'startFilePos'  => Offset into the code string of the first character that is part of the node.
     *  * 'endFilePos'    => Offset into the code string of the last character that is part of the node
     *
     * @param mixed $value           Variable to store token content in
     * @param mixed $startAttributes Variable to store start attributes in
     * @param mixed $endAttributes   Variable to store end attributes in
     *
     * @return int Token id
     */
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $startAttributes = array();
        $endAttributes   = array();

        while (1) {
            if (isset($this->tokens[++$this->pos])) {
                $token = $this->tokens[$this->pos];
            } else {
                // EOF token with ID 0
                $token = "\0";
            }

            if (isset($this->usedAttributes['startTokenPos'])) {
                $startAttributes['startTokenPos'] = $this->pos;
            }
            if (isset($this->usedAttributes['startFilePos'])) {
                $startAttributes['startFilePos'] = $this->filePos;
            }

            if (is_string($token)) {
                // bug in token_get_all
                if ('b"' === $token) {
                    $value = 'b"';
                    $this->filePos += 2;
                    $id = ord('"');
                } else {
                    $value = $token;
                    $this->filePos += 1;
                    $id = ord($token);
                }

                if (isset($this->usedAttributes['startLine'])) {
                    $startAttributes['startLine'] = $this->line;
                }
                if (isset($this->usedAttributes['endLine'])) {
                    $endAttributes['endLine'] = $this->line;
                }
                if (isset($this->usedAttributes['endTokenPos'])) {
                    $endAttributes['endTokenPos'] = $this->pos;
                }
                if (isset($this->usedAttributes['endFilePos'])) {
                    $endAttributes['endFilePos'] = $this->filePos - 1;
                }

                return $id;
            } else {
                $this->line += substr_count($token[1], "\n");
                $this->filePos += strlen($token[1]);

                if (T_COMMENT === $token[0]) {
                    if (isset($this->usedAttributes['comments'])) {
                        $startAttributes['comments'][] = new Comment($token[1], $token[2]);
                    }
                } elseif (T_DOC_COMMENT === $token[0]) {
                    if (isset($this->usedAttributes['comments'])) {
                        $startAttributes['comments'][] = new Comment\Doc($token[1], $token[2]);
                    }
                } elseif (!isset($this->dropTokens[$token[0]])) {
                    $value = $token[1];

                    if (isset($this->usedAttributes['startLine'])) {
                        $startAttributes['startLine'] = $token[2];
                    }
                    if (isset($this->usedAttributes['endLine'])) {
                        $endAttributes['endLine'] = $this->line;
                    }
                    if (isset($this->usedAttributes['endTokenPos'])) {
                        $endAttributes['endTokenPos'] = $this->pos;
                    }
                    if (isset($this->usedAttributes['endFilePos'])) {
                        $endAttributes['endFilePos'] = $this->filePos - 1;
                    }

                    return $this->tokenMap[$token[0]];
                }
            }
        }

        throw new \RuntimeException('Reached end of lexer loop');
    }

    /**
     * Returns the token array for current code.
     *
     * The token array is in the same format as provided by the
     * token_get_all() function and does not discard tokens (i.e.
     * whitespace and comments are included). The token position
     * attributes are against this token array.
     *
     * @return array Array of tokens in token_get_all() format
     */
    public function getTokens() {
        return $this->tokens;
    }

    /**
     * Handles __halt_compiler() by returning the text after it.
     *
     * @return string Remaining text
     */
    public function handleHaltCompiler() {
        // text after T_HALT_COMPILER, still including ();
        $textAfter = substr($this->code, $this->filePos);

        // ensure that it is followed by ();
        // this simplifies the situation, by not allowing any comments
        // in between of the tokens.
        if (!preg_match('~^\s*\(\s*\)\s*(?:;|\?>\r?\n?)~', $textAfter, $matches)) {
            throw new Error('__HALT_COMPILER must be followed by "();"');
        }

        // prevent the lexer from returning any further tokens
        $this->pos = count($this->tokens);

        // return with (); removed
        return (string) substr($textAfter, strlen($matches[0])); // (string) converts false to ''
    }

    /**
     * Creates the token map.
     *
     * The token map maps the PHP internal token identifiers
     * to the identifiers used by the Parser. Additionally it
     * maps T_OPEN_TAG_WITH_ECHO to T_ECHO and T_CLOSE_TAG to ';'.
     *
     * @return array The token map
     */
    protected function createTokenMap() {
        $tokenMap = array();

        // 256 is the minimum possible token number, as everything below
        // it is an ASCII value
        for ($i = 256; $i < 1000; ++$i) {
            if (T_DOUBLE_COLON === $i) {
                // T_DOUBLE_COLON is equivalent to T_PAAMAYIM_NEKUDOTAYIM
                $tokenMap[$i] = Tokens::T_PAAMAYIM_NEKUDOTAYIM;
            } elseif(T_OPEN_TAG_WITH_ECHO === $i) {
                // T_OPEN_TAG_WITH_ECHO with dropped T_OPEN_TAG results in T_ECHO
                $tokenMap[$i] = Tokens::T_ECHO;
            } elseif(T_CLOSE_TAG === $i) {
                // T_CLOSE_TAG is equivalent to ';'
                $tokenMap[$i] = ord(';');
            } elseif ('UNKNOWN' !== $name = token_name($i)) {
                if ('T_HASHBANG' === $name) {
                    // HHVM uses a special token for #! hashbang lines
                    $tokenMap[$i] = Tokens::T_INLINE_HTML;
                } else if (defined($name = 'PhpParser\Parser\Tokens::' . $name)) {
                    // Other tokens can be mapped directly
                    $tokenMap[$i] = constant($name);
                }
            }
        }

        // HHVM uses a special token for numbers that overflow to double
        if (defined('T_ONUMBER')) {
            $tokenMap[T_ONUMBER] = Tokens::T_DNUMBER;
        }
        // HHVM also has a separate token for the __COMPILER_HALT_OFFSET__ constant
        if (defined('T_COMPILER_HALT_OFFSET')) {
            $tokenMap[T_COMPILER_HALT_OFFSET] = Tokens::T_STRING;
        }

        return $tokenMap;
    }
}
