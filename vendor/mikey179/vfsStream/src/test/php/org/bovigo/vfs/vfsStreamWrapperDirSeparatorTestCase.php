<?php
/**
 * This file is part of vfsStream.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\bovigo\vfs
 */
namespace org\bovigo\vfs;
/**
 * Test that using windows directory separator works correct.
 *
 * @since  0.9.0
 * @group  issue_8
 */
class vfsStreamWrapperDirSeparatorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * root diretory
     *
     * @var  vfsStreamDirectory
     */
    protected $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup();
    }

    /**
     * @test
     */
    public function fileCanBeAccessedUsingWinDirSeparator()
    {
        vfsStream::newFile('foo/bar/baz.txt')
                 ->at($this->root)
                 ->withContent('test');
        $this->assertEquals('test', file_get_contents('vfs://root/foo\bar\baz.txt'));
    }


    /**
     * @test
     */
    public function directoryCanBeCreatedUsingWinDirSeparator()
    {
        mkdir('vfs://root/dir\bar\foo', true, 0777);
        $this->assertTrue($this->root->hasChild('dir'));
        $this->assertTrue($this->root->getChild('dir')->hasChild('bar'));
        $this->assertTrue($this->root->getChild('dir/bar')->hasChild('foo'));
    }
}
?>