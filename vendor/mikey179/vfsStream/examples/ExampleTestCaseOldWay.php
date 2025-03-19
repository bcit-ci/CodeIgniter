<?php
/**
 * This file is part of vfsStream.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\bovigo\vfs
 */
namespace org\bovigo\vfs\example;
require_once 'Example.php';
/**
 * Test case for class Example.
 */
class ExampleTestCaseOldWay extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environmemt
     */
    public function setUp()
    {
        if (file_exists(__DIR__ . '/id') === true) {
            rmdir(__DIR__ . '/id');
        }
    }

    /**
     * clear up test environment
     */
    public function tearDown()
    {
        if (file_exists(__DIR__ . '/id') === true) {
            rmdir(__DIR__ . '/id');
        }
    }

    /**
     * @test
     */
    public function directoryIsCreated()
    {
        $example = new Example('id');
        $this->assertFalse(file_exists(__DIR__ . '/id'));
        $example->setDirectory(__DIR__);
        $this->assertTrue(file_exists(__DIR__ . '/id'));
    }
}
?>