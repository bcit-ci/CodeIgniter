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
 * @author    Piotr Karas <office@mediaself.pl>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @see       http://en.wikipedia.org/wiki/Byte_order_mark
 */
class CodeIgniter_Sniffs_Files_ByteOrderMarkSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * List of supported BOM definitions.
     *
     * Use encoding names as keys and hex BOM representations as values.
     *
     * @var array
     */
    public $bomDefinitions = array(
                              'UTF-8'       => 'efbbbf',
                              'UTF-16 (BE)' => 'feff',
                              'UTF-16 (LE)' => 'fffe',
                             );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_INLINE_HTML);

    }//end register()

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
    {
        // The BOM will be the very first token in the file.
        if ($stackPtr !== 0) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        foreach ($this->bomDefinitions as $bomName => $expectedBomHex) {
            $bomByteLength = (strlen($expectedBomHex) / 2);
            $htmlBomHex    = bin2hex(substr($tokens[$stackPtr]['content'], 0, $bomByteLength));
            if ($htmlBomHex === $expectedBomHex) {
                $errorData = array($bomName);
                $error     = 'File contains %s byte order mark, which may corrupt your application';
                $phpcsFile->addError($error, $stackPtr, 'Found', $errorData);
                break;
            }
        }
    }//end process()

}//end class