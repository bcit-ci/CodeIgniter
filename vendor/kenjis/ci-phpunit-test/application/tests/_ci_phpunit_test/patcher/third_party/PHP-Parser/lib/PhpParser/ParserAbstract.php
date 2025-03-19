<?php

namespace PhpParser;

/*
 * This parser is based on a skeleton written by Moriyoshi Koizumi, which in
 * turn is based on work by Masato Bito.
 */
use PhpParser\Node\Name;

abstract class ParserAbstract implements Parser
{
    const SYMBOL_NONE = -1;

    /*
     * The following members will be filled with generated parsing data:
     */

    /** @var int Size of $tokenToSymbol map */
    protected $tokenToSymbolMapSize;
    /** @var int Size of $action table */
    protected $actionTableSize;
    /** @var int Size of $goto table */
    protected $gotoTableSize;

    /** @var int Symbol number signifying an invalid token */
    protected $invalidSymbol;
    /** @var int Symbol number of error recovery token */
    protected $errorSymbol;
    /** @var int Action number signifying default action */
    protected $defaultAction;
    /** @var int Rule number signifying that an unexpected token was encountered */
    protected $unexpectedTokenRule;

    protected $YY2TBLSTATE;
    protected $YYNLSTATES;

    /** @var array Map of lexer tokens to internal symbols */
    protected $tokenToSymbol;
    /** @var array Map of symbols to their names */
    protected $symbolToName;
    /** @var array Names of the production rules (only necessary for debugging) */
    protected $productions;

    /** @var array Map of states to a displacement into the $action table. The corresponding action for this
     *             state/symbol pair is $action[$actionBase[$state] + $symbol]. If $actionBase[$state] is 0, the
                   action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected $actionBase;
    /** @var array Table of actions. Indexed according to $actionBase comment. */
    protected $action;
    /** @var array Table indexed analogously to $action. If $actionCheck[$actionBase[$state] + $symbol] != $symbol
     *             then the action is defaulted, i.e. $actionDefault[$state] should be used instead. */
    protected $actionCheck;
    /** @var array Map of states to their default action */
    protected $actionDefault;

    /** @var array Map of non-terminals to a displacement into the $goto table. The corresponding goto state for this
     *             non-terminal/state pair is $goto[$gotoBase[$nonTerminal] + $state] (unless defaulted) */
    protected $gotoBase;
    /** @var array Table of states to goto after reduction. Indexed according to $gotoBase comment. */
    protected $goto;
    /** @var array Table indexed analogously to $goto. If $gotoCheck[$gotoBase[$nonTerminal] + $state] != $nonTerminal
     *             then the goto state is defaulted, i.e. $gotoDefault[$nonTerminal] should be used. */
    protected $gotoCheck;
    /** @var array Map of non-terminals to the default state to goto after their reduction */
    protected $gotoDefault;

    /** @var array Map of rules to the non-terminal on their left-hand side, i.e. the non-terminal to use for
     *             determining the state to goto after reduction. */
    protected $ruleToNonTerminal;
    /** @var array Map of rules to the length of their right-hand side, which is the number of elements that have to
     *             be popped from the stack(s) on reduction. */
    protected $ruleToLength;

    /*
     * The following members are part of the parser state:
     */

    /** @var Lexer Lexer that is used when parsing */
    protected $lexer;
    /** @var mixed Temporary value containing the result of last semantic action (reduction) */
    protected $semValue;
    /** @var int Position in stacks (state stack, semantic value stack, attribute stack) */
    protected $stackPos;
    /** @var array Semantic value stack (contains values of tokens and semantic action results) */
    protected $semStack;
    /** @var array[] Start attribute stack */
    protected $startAttributeStack;
    /** @var array End attributes of last *shifted* token */
    protected $endAttributes;

    /** @var bool Whether to throw on first error */
    protected $throwOnError;
    /** @var Error[] Errors collected during last parse */
    protected $errors;

    /**
     * Creates a parser instance.
     *
     * @param Lexer $lexer A lexer
     * @param array $options Options array. The boolean 'throwOnError' option determines whether an exception should be
     *                       thrown on first error, or if the parser should try to continue parsing the remaining code
     *                       and build a partial AST.
     */
    public function __construct(Lexer $lexer, array $options = array()) {
        $this->lexer = $lexer;
        $this->errors = array();
        $this->throwOnError = isset($options['throwOnError']) ? $options['throwOnError'] : true;
    }

    /**
     * Get array of errors that occurred during the last parse.
     *
     * This method may only return multiple errors if the 'throwOnError' option is disabled.
     *
     * @return Error[]
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Parses PHP code into a node tree.
     *
     * @param string $code The source code to parse
     *
     * @return Node[]|null Array of statements (or null if the 'throwOnError' option is disabled and the parser was
     *                     unable to recover from an error).
     */
    public function parse($code) {
        $this->lexer->startLexing($code);
        $this->errors = array();

        // We start off with no lookahead-token
        $symbol = self::SYMBOL_NONE;

        // The attributes for a node are taken from the first and last token of the node.
        // From the first token only the startAttributes are taken and from the last only
        // the endAttributes. Both are merged using the array union operator (+).
        $startAttributes = '*POISON';
        $endAttributes = '*POISON';
        $this->endAttributes = $endAttributes;

        // In order to figure out the attributes for the starting token, we have to keep
        // them in a stack
        $this->startAttributeStack = array();

        // Start off in the initial state and keep a stack of previous states
        $state = 0;
        $stateStack = array($state);

        // Semantic value stack (contains values of tokens and semantic action results)
        $this->semStack = array();

        // Current position in the stack(s)
        $this->stackPos = 0;

        $errorState = 0;

        for (;;) {
            //$this->traceNewState($state, $symbol);

            if ($this->actionBase[$state] == 0) {
                $rule = $this->actionDefault[$state];
            } else {
                if ($symbol === self::SYMBOL_NONE) {
                    // Fetch the next token id from the lexer and fetch additional info by-ref.
                    // The end attributes are fetched into a temporary variable and only set once the token is really
                    // shifted (not during read). Otherwise you would sometimes get off-by-one errors, when a rule is
                    // reduced after a token was read but not yet shifted.
                    $tokenId = $this->lexer->getNextToken($tokenValue, $startAttributes, $endAttributes);

                    // map the lexer token id to the internally used symbols
                    $symbol = $tokenId >= 0 && $tokenId < $this->tokenToSymbolMapSize
                        ? $this->tokenToSymbol[$tokenId]
                        : $this->invalidSymbol;

                    if ($symbol === $this->invalidSymbol) {
                        throw new \RangeException(sprintf(
                            'The lexer returned an invalid token (id=%d, value=%s)',
                            $tokenId, $tokenValue
                        ));
                    }

                    // This is necessary to assign some meaningful attributes to /* empty */ productions. They'll get
                    // the attributes of the next token, even though they don't contain it themselves.
                    $this->startAttributeStack[$this->stackPos+1] = $startAttributes;

                    //$this->traceRead($symbol);
                }

                $idx = $this->actionBase[$state] + $symbol;
                if ((($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] == $symbol)
                     || ($state < $this->YY2TBLSTATE
                         && ($idx = $this->actionBase[$state + $this->YYNLSTATES] + $symbol) >= 0
                         && $idx < $this->actionTableSize && $this->actionCheck[$idx] == $symbol))
                    && ($action = $this->action[$idx]) != $this->defaultAction) {
                    /*
                     * >= YYNLSTATES: shift and reduce
                     * > 0: shift
                     * = 0: accept
                     * < 0: reduce
                     * = -YYUNEXPECTED: error
                     */
                    if ($action > 0) {
                        /* shift */
                        //$this->traceShift($symbol);

                        ++$this->stackPos;
                        $stateStack[$this->stackPos] = $state = $action;
                        $this->semStack[$this->stackPos] = $tokenValue;
                        $this->startAttributeStack[$this->stackPos] = $startAttributes;
                        $this->endAttributes = $endAttributes;
                        $symbol = self::SYMBOL_NONE;

                        if ($errorState) {
                            --$errorState;
                        }

                        if ($action < $this->YYNLSTATES) {
                            continue;
                        }

                        /* $yyn >= YYNLSTATES means shift-and-reduce */
                        $rule = $action - $this->YYNLSTATES;
                    } else {
                        $rule = -$action;
                    }
                } else {
                    $rule = $this->actionDefault[$state];
                }
            }

            for (;;) {
                if ($rule === 0) {
                    /* accept */
                    //$this->traceAccept();
                    return $this->semValue;
                } elseif ($rule !== $this->unexpectedTokenRule) {
                    /* reduce */
                    //$this->traceReduce($rule);

                    try {
                        $this->{'reduceRule' . $rule}();
                    } catch (Error $e) {
                        if (-1 === $e->getStartLine() && isset($startAttributes['startLine'])) {
                            $e->setStartLine($startAttributes['startLine']);
                        }

                        $this->errors[] = $e;
                        if ($this->throwOnError) {
                            throw $e;
                        } else {
                            // Currently can't recover from "special" errors
                            return null;
                        }
                    }

                    /* Goto - shift nonterminal */
                    $this->stackPos -= $this->ruleToLength[$rule];
                    $nonTerminal = $this->ruleToNonTerminal[$rule];
                    $idx = $this->gotoBase[$nonTerminal] + $stateStack[$this->stackPos];
                    if ($idx >= 0 && $idx < $this->gotoTableSize && $this->gotoCheck[$idx] == $nonTerminal) {
                        $state = $this->goto[$idx];
                    } else {
                        $state = $this->gotoDefault[$nonTerminal];
                    }

                    ++$this->stackPos;
                    $stateStack[$this->stackPos]     = $state;
                    $this->semStack[$this->stackPos] = $this->semValue;
                } else {
                    /* error */
                    switch ($errorState) {
                        case 0:
                            $msg = $this->getErrorMessage($symbol, $state);
                            $error = new Error($msg, $startAttributes + $endAttributes);
                            $this->errors[] = $error;
                            if ($this->throwOnError) {
                                throw $error;
                            }
                            // Break missing intentionally
                        case 1:
                        case 2:
                            $errorState = 3;

                            // Pop until error-expecting state uncovered
                            while (!(
                                (($idx = $this->actionBase[$state] + $this->errorSymbol) >= 0
                                    && $idx < $this->actionTableSize && $this->actionCheck[$idx] == $this->errorSymbol)
                                || ($state < $this->YY2TBLSTATE
                                    && ($idx = $this->actionBase[$state + $this->YYNLSTATES] + $this->errorSymbol) >= 0
                                    && $idx < $this->actionTableSize && $this->actionCheck[$idx] == $this->errorSymbol)
                            ) || ($action = $this->action[$idx]) == $this->defaultAction) { // Not totally sure about this
                                if ($this->stackPos <= 0) {
                                    // Could not recover from error
                                    return null;
                                }
                                $state = $stateStack[--$this->stackPos];
                                //$this->tracePop($state);
                            }

                            //$this->traceShift($this->errorSymbol);
                            $stateStack[++$this->stackPos] = $state = $action;
                            break;

                        case 3:
                            if ($symbol === 0) {
                                // Reached EOF without recovering from error
                                return null;
                            }

                            //$this->traceDiscard($symbol);
                            $symbol = self::SYMBOL_NONE;
                            break 2;
                    }
                }

                if ($state < $this->YYNLSTATES) {
                    break;
                }

                /* >= YYNLSTATES means shift-and-reduce */
                $rule = $state - $this->YYNLSTATES;
            }
        }

        throw new \RuntimeException('Reached end of parser loop');
    }

    protected function getErrorMessage($symbol, $state) {
        $expectedString = '';
        if ($expected = $this->getExpectedTokens($state)) {
            $expectedString = ', expecting ' . implode(' or ', $expected);
        }

        return 'Syntax error, unexpected ' . $this->symbolToName[$symbol] . $expectedString;
    }

    protected function getExpectedTokens($state) {
        $expected = array();

        $base = $this->actionBase[$state];
        foreach ($this->symbolToName as $symbol => $name) {
            $idx = $base + $symbol;
            if ($idx >= 0 && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
                || $state < $this->YY2TBLSTATE
                && ($idx = $this->actionBase[$state + $this->YYNLSTATES] + $symbol) >= 0
                && $idx < $this->actionTableSize && $this->actionCheck[$idx] === $symbol
            ) {
                if ($this->action[$idx] != $this->unexpectedTokenRule) {
                    if (count($expected) == 4) {
                        /* Too many expected tokens */
                        return array();
                    }

                    $expected[] = $name;
                }
            }
        }

        return $expected;
    }

    /*
     * Tracing functions used for debugging the parser.
     */

    /*
    protected function traceNewState($state, $symbol) {
        echo '% State ' . $state
            . ', Lookahead ' . ($symbol == self::SYMBOL_NONE ? '--none--' : $this->symbolToName[$symbol]) . "\n";
    }

    protected function traceRead($symbol) {
        echo '% Reading ' . $this->symbolToName[$symbol] . "\n";
    }

    protected function traceShift($symbol) {
        echo '% Shift ' . $this->symbolToName[$symbol] . "\n";
    }

    protected function traceAccept() {
        echo "% Accepted.\n";
    }

    protected function traceReduce($n) {
        echo '% Reduce by (' . $n . ') ' . $this->productions[$n] . "\n";
    }

    protected function tracePop($state) {
        echo '% Recovering, uncovered state ' . $state . "\n";
    }

    protected function traceDiscard($symbol) {
        echo '% Discard ' . $this->symbolToName[$symbol] . "\n";
    }
    */

    /*
     * Helper functions invoked by semantic actions
     */

    /**
     * Moves statements of semicolon-style namespaces into $ns->stmts and checks various error conditions.
     *
     * @param Node[] $stmts
     * @return Node[]
     */
    protected function handleNamespaces(array $stmts) {
        $style = $this->getNamespacingStyle($stmts);
        if (null === $style) {
            // not namespaced, nothing to do
            return $stmts;
        } elseif ('brace' === $style) {
            // For braced namespaces we only have to check that there are no invalid statements between the namespaces
            $afterFirstNamespace = false;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Namespace_) {
                    $afterFirstNamespace = true;
                } elseif (!$stmt instanceof Node\Stmt\HaltCompiler && $afterFirstNamespace) {
                    throw new Error('No code may exist outside of namespace {}', $stmt->getLine());
                }
            }
            return $stmts;
        } else {
            // For semicolon namespaces we have to move the statements after a namespace declaration into ->stmts
            $resultStmts = array();
            $targetStmts =& $resultStmts;
            foreach ($stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Namespace_) {
                    $stmt->stmts = array();
                    $targetStmts =& $stmt->stmts;
                    $resultStmts[] = $stmt;
                } elseif ($stmt instanceof Node\Stmt\HaltCompiler) {
                    // __halt_compiler() is not moved into the namespace
                    $resultStmts[] = $stmt;
                } else {
                    $targetStmts[] = $stmt;
                }
            }
            return $resultStmts;
        }
    }

    private function getNamespacingStyle(array $stmts) {
        $style = null;
        $hasNotAllowedStmts = false;
        foreach ($stmts as $i => $stmt) {
            if ($stmt instanceof Node\Stmt\Namespace_) {
                $currentStyle = null === $stmt->stmts ? 'semicolon' : 'brace';
                if (null === $style) {
                    $style = $currentStyle;
                    if ($hasNotAllowedStmts) {
                        throw new Error('Namespace declaration statement has to be the very first statement in the script', $stmt->getLine());
                    }
                } elseif ($style !== $currentStyle) {
                    throw new Error('Cannot mix bracketed namespace declarations with unbracketed namespace declarations', $stmt->getLine());
                }
                continue;
            }

            /* declare() and __halt_compiler() can be used before a namespace declaration */
            if ($stmt instanceof Node\Stmt\Declare_ || $stmt instanceof Node\Stmt\HaltCompiler) {
                continue;
            }

            /* There may be a hashbang line at the very start of the file */
            if ($i == 0 && $stmt instanceof Node\Stmt\InlineHTML && preg_match('/\A#!.*\r?\n\z/', $stmt->value)) {
                continue;
            }

            /* Everything else if forbidden before namespace declarations */
            $hasNotAllowedStmts = true;
        }
        return $style;
    }

    protected function handleScalarTypes(Name $name) {
        $scalarTypes = [
            'bool'   => true,
            'int'    => true,
            'float'  => true,
            'string' => true,
        ];

        if (!$name->isUnqualified()) {
            return $name;
        }

        $lowerName = strtolower($name->toString());
        return isset($scalarTypes[$lowerName]) ? $lowerName : $name;
    }
}
