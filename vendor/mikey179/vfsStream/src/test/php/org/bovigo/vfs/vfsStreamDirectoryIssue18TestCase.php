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
 * Test for org\bovigo\vfs\vfsStreamDirectory.
 *
 * @group  bug_18
 */
class vfsStreamDirectoryIssue18TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * access to root directory
     *
     * @var  vfsStreamDirectory
     */
    protected $rootDirectory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rootDirectory = vfsStream::newDirectory('/');
        $this->rootDirectory->addChild(vfsStream::newDirectory('var/log/app'));
        $dir = $this->rootDirectory->getChild('var/log/app');
        $dir->addChild(vfsStream::newDirectory('app1'));
        $dir->addChild(vfsStream::newDirectory('app2'));
        $dir->addChild(vfsStream::newDirectory('foo'));
    }

    /**
     * @test
     */
    public function shouldContainThreeSubdirectories()
    {
        $this->assertEquals(3,
                            count($this->rootDirectory->getChild('var/log/app')->getChildren())
        );
    }

    /**
     * @test
     */
    public function shouldContainSubdirectoryFoo()
    {
        $this->assertTrue($this->rootDirectory->getChild('var/log/app')->hasChild('foo'));
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory',
                                $this->rootDirectory->getChild('var/log/app')->getChild('foo')
        );
    }

    /**
     * @test
     */
    public function shouldContainSubdirectoryApp1()
    {
        $this->assertTrue($this->rootDirectory->getChild('var/log/app')->hasChild('app1'));
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory',
                                $this->rootDirectory->getChild('var/log/app')->getChild('app1')
        );
    }

    /**
     * @test
     */
    public function shouldContainSubdirectoryApp2()
    {
        $this->assertTrue($this->rootDirectory->getChild('var/log/app')->hasChild('app2'));
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory',
                                $this->rootDirectory->getChild('var/log/app')->getChild('app2')
        );
    }
}
?>