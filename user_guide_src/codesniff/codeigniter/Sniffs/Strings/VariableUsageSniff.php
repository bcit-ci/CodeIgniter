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
 * @category  Strings
 * @author    Nisheeth Barthwal <nisheeth.barthwal@nbaztec.co.in>
 * @author    Thomas Ernest <thomas.ernest@baobaz.com>
 */
class CodeIgniter_Sniffs_Strings_VariableUsageSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        /*
        return array(
            T_DOUBLE_QUOTED_STRING,
            T_CONSTANT_ENCAPSED_STRING,
        );
        */
        return array();
    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $string = $tokens[$stackPtr]['content'];
        // makes sure that it is about a double quote string,
        // since variables are not parsed out of double quoted string
        $openDblQtStr = substr($string, 0, 1);
        if (0 === strcmp($openDblQtStr, '"')) {
            $this->processDoubleQuotedString($phpcsFile, $stackPtr, $string);
        } else if (0 === strcmp($openDblQtStr, "'")) {
            $this->processSingleQuotedString($phpcsFile, $stackPtr, $string);
        }
    }//end process()


    /**
     * Processes this test, when the token encountered is a double-quoted string.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The current file being scanned.
     * @param int                  $stackPtr    The position of the current token
     *                                          in the stack passed in $tokens.
     * @param string               $dblQtString The double-quoted string content,
     *                                          i.e. without quotes.
     *
     * @return void
     */
    protected function processDoubleQuotedString (PHP_CodeSniffer_File $phpcsFile, $stackPtr, $dblQtString)
    {
        $variableFound = FALSE;
        $strTokens = token_get_all('<?php '.$dblQtString);
        $strPtr = 1; // skip php opening tag added by ourselves
        $requireDblQuotes = FALSE;
        while ($strPtr < count($strTokens)) {
            $strToken = $strTokens[$strPtr];
            if (is_array($strToken)) {
                if (in_array($strToken[0], array(T_DOLLAR_OPEN_CURLY_BRACES, T_CURLY_OPEN))) {
                    $strPtr++;
                    try {
                        $this->_parseVariable($strTokens, $strPtr);
                    } catch (Exception $err) {
                        $error = 'There is no variable, object nor array between curly braces. Please use the escape char for $ or {.';
                        $phpcsFile->addError($error, $stackPtr);
                    }
                    $variableFound = TRUE;
                    if ('}' !== $strTokens[$strPtr]) {
                        $error = 'There is no matching closing curly brace.';
                        $phpcsFile->addError($error, $stackPtr);
                    }
                    // don't move forward, since it will be done in the main loop
                    // $strPtr++;
                } else if (T_VARIABLE === $strToken[0]) {
                    $variableFound = TRUE;
                    $error = "Variable {$strToken[1]} in double-quoted strings should be enclosed with curly braces. Please consider {{$strToken[1]}}";
                    $phpcsFile->addError($error, $stackPtr);
                }
            }
            $strPtr++;
        }
        return $variableFound;
    }//end processDoubleQuotedString()

    /**
     * Processes this test, when the token encountered is a single-quoted string.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The current file being scanned.
     * @param int                  $stackPtr    The position of the current token
     *                                          in the stack passed in $tokens.
     * @param string               $sglQtString The single-quoted string content,
     *                                          i.e. without quotes.
     *
     * @return void
     */
    protected function processSingleQuotedString (PHP_CodeSniffer_File $phpcsFile, $stackPtr, $sglQtString)
    {
        $variableFound = FALSE;
        $strTokens = token_get_all('<?php '.$sglQtString);
        $strPtr = 1; // skip php opening tag added by ourselves
        while ($strPtr < count($strTokens)) {
            $strToken = $strTokens[$strPtr];
            if (is_array($strToken)) {
                if (T_VARIABLE === $strToken[0]) {
                    $error = "Variables like {$strToken[1]} should be in double-quoted strings only.";
                    $phpcsFile->addError($error, $stackPtr);
                }
            }
            $strPtr++;
        }
        return $variableFound;
    }//end processSingleQuotedString()

    /**
     * Grammar rule to parse the use of a variable. Please notice that it
     * doesn't manage the leading $.
     *
     * _parseVariable ::= <variable>
     *     | <variable>_parseObjectAttribute()
     *     | <variable>_parseArrayIndexes()
     *
     * @exception Exception raised if $strTokens starting from $strPtr
     *                      doesn't matched the rule.
     *
     * @param array $strTokens Tokens to parse.
     * @param int $strPtr    Pointer to the token where parsing starts.
     *
     * @throws Exception
     * @return array The attribute name associated to index 'var', an array with
     * indexes 'obj' and 'attr' or an array with indexes 'arr' and 'idx'.
     */
    private function _parseVariable ($strTokens, &$strPtr)
    {
        if ( ! in_array($strTokens[$strPtr][0], array(T_VARIABLE, T_STRING_VARNAME))) {
            throw new Exception ('Expected variable name.');
        }
        $var = $strTokens[$strPtr][1];
        $strPtr++;
        $startStrPtr = $strPtr;
        try {
            $attr = $this->_parseObjectAttribute($strTokens, $strPtr);
            return array ('obj' => $var, 'attr' => $attr);
        } catch (Exception $err) {
            if ($strPtr !== $startStrPtr) {
                throw $err;
            }
        }
        try {
            $idx = $this->_parseArrayIndexes($strTokens, $strPtr);
            return array ('arr' => $var, 'idx' => $idx);
        } catch (Exception $err) {
            if ($strPtr !== $startStrPtr) {
                throw $err;
            }
        }
        return array ('var' => $var);
    }//end _parseVariable()


    /**
     * Grammar rule to parse the use of an object attribute.
     *
     * _parseObjectAttribute ::= -><attribute>
     *     | -><attribute>_parseObjectAttribute()
     *     | -><attribute>_parseArrayIndexes()
     *
     * @exception Exception raised if $strTokens starting from $strPtr
     *                      doesn't matched the rule.
     *
     * @param array $strTokens Tokens to parse.
     * @param int $strPtr    Pointer to the token where parsing starts.
     *
     * @throws Exception
     * @return mixed The attribute name as a string, an array with indexes
     * 'obj' and 'attr' or an array with indexes 'arr' and 'idx'.
     */
    private function _parseObjectAttribute ($strTokens, &$strPtr)
    {
        if (T_OBJECT_OPERATOR !== $strTokens[$strPtr][0]) {
            throw new Exception ('Expected ->.');
        }
        $strPtr++;
        if (T_STRING !== $strTokens[$strPtr][0]) {
            throw new Exception ('Expected an object attribute.');
        }
        $attr = $strTokens[$strPtr][1];
        $strPtr++;
        $startStrPtr = $strPtr;
        try {
            $sub_attr = $this->_parseObjectAttribute($strTokens, $strPtr);
            return array ('obj' => $attr, 'attr' => $sub_attr);
        } catch (Exception $err) {
            if ($strPtr !== $startStrPtr) {
                throw $err;
            }
        }
        try {
            $idx = $this->_parseArrayIndexes($strTokens, $strPtr);
            return array ('arr' => $attr, 'idx' => $idx);
        } catch (Exception $err) {
            if ($strPtr !== $startStrPtr) {
                throw $err;
            }
        }
        return $attr;
    }//end _parseObjectAttribute()


    /**
     * Grammar rule to parse the use of one or more array indexes.
     *
     * _parseArrayIndexes ::= _parseArrayIndex()+
     *
     * @exception Exception raised if $strTokens starting from $strPtr
     *                      doesn't matched the rule.
     *
     * @param array $strTokens Tokens to parse.
     * @param int $strPtr    Pointer to the token where parsing starts.
     *
     * @throws Exception
     * @return array Indexes in the same order as in the string.
     */
    private function _parseArrayIndexes ($strTokens, &$strPtr)
    {
        $indexes = array($this->_parseArrayIndex($strTokens, $strPtr));
        try {
            while (1) {
                $startStrPtr = $strPtr;
                $indexes [] = $this->_parseArrayIndex($strTokens, $strPtr);
            }
        } catch (Exception $err) {
            if (0 !== ($strPtr - $startStrPtr)) {
                throw $err;
            }
            return $indexes;
        }
    }//end _parseArrayIndexes()


    /**
     * Grammar rule to parse the use of array index.
     *
     * _parseArrayIndex ::= [<index>]
     *
     * @exception Exception raised if $strTokens starting from $strPtr
     *                      doesn't matched the rule.
     *
     * @param array $strTokens Tokens to parse.
     * @param int $strPtr    Pointer to the token where parsing starts.
     *
     * @throws Exception
     * @return string Index between the 2 square brackets
     */
    private function _parseArrayIndex ($strTokens, &$strPtr)
    {
        if ('[' !== $strTokens[$strPtr]) {
            throw new Exception ('Expected [.');
        }
        $strPtr++;
        if (! in_array($strTokens[$strPtr][0], array(T_CONSTANT_ENCAPSED_STRING, T_LNUMBER))) {
            throw new Exception ('Expected an array index.');
        }
        $index = $strTokens[$strPtr][1];
        $strPtr++;
        if (']' !== $strTokens[$strPtr]) {
            throw new Exception ('Expected ].');
        }
        $strPtr++;
        return $index;
    }//end _parseArrayIndex()

}//end class
