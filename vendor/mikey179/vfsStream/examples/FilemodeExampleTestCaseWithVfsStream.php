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
use org\bovigo\vfs\vfsStream;
require_once 'FilemodeExample.php';
/**
 * Test case for class FilemodeExample.
 */
class FilemodeExampleTestCaseWithVfsStream extends \PHPUnit_Framework_TestCase
{
    /**
     * root directory
     *
     * @type  vfsStreamDirectory
     */
    protected $root;

    /**
     * set up test environmemt
     */
    public function setUp()
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    /**
     * test that the directory is created
     */
    public function testDirectoryIsCreatedWithDefaultPermissions()
    {
        $example = new FilemodeExample('id');
        $example->setDirectory(vfsStream::url('exampleDir'));
        $this->assertEquals(0700, $this->root->getChild('id')->getPermissions());
    }

    /**
     * test that the directory is created
     */
    public function testDirectoryIsCreatedWithGivenPermissions()
    {
        $example = new FilemodeExample('id', 0755);
        $example->setDirectory(vfsStream::url('exampleDir'));
        $this->assertEquals(0755, $this->root->getChild('id')->getPermissions());
    }
}
?>