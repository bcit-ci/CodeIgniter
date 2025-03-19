<?php
/**
 * Test Suite Provider for NetBeans
 * https://github.com/BrickieToolShed/netbeans-phpunit-support
 * 
 * Modified 2015 by Kenji Suzuki <https://github.com/kenjis>
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * The MIT License
 *
 * Copyright 2015 Eric VILLARD <dev@eviweb.fr>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package     netbeans\phpunit\support
 * @author      Eric VILLARD <dev@eviweb.fr>
 * @copyright   (c) 2015 Eric VILLARD <dev@eviweb.fr>
 * @license     http://opensource.org/licenses/MIT MIT License
 */

namespace netbeans\phpunit\support;

use PHPUnit_Framework_TestSuite;
use PHPUnit_Util_Configuration;

/**
 * TestSuiteProvider
 *
 * @package     netbeans\phpunit\support
 * @author      Eric VILLARD <dev@eviweb.fr>
 * @copyright   (c) 2015 Eric VILLARD <dev@eviweb.fr>
 * @license     http://opensource.org/licenses/MIT MIT License
 */
final class TestSuiteProvider
{
    /**
     * phpunit configuration file
     *
     * @var string
     */
    private static $file;

    /**
     * constructor
     */
    private function __construct() {}

    /**
     * set the phpunit configuration file
     *
     * @param string $file the path or filename of the phunit configuration file
     */
    public static function setConfigurationFile($file)
    {
        static::$file = $file;
    }

    /**
     * get the phpunit test suite instance
     *
     * @return PHPUnit_Framework_TestSuite returns the phpunit test suite instance
     * @throws FileNotFoundException       if the file is not found
     */
    public static function suite()
    {
        $file = static::checkConfigurationFile(
            static::getConfigurationFile()
        );

        return PHPUnit_Util_Configuration::getInstance($file)
            ->getTestSuiteConfiguration();
    }

    /**
     * get the phpunit configuration file
     *
     * @return string
     */
    private static function getConfigurationFile()
    {
        static::$file = isset(static::$file)
            ? static::$file
            : APPPATH.'tests/phpunit.xml';

        return static::$file;
    }

    /**
     * check the given file
     *
     * @param  string                $file file to check
     * @return string                returns the file if it is valid
     * @throws FileNotFoundException if the file is not found
     */
    private static function checkConfigurationFile($file)
    {
        if (!file_exists($file)) {
            throw new \RuntimeException("The requested phpunit configuration was not found at $file");
        }

        return $file;
    }
}
