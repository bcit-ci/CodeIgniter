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
 * Test for umask settings.
 *
 * @group  permissions
 * @group  umask
 * @since  0.8.0
 */
class vfsStreamUmaskTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        vfsStream::umask(0000);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        vfsStream::umask(0000);
    }

    /**
     * @test
     */
    public function gettingUmaskSettingDoesNotChangeUmaskSetting()
    {
        $this->assertEquals(vfsStream::umask(),
                            vfsStream::umask()
        );
        $this->assertEquals(0000,
                            vfsStream::umask()
        );
    }

    /**
     * @test
     */
    public function changingUmaskSettingReturnsOldUmaskSetting()
    {
        $this->assertEquals(0000,
                            vfsStream::umask(0022)
        );
        $this->assertEquals(0022,
                            vfsStream::umask()
        );
    }

    /**
     * @test
     */
    public function createFileWithDefaultUmaskSetting()
    {
        $file = new vfsStreamFile('foo');
        $this->assertEquals(0666, $file->getPermissions());
    }

    /**
     * @test
     */
    public function createFileWithDifferentUmaskSetting()
    {
        vfsStream::umask(0022);
        $file = new vfsStreamFile('foo');
        $this->assertEquals(0644, $file->getPermissions());
    }

    /**
     * @test
     */
    public function createDirectoryWithDefaultUmaskSetting()
    {
        $directory = new vfsStreamDirectory('foo');
        $this->assertEquals(0777, $directory->getPermissions());
    }

    /**
     * @test
     */
    public function createDirectoryWithDifferentUmaskSetting()
    {
        vfsStream::umask(0022);
        $directory = new vfsStreamDirectory('foo');
        $this->assertEquals(0755, $directory->getPermissions());
    }

    /**
     * @test
     */
    public function createFileUsingStreamWithDefaultUmaskSetting()
    {
        $root = vfsStream::setup();
        file_put_contents(vfsStream::url('root/newfile.txt'), 'file content');
        $this->assertEquals(0666, $root->getChild('newfile.txt')->getPermissions());
    }

    /**
     * @test
     */
    public function createFileUsingStreamWithDifferentUmaskSetting()
    {
        $root = vfsStream::setup();
        vfsStream::umask(0022);
        file_put_contents(vfsStream::url('root/newfile.txt'), 'file content');
        $this->assertEquals(0644, $root->getChild('newfile.txt')->getPermissions());
    }

    /**
     * @test
     */
    public function createDirectoryUsingStreamWithDefaultUmaskSetting()
    {
        $root = vfsStream::setup();
        mkdir(vfsStream::url('root/newdir'));
        $this->assertEquals(0777, $root->getChild('newdir')->getPermissions());
    }

    /**
     * @test
     */
    public function createDirectoryUsingStreamWithDifferentUmaskSetting()
    {
        $root = vfsStream::setup();
        vfsStream::umask(0022);
        mkdir(vfsStream::url('root/newdir'));
        $this->assertEquals(0755, $root->getChild('newdir')->getPermissions());
    }

    /**
     * @test
     */
    public function createDirectoryUsingStreamWithExplicit0()
    {
        $root = vfsStream::setup();
        vfsStream::umask(0022);
        mkdir(vfsStream::url('root/newdir'), null);
        $this->assertEquals(0000, $root->getChild('newdir')->getPermissions());
    }

    /**
     * @test
     *
     */
    public function createDirectoryUsingStreamWithDifferentUmaskSettingButExplicit0777()
    {
        $root = vfsStream::setup();
        vfsStream::umask(0022);
        mkdir(vfsStream::url('root/newdir'), 0777);
        $this->assertEquals(0755, $root->getChild('newdir')->getPermissions());
    }

    /**
     * @test
     */
    public function createDirectoryUsingStreamWithDifferentUmaskSettingButExplicitModeRequestedByCall()
    {
        $root = vfsStream::setup();
        vfsStream::umask(0022);
        mkdir(vfsStream::url('root/newdir'), 0700);
        $this->assertEquals(0700, $root->getChild('newdir')->getPermissions());
    }

    /**
     * @test
     */
    public function defaultUmaskSettingDoesNotInfluenceSetup()
    {
        $root = vfsStream::setup();
        $this->assertEquals(0777, $root->getPermissions());
    }

    /**
     * @test
     */
    public function umaskSettingShouldBeRespectedBySetup()
    {
        vfsStream::umask(0022);
        $root = vfsStream::setup();
        $this->assertEquals(0755, $root->getPermissions());
    }
}
?>