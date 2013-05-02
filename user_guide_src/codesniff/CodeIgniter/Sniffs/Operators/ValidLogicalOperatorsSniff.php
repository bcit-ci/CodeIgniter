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
 * @category  Operators
 * @author    Nisheeth Barthwal <nisheeth.barthwal@nbaztec.co.in>
 */
class CodeIgniter_Sniffs_Operators_ValidLogicalOperatorsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_LOGICAL_AND,
                T_LOGICAL_OR,
                T_BOOLEAN_OR,
                T_BOOLEAN_NOT,
               );

    }//end register()

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $replacements = array(
                         'and' => '&&',
                         '||'  => 'OR',
                        );

        if ($tokens[$stackPtr]['code'] === T_BOOLEAN_NOT) {
            if($tokens[$stackPtr-1]['code'] !== T_WHITESPACE || $tokens[$stackPtr+1]['code'] !== T_WHITESPACE) {
                $error = 'Must have spaces surrounding "%s"';
                $data  = array($tokens[$stackPtr]['content']);
                $phpcsFile->addError($error, $stackPtr, 'BadSpaces', $data);
            }
        }
        
        $operator = strtolower($tokens[$stackPtr]['content']);
        if (strtoupper($tokens[$stackPtr]['content']) !== $tokens[$stackPtr]['content']) {
            $error = 'Logical operator "%s" must be in uppercase; use "%s" instead';
            $data  = array(
                      $operator,
                      strtoupper($operator),
                     );
            $phpcsFile->addError($error, $stackPtr, 'NotAllowed', $data);
        }
        
        if (isset($replacements[$operator]) === false) {
            return;
        }

        $error = 'Operator "%s" is prohibited; use "%s" instead';
        $data  = array(
                  $tokens[$stackPtr]['content'] === '||'? 'T_BOOLEAN_OR' : $tokens[$stackPtr]['content'], // PhpStorm has a bug where it doesn't raise error if "||" is present in the message
                  $replacements[$operator],
                 );
        $phpcsFile->addWarning($error, $stackPtr, 'NotAllowed', $data);

    }//end process()

}//end class