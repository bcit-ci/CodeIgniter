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
require_once 'FailureExample.php';
/**
 * Test case for class FailureExample.
 */
class FailureExampleTestCase extends \PHPUnit_Framework_TestCase
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
     * @test
     */
    public function returnsOkOnNoFailure()
    {
        $example = new FailureExample(vfsStream::url('exampleDir/test.txt'));
        $this->assertSame('ok', $example->writeData('testdata'));
        $this->assertTrue($this->root->hasChild('test.txt'));
        $this->assertSame('testdata', $this->root->getChild('test.txt')->getContent());
    }

    /**
     * @test
     */
    public function returnsErrorMessageIfWritingToFileFails()
    {
        $file = vfsStream::newFile('test.txt', 0000)
                         ->withContent('notoverwritten')
                         ->at($this->root);
        $example = new FailureExample(vfsStream::url('exampleDir/test.txt'));
        $this->assertSame('could not write data', $example->writeData('testdata'));
        $this->assertTrue($this->root->hasChild('test.txt'));
        $this->assertSame('notoverwritten', $this->root->getChild('test.txt')->getContent());
    }
}
?>