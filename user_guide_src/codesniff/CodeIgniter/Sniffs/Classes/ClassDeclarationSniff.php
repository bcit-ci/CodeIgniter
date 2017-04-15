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
 * @category  Classes
 * @author    Nisheeth Barthwal <nisheeth.barthwal@nbaztec.co.in>
 */
class CodeIgniter_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    public $indent = 4;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param integer              $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens    = $phpcsFile->getTokens();
        $errorData = array($tokens[$stackPtr]['content']);

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            $error = 'Possible parse error: %s missing opening or closing brace';
            $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $errorData);
            return;
        }

        $curlyBrace  = $tokens[$stackPtr]['scope_opener'];
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
        $classLine   = $tokens[$lastContent]['line'];
        $braceLine   = $tokens[$curlyBrace]['line'];
        if ($braceLine !== $classLine) {
            $error = 'Opening brace of a %s must be on the same line as the definition';
            $phpcsFile->addError($error, $curlyBrace, 'OpenBraceWrongLine', $errorData);
            return;
        }

        if ($tokens[($curlyBrace + 1)]['content'] !== $phpcsFile->eolChar) {
            $error = 'Opening %s brace must be on a line by itself';
            $phpcsFile->addError($error, $curlyBrace, 'OpenBraceNotAlone', $errorData);
        }

        if ($tokens[($curlyBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($curlyBrace - 1)]['content'];
            if ($prevContent === $phpcsFile->eolChar) {
                $spaces = 0;
            } else {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
            }

            $expected = 1; //($tokens[$stackPtr]['level'] * $this->indent);
            if ($spaces !== $expected) {
                $error = 'Expected %s spaces before opening brace; %s found';
                $data  = array(
                          $expected,
                          $spaces,
                         );
                $phpcsFile->addError($error, $curlyBrace, 'SpaceBeforeBrace', $data);
            }
        } else {
            $error = 'Expected %s spaces before opening brace; %s found';
            $data  = array(
                      1,
                      0,
                     );
            $phpcsFile->addError($error, $curlyBrace, 'SpaceBeforeBrace', $data);
        }

    }//end process()

}//end class
