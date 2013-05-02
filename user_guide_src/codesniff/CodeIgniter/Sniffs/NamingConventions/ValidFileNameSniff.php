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
 * @category  NamingConventions
 * @author    Nisheeth Barthwal <nisheeth.barthwal@nbaztec.co.in>
 * @author    Thomas Ernest <thomas.ernest@baobaz.com>
 */
class CodeIgniter_Sniffs_NamingConventions_ValidFileNameSniff implements PHP_CodeSniffer_Sniff
{
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
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        // computes the expected filename based on the name of the class or interface that it contains.
        $decNamePtr = $phpcsFile->findNext(T_STRING, $stackPtr);
        $decName = $tokens[$decNamePtr]['content'];

        // Check if the class name is prefixed
        if (preg_match('/[A-Z]{2}_(.*)/', $decName, $matches) === 1) {
            $decName = $matches[1];
        }

        $expectedFileName = ucfirst(strtolower($decName));
        // extracts filename without extension from its path.
        $fullPath = $phpcsFile->getFilename();
        $fileNameAndExt = basename($fullPath);
        $fileName = substr($fileNameAndExt, 0, strrpos($fileNameAndExt, '.'));

        if ($expectedFileName !== $fileName) {
            $data = array(
                $fileName,
                strtolower($tokens[$stackPtr]['content']), // class or interface
                $decName,
                $expectedFileName
            );
            $phpcsFile->addError('Filename "%s" doesn\'t match the name of the %s that it contains "%s" in lower case. "%s" was expected.', 0, 'IncorrectFilename', $data);
        }
    }//end process()
}//end class