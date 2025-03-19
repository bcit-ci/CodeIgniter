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
require_once 'FilemodeExample.php';
/**
 * Test case for class FilemodeExample.
 */
class FilemodeExampleTestCaseOldWay extends \PHPUnit_Framework_TestCase
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
     * test correct file mode for created directory
     */
    public function testDirectoryHasCorrectDefaultFilePermissions()
    {
        $example = new FilemodeExample('id');
        $example->setDirectory(__DIR__);
        if (DIRECTORY_SEPARATOR === '\\') {
            // can not really test on windows, filemode from mkdir() is ignored
            $this->assertEquals(40777, decoct(fileperms(__DIR__ . '/id')));
        } else {
            $this->assertEquals(40700, decoct(fileperms(__DIR__ . '/id')));
        }
    }

    /**
     * test correct file mode for created directory
     */
    public function testDirectoryHasCorrectDifferentFilePermissions()
    {
        $example = new FilemodeExample('id', 0755);
        $example->setDirectory(__DIR__);
        if (DIRECTORY_SEPARATOR === '\\') {
            // can not really test on windows, filemode from mkdir() is ignored
            $this->assertEquals(40777, decoct(fileperms(__DIR__ . '/id')));
        } else {
            $this->assertEquals(40755, decoct(fileperms(__DIR__ . '/id')));
        }
    }
}
?>