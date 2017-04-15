<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 */

/**
 * CodeIgniter_Sniffs_NamingConventions_ValidFileNameSniff.
 *
 * Tests that the file name matchs the name of the class that it contains.
 *
 * @package   CodeSniff
 * @category  ControlStructures
 * @author    Nisheeth Barthwal <nisheeth.barthwal@nbaztec.co.in>
 */
 class CodeIgniter_Sniffs_ControlStructures_SwitchDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array('PHP');

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_SWITCH);

    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // We can't process SWITCH statements unless we know where they start and end.
        if (isset($tokens[$stackPtr]['scope_opener']) === false
            || isset($tokens[$stackPtr]['scope_closer']) === false
        ) {
            return;
        }

        $switch        = $tokens[$stackPtr];
        $nextCase      = $stackPtr;
        $caseAlignment = ($switch['column'] + 1);
        $caseCount     = 0;
        $foundDefault  = false;

        while (($nextCase = $phpcsFile->findNext(array(T_CASE, T_DEFAULT, T_SWITCH), ($nextCase + 1), $switch['scope_closer'])) !== false) {
            // Skip nested SWITCH statements; they are handled on their own.
            if ($tokens[$nextCase]['code'] === T_SWITCH) {
                $nextCase = $tokens[$nextCase]['scope_closer'];
                continue;
            }

            if ($tokens[$nextCase]['code'] === T_DEFAULT) {
                $type         = 'Default';
                $foundDefault = true;
            } else {
                $type = 'Case';
                $caseCount++;
            }

            if ($tokens[$nextCase]['content'] !== strtolower($tokens[$nextCase]['content'])) {
                $expected = strtolower($tokens[$nextCase]['content']);
                $error    = strtoupper($type).' keyword must be lowercase; expected "%s" but found "%s"';
                $data     = array(
                             $expected,
                             $tokens[$nextCase]['content'],
                            );
                $phpcsFile->addError($error, $nextCase, $type.'NotLower', $data);
            }

            if ($tokens[$nextCase]['column'] !== $caseAlignment) {
                $error = strtoupper($type).' keyword must be indented 1 level from SWITCH keyword';
                $phpcsFile->addError($error, $nextCase, $type.'Indent');
            }

            if ($type === 'Case'
                && ($tokens[($nextCase + 1)]['type'] !== 'T_WHITESPACE'
                || $tokens[($nextCase + 1)]['content'] !== ' ')
            ) {
                $error = 'CASE keyword must be followed by a single space';
                $phpcsFile->addError($error, $nextCase, 'SpacingAfterCase');
            }

            $opener = $tokens[$nextCase]['scope_opener'];
            if ($tokens[($opener - 1)]['type'] === 'T_WHITESPACE') {
                $error = 'There must be no space before the colon in a '.strtoupper($type).' statement';
                $phpcsFile->addError($error, $nextCase, 'SpaceBeforeColon'.$type);
            }

            $nextBreak = $tokens[$nextCase]['scope_closer'];
            if ($tokens[$nextBreak]['code'] === T_BREAK || $tokens[$nextBreak]['code'] === T_RETURN) {
                if ($tokens[$nextBreak]['scope_condition'] === $nextCase) {
                    // Only need to check a couple of things once, even if the
                    // break is shared between multiple case statements, or even
                    // the default case.
                    if ($tokens[$nextBreak]['column'] !== $caseAlignment + 1) {
                        $error = 'Case breaking statement must be indented 2 levels from SWITCH keyword';
                        $phpcsFile->addError($error, $nextBreak, 'BreakIndent');
                    }

                    $breakLine = $tokens[$nextBreak]['line'];
                    $prevLine  = 0;
                    for ($i = ($nextBreak - 1); $i > $stackPtr; $i--) {
                        if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                            $prevLine = $tokens[$i]['line'];
                            break;
                        }
                    }

                    if ($prevLine !== ($breakLine - 1)) {
                        $error = 'Blank lines are not allowed before case breaking statements';
                        $phpcsFile->addError($error, $nextBreak, 'SpacingBeforeBreak');
                    }

                    $breakLine = $tokens[$nextBreak]['line'];
                    $nextLine  = $tokens[$tokens[$stackPtr]['scope_closer']]['line'];
                    $semicolon = $phpcsFile->findNext(T_SEMICOLON, $nextBreak);
                    for ($i = ($semicolon + 1); $i < $tokens[$stackPtr]['scope_closer']; $i++) {
                        if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                            $nextLine = $tokens[$i]['line'];
                            break;
                        }
                    }

                    if ($type === 'Case') {
                        // Ensure the BREAK statement is followed by
                        // a single blank line, or the end switch brace.
                        if ($nextLine !== ($breakLine + 2) && $i !== $tokens[$stackPtr]['scope_closer']) {
                            $error = 'Case breaking statements must be followed by a single blank line';
                            $phpcsFile->addError($error, $nextBreak, 'SpacingAfterBreak');
                        }
                    } else {
                        // Ensure the BREAK statement is not followed by a blank line.
                        if ($nextLine !== ($breakLine + 1)) {
                            $error = 'Blank lines are not allowed after the DEFAULT case\'s breaking statement';
                            $phpcsFile->addError($error, $nextBreak, 'SpacingAfterDefaultBreak');
                        }
                    }

                    $caseLine = $tokens[$nextCase]['line'];
                    $nextLine = $tokens[$nextBreak]['line'];
                    for ($i = ($opener + 1); $i < $nextBreak; $i++) {
                        if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                            $nextLine = $tokens[$i]['line'];
                            break;
                        }
                    }

                    if ($nextLine !== ($caseLine + 1)) {
                        $error = 'Blank lines are not allowed after '.strtoupper($type).' statements';
                        $phpcsFile->addError($error, $nextCase, 'SpacingAfter'.$type);
                    }
                }//end if

                if ($tokens[$nextBreak]['code'] === T_BREAK) {
                    if ($type === 'Case') {
                        // Ensure empty CASE statements are not allowed.
                        // They must have some code content in them. A comment is not enough.
                        // But count RETURN statements as valid content if they also
                        // happen to close the CASE statement.
                        $foundContent = false;
                        for ($i = ($tokens[$nextCase]['scope_opener'] + 1); $i < $nextBreak; $i++) {
                            if ($tokens[$i]['code'] === T_CASE) {
                                $i = $tokens[$i]['scope_opener'];
                                continue;
                            }

                            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                                $foundContent = true;
                                break;
                            }
                        }

                        if ($foundContent === false) {
                            $error = 'Empty CASE statements are not allowed';
                            $phpcsFile->addError($error, $nextCase, 'EmptyCase');
                        }
                    } else {
                        // Ensure empty DEFAULT statements are not allowed.
                        // They must (at least) have a comment describing why
                        // the default case is being ignored.
                        $foundContent = false;
                        for ($i = ($tokens[$nextCase]['scope_opener'] + 1); $i < $nextBreak; $i++) {
                            if ($tokens[$i]['type'] !== 'T_WHITESPACE') {
                                $foundContent = true;
                                break;
                            }
                        }

                        if ($foundContent === false) {
                            $error = 'Comment required for empty DEFAULT case';
                            $phpcsFile->addError($error, $nextCase, 'EmptyDefault');
                        }
                    }//end if
                }//end if
            } else if ($type === 'Default') {
                $error = 'DEFAULT case must have a breaking statement';
                $phpcsFile->addError($error, $nextCase, 'DefaultNoBreak');
            }//end if
        }//end while

        if ($tokens[$switch['scope_closer']]['column'] !== $switch['column']) {
            $error = 'Closing brace of SWITCH statement must be aligned with SWITCH keyword';
            $phpcsFile->addError($error, $switch['scope_closer'], 'CloseBraceAlign');
        }

        if ($caseCount === 0) {
            $error = 'SWITCH statements must contain at least one CASE statement';
            $phpcsFile->addError($error, $stackPtr, 'MissingCase');
        }

    }//end process()

}//end class