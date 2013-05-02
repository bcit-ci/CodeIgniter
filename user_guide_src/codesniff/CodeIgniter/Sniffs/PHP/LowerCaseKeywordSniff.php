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
 * @category  PHP
 * @author    Nisheeth Barthwal <nisheeth.barthwal@nbaztec.co.in>
 */
class CodeIgniter_Sniffs_PHP_LowerCaseKeywordSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_HALT_COMPILER,
                T_ABSTRACT,
                T_ARRAY,
                T_AS,
                T_BREAK,
                T_CALLABLE,
                T_CASE,
                T_CATCH,
                T_CLASS,
                T_CLONE,
                T_CONST,
                T_CONTINUE,
                T_DECLARE,
                T_DEFAULT,
                T_DO,
                T_ECHO,
                T_ELSE,
                T_ELSEIF,
                T_EMPTY,
                T_ENDDECLARE,
                T_ENDFOR,
                T_ENDFOREACH,
                T_ENDIF,
                T_ENDSWITCH,
                T_ENDWHILE,
                T_EVAL,
                T_EXIT,
                T_EXTENDS,
                T_FINAL,
                T_FINALLY,
                T_FOR,
                T_FOREACH,
                T_FUNCTION,
                T_GLOBAL,
                T_GOTO,
                T_IF,
                T_IMPLEMENTS,
                T_INCLUDE,
                T_INCLUDE_ONCE,
                T_INSTANCEOF,
                T_INSTEADOF,
                T_INTERFACE,
                T_ISSET,
                T_LIST,
                // T_LOGICAL_AND,
                // T_LOGICAL_OR,
                // T_LOGICAL_XOR,
                T_NAMESPACE,
                T_NEW,
                T_PRINT,
                T_PRIVATE,
                T_PROTECTED,
                T_PUBLIC,
                T_REQUIRE,
                T_REQUIRE_ONCE,
                T_RETURN,
                T_STATIC,
                T_SWITCH,
                T_THROW,
                T_TRAIT,
                T_TRY,
                T_UNSET,
                T_USE,
                T_VAR,
                T_WHILE,
               );

    }//end register()

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $keyword = $tokens[$stackPtr]['content'];
        if (strtolower($keyword) !== $keyword) {
            $error = 'PHP keywords must be lowercase; expected "%s" but found "%s"';
            $data  = array(
                      strtolower($keyword),
                      $keyword,
                     );
            $phpcsFile->addError($error, $stackPtr, 'Found', $data);
        }
    }//end process()

}//end class
