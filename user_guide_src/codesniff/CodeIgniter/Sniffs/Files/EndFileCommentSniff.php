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
 * @category  Files
 * @author    Nisheeth Barthwal <nisheeth.barthwal@nbaztec.co.in>
 * @author    Thomas Ernest <thomas.ernest@baobaz.com>
 */
class CodeIgniter_Sniffs_Files_EndFileCommentSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

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
        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $fullFilename = $phpcsFile->getFilename();
        $filename = basename($fullFilename);
        $template = array(
            " End of file $filename ",
            " Location: ./{path-to-file}/$filename "
        );
        $templateMatch = array(
            " End of file ".preg_quote($filename, '|')." ",
            " Location: \..*/".preg_quote($filename, '|')." "
        );
        $commentTemplate = "/* End of file $filename */ /* Location: ./{path-to-file}/$filename */";

        $tokens = $phpcsFile->getTokens();
        $currentToken = count($tokens) - 1;
        $hasClosingFileComment = false;
        $isNotAWhitespaceOrAComment = false;
        $commentCount = count($template) - 1;
        while ($currentToken >= 0
            && ! $isNotAWhitespaceOrAComment
            && ! $hasClosingFileComment
        ) {
            $token = $tokens[$currentToken];
            $tokenCode = $token['code'];
            if (T_COMMENT === $tokenCode) {
                $commentString = $this->getCommentContent($token['content']);
                if (1 === preg_match('|'.$templateMatch[$commentCount].'|', $commentString)) {
                    $hasClosingFileComment = ($commentCount-- == 0);
                }
            } else if (T_WHITESPACE === $tokenCode) {
                // Whitespaces are allowed between the closing file comment,
                //other comments and end of file
            } else {
                $isNotAWhitespaceOrAComment = true;
            }
            $currentToken--;
        }

        if ( ! $hasClosingFileComment) {
            $error = 'Require an end of file comment block containing "' . $commentTemplate . '".';
            $phpcsFile->addError($error, count($tokens)-1);
        }
    }//end process()
    
    protected function getCommentContent ($comment)
    {
        if ($this->stringStartsWith($comment, '#')) {
            $comment = substr($comment, 1);
        } else if ($this->stringStartsWith($comment, '//')) {
            $comment = substr($comment, 2);
        } else if ($this->stringStartsWith($comment, '/*')) {
            $comment = substr($comment, 2, strlen($comment) - 2 - 2);
        }
        //$comment = trim($comment);
        return $comment;
    }
    
    /**
     * Binary safe string comparison between $needle and
     * the beginning of $haystack. Returns true if $haystack starts with
     * $needle, false otherwise.
     *
     * @param string $haystack The string to search in.
     * @param string $needle   The string to search for.
     *
     * @return bool true if $haystack starts with $needle, false otherwise.
     */
    protected function stringStartsWith ($haystack, $needle)
    {
        $startsWith = false;
        if (strlen($needle) <= strlen($haystack)) {
            $haystackBeginning = substr($haystack, 0, strlen($needle));
            if (0 === strcmp($haystackBeginning, $needle)) {
                $startsWith = true;
            }
        }
        return $startsWith;
    }//_stringStartsWith()

}//end class