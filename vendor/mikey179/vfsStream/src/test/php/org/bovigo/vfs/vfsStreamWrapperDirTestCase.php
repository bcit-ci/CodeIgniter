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
require_once __DIR__ . '/vfsStreamWrapperBaseTestCase.php';
/**
 * Test for org\bovigo\vfs\vfsStreamWrapper around mkdir().
 *
 * @package     bovigo_vfs
 * @subpackage  test
 */
class vfsStreamWrapperMkDirTestCase extends vfsStreamWrapperBaseTestCase
{
    /**
     * mkdir() should not overwrite existing root
     *
     * @test
     */
    public function mkdirNoNewRoot()
    {
        $this->assertFalse(mkdir(vfsStream::url('another')));
        $this->assertEquals(2, count($this->foo->getChildren()));
        $this->assertSame($this->foo, vfsStreamWrapper::getRoot());
    }

    /**
     * mkdir() should not overwrite existing root
     *
     * @test
     */
    public function mkdirNoNewRootRecursively()
    {
        $this->assertFalse(mkdir(vfsStream::url('another/more'), 0777, true));
        $this->assertEquals(2, count($this->foo->getChildren()));
        $this->assertSame($this->foo, vfsStreamWrapper::getRoot());
    }

    /**
     * assert that mkdir() creates the correct directory structure
     *
     * @test
     * @group  permissions
     */
    public function mkdirNonRecursively()
    {
        $this->assertFalse(mkdir($this->barURL . '/another/more'));
        $this->assertEquals(2, count($this->foo->getChildren()));
        $this->assertTrue(mkdir($this->fooURL . '/another'));
        $this->assertEquals(3, count($this->foo->getChildren()));
        $this->assertEquals(0777, $this->foo->getChild('another')->getPermissions());
    }

    /**
     * assert that mkdir() creates the correct directory structure
     *
     * @test
     * @group  permissions
     */
    public function mkdirRecursively()
    {
        $this->assertTrue(mkdir($this->fooURL . '/another/more', 0777, true));
        $this->assertEquals(3, count($this->foo->getChildren()));
        $another = $this->foo->getChild('another');
        $this->assertTrue($another->hasChild('more'));
        $this->assertEquals(0777, $this->foo->getChild('another')->getPermissions());
        $this->assertEquals(0777, $this->foo->getChild('another')->getChild('more')->getPermissions());
    }

    /**
     * @test
     * @group  issue_9
     * @since  0.9.0
     */
    public function mkdirWithDots()
    {
        $this->assertTrue(mkdir($this->fooURL . '/another/../more/.', 0777, true));
        $this->assertEquals(3, count($this->foo->getChildren()));
        $this->assertTrue($this->foo->hasChild('more'));
    }

    /**
     * no root > new directory becomes root
     *
     * @test
     * @group  permissions
     */
    public function mkdirWithoutRootCreatesNewRoot()
    {
        vfsStreamWrapper::register();
        $this->assertTrue(@mkdir(vfsStream::url('foo')));
        $this->assertEquals(vfsStreamContent::TYPE_DIR, vfsStreamWrapper::getRoot()->getType());
        $this->assertEquals('foo', vfsStreamWrapper::getRoot()->getName());
        $this->assertEquals(0777, vfsStreamWrapper::getRoot()->getPermissions());
    }

    /**
     * trying to create a subdirectory of a file should not work
     *
     * @test
     */
    public function mkdirOnFileReturnsFalse()
    {
        $this->assertFalse(mkdir($this->baz1URL . '/another/more', 0777, true));
    }

    /**
     * assert that mkdir() creates the correct directory structure
     *
     * @test
     * @group  permissions
     */
    public function mkdirNonRecursivelyDifferentPermissions()
    {
        $this->assertTrue(mkdir($this->fooURL . '/another', 0755));
        $this->assertEquals(0755, $this->foo->getChild('another')->getPermissions());
    }

    /**
     * assert that mkdir() creates the correct directory structure
     *
     * @test
     * @group  permissions
     */
    public function mkdirRecursivelyDifferentPermissions()
    {
        $this->assertTrue(mkdir($this->fooURL . '/another/more', 0755, true));
        $this->assertEquals(3, count($this->foo->getChildren()));
        $another = $this->foo->getChild('another');
        $this->assertTrue($another->hasChild('more'));
        $this->assertEquals(0755, $this->foo->getChild('another')->getPermissions());
        $this->assertEquals(0755, $this->foo->getChild('another')->getChild('more')->getPermissions());
    }

    /**
     * assert that mkdir() creates the correct directory structure
     *
     * @test
     * @group  permissions
     */
    public function mkdirRecursivelyUsesDefaultPermissions()
    {
        $this->foo->chmod(0700);
        $this->assertTrue(mkdir($this->fooURL . '/another/more', 0777, true));
        $this->assertEquals(3, count($this->foo->getChildren()));
        $another = $this->foo->getChild('another');
        $this->assertTrue($another->hasChild('more'));
        $this->assertEquals(0777, $this->foo->getChild('another')->getPermissions());
        $this->assertEquals(0777, $this->foo->getChild('another')->getChild('more')->getPermissions());
    }

    /**
     * no root > new directory becomes root
     *
     * @test
     * @group  permissions
     */
    public function mkdirWithoutRootCreatesNewRootDifferentPermissions()
    {
        vfsStreamWrapper::register();
        $this->assertTrue(@mkdir(vfsStream::url('foo'), 0755));
        $this->assertEquals(vfsStreamContent::TYPE_DIR, vfsStreamWrapper::getRoot()->getType());
        $this->assertEquals('foo', vfsStreamWrapper::getRoot()->getName());
        $this->assertEquals(0755, vfsStreamWrapper::getRoot()->getPermissions());
    }

    /**
     * no root > new directory becomes root
     *
     * @test
     * @group  permissions
     */
    public function mkdirWithoutRootCreatesNewRootWithDefaultPermissions()
    {
        vfsStreamWrapper::register();
        $this->assertTrue(@mkdir(vfsStream::url('foo')));
        $this->assertEquals(vfsStreamContent::TYPE_DIR, vfsStreamWrapper::getRoot()->getType());
        $this->assertEquals('foo', vfsStreamWrapper::getRoot()->getName());
        $this->assertEquals(0777, vfsStreamWrapper::getRoot()->getPermissions());
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function mkdirDirCanNotCreateNewDirInNonWritingDirectory()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root'));
        vfsStreamWrapper::getRoot()->addChild(new vfsStreamDirectory('restrictedFolder', 0000));
        $this->assertFalse(is_writable(vfsStream::url('root/restrictedFolder/')));
        $this->assertFalse(mkdir(vfsStream::url('root/restrictedFolder/newFolder')));
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('restrictedFolder/newFolder'));
    }

    /**
     * @test
     * @group  issue_28
     */
    public function mkDirShouldNotOverwriteExistingDirectories()
    {
        vfsStream::setup('root');
        $dir = vfsStream::url('root/dir');
        $this->assertTrue(mkdir($dir));
        $this->assertFalse(@mkdir($dir));
    }

    /**
     * @test
     * @group  issue_28
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage  mkdir(): Path vfs://root/dir exists
     */
    public function mkDirShouldNotOverwriteExistingDirectoriesAndTriggerE_USER_WARNING()
    {
        vfsStream::setup('root');
        $dir = vfsStream::url('root/dir');
        $this->assertTrue(mkdir($dir));
        $this->assertFalse(mkdir($dir));
    }

    /**
     * @test
     * @group  issue_28
     */
    public function mkDirShouldNotOverwriteExistingFiles()
    {
        $root = vfsStream::setup('root');
        vfsStream::newFile('test.txt')->at($root);
        $this->assertFalse(@mkdir(vfsStream::url('root/test.txt')));
    }

    /**
     * @test
     * @group  issue_28
     * @expectedException PHPUnit_Framework_Error
     * @expectedExceptionMessage  mkdir(): Path vfs://root/test.txt exists
     */
    public function mkDirShouldNotOverwriteExistingFilesAndTriggerE_USER_WARNING()
    {
        $root = vfsStream::setup('root');
        vfsStream::newFile('test.txt')->at($root);
        $this->assertFalse(mkdir(vfsStream::url('root/test.txt')));
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function canNotIterateOverNonReadableDirectory()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root', 0000));
        $this->assertFalse(@opendir(vfsStream::url('root')));
        $this->assertFalse(@dir(vfsStream::url('root')));
    }

    /**
     * @test
     */
    public function directoryIteration()
    {
        $dir = dir($this->fooURL);
        $i   = 0;
        while (false !== ($entry = $dir->read())) {
            $i++;
            $this->assertTrue('bar' === $entry || 'baz2' === $entry);
        }

        $this->assertEquals(2, $i, 'Directory foo contains two children, but got ' . $i . ' children while iterating over directory contents');
        $dir->rewind();
        $i   = 0;
        while (false !== ($entry = $dir->read())) {
            $i++;
            $this->assertTrue('bar' === $entry || 'baz2' === $entry);
        }

        $this->assertEquals(2, $i, 'Directory foo contains two children, but got ' . $i . ' children while iterating over directory contents');
        $dir->close();
    }

    /**
     * @test
     */
    public function directoryIterationWithDot()
    {
        $dir = dir($this->fooURL . '/.');
        $i   = 0;
        while (false !== ($entry = $dir->read())) {
            $i++;
            $this->assertTrue('bar' === $entry || 'baz2' === $entry);
        }

        $this->assertEquals(2, $i, 'Directory foo contains two children, but got ' . $i . ' children while iterating over directory contents');
        $dir->rewind();
        $i   = 0;
        while (false !== ($entry = $dir->read())) {
            $i++;
            $this->assertTrue('bar' === $entry || 'baz2' === $entry);
        }

        $this->assertEquals(2, $i, 'Directory foo contains two children, but got ' . $i . ' children while iterating over directory contents');
        $dir->close();
    }

    /**
     * assure that a directory iteration works as expected
     *
     * @test
     * @group  regression
     * @group  bug_2
     */
    public function directoryIterationWithOpenDir_Bug_2()
    {
        $handle = opendir($this->fooURL);
        $i   = 0;
        while (false !== ($entry = readdir($handle))) {
            $i++;
            $this->assertTrue('bar' === $entry || 'baz2' === $entry);
        }

        $this->assertEquals(2, $i, 'Directory foo contains two children, but got ' . $i . ' children while iterating over directory contents');

        rewind($handle);
        $i   = 0;
        while (false !== ($entry = readdir($handle))) {
            $i++;
            $this->assertTrue('bar' === $entry || 'baz2' === $entry);
        }

        $this->assertEquals(2, $i, 'Directory foo contains two children, but got ' . $i . ' children while iterating over directory contents');
        closedir($handle);
    }

    /**
     * assure that a directory iteration works as expected
     *
     * @author  Christoph Bloemer
     * @test
     * @group  regression
     * @group  bug_4
     */
    public function directoryIteration_Bug_4()
    {
        $dir   = $this->fooURL;
        $list1 = array();
        if ($handle = opendir($dir)) {
            while (false !== ($listItem = readdir($handle))) {
                if ('.'  != $listItem && '..' != $listItem) {
                    if (is_file($dir . '/' . $listItem) === true) {
                        $list1[] = 'File:[' . $listItem . ']';
                    } elseif (is_dir($dir . '/' . $listItem) === true) {
                        $list1[] = 'Folder:[' . $listItem . ']';
                    }
                }
            }

            closedir($handle);
        }

        $list2 = array();
        if ($handle = opendir($dir)) {
            while (false !== ($listItem = readdir($handle))) {
                if ('.'  != $listItem && '..' != $listItem) {
                    if (is_file($dir . '/' . $listItem) === true) {
                        $list2[] = 'File:[' . $listItem . ']';
                    } elseif (is_dir($dir . '/' . $listItem) === true) {
                        $list2[] = 'Folder:[' . $listItem . ']';
                    }
                }
            }

            closedir($handle);
        }

        $this->assertEquals($list1, $list2);
        $this->assertEquals(2, count($list1));
        $this->assertEquals(2, count($list2));
    }

    /**
     * assure that a directory iteration works as expected
     *
     * @test
     */
    public function directoryIterationShouldBeIndependent()
    {
        $list1   = array();
        $list2   = array();
        $handle1 = opendir($this->fooURL);
        if (false !== ($listItem = readdir($handle1))) {
            $list1[] = $listItem;
        }

        $handle2 = opendir($this->fooURL);
        if (false !== ($listItem = readdir($handle2))) {
            $list2[] = $listItem;
        }

        if (false !== ($listItem = readdir($handle1))) {
            $list1[] = $listItem;
        }

        if (false !== ($listItem = readdir($handle2))) {
            $list2[] = $listItem;
        }

        closedir($handle1);
        closedir($handle2);
        $this->assertEquals($list1, $list2);
        $this->assertEquals(2, count($list1));
        $this->assertEquals(2, count($list2));
    }

    /**
     * assert is_dir() returns correct result
     *
     * @test
     */
    public function is_dir()
    {
        $this->assertTrue(is_dir($this->fooURL));
        $this->assertTrue(is_dir($this->fooURL . '/.'));
        $this->assertTrue(is_dir($this->barURL));
        $this->assertTrue(is_dir($this->barURL . '/.'));
        $this->assertFalse(is_dir($this->baz1URL));
        $this->assertFalse(is_dir($this->baz2URL));
        $this->assertFalse(is_dir($this->fooURL . '/another'));
        $this->assertFalse(is_dir(vfsStream::url('another')));
    }

    /**
     * can not unlink without root
     *
     * @test
     */
    public function canNotUnlinkDirectoryWithoutRoot()
    {
        vfsStreamWrapper::register();
        $this->assertFalse(@rmdir(vfsStream::url('foo')));
    }

    /**
     * rmdir() can not remove files
     *
     * @test
     */
    public function rmdirCanNotRemoveFiles()
    {
        $this->assertFalse(rmdir($this->baz1URL));
        $this->assertFalse(rmdir($this->baz2URL));
    }

    /**
     * rmdir() can not remove a non-existing directory
     *
     * @test
     */
    public function rmdirCanNotRemoveNonExistingDirectory()
    {
        $this->assertFalse(rmdir($this->fooURL . '/another'));
    }

    /**
     * rmdir() can not remove non-empty directories
     *
     * @test
     */
    public function rmdirCanNotRemoveNonEmptyDirectory()
    {
        $this->assertFalse(rmdir($this->fooURL));
        $this->assertFalse(rmdir($this->barURL));
    }

    /**
     * @test
     */
    public function rmdirCanRemoveEmptyDirectory()
    {
        vfsStream::newDirectory('empty')->at($this->foo);
        $this->assertTrue($this->foo->hasChild('empty'));
        $this->assertTrue(rmdir($this->fooURL . '/empty'));
        $this->assertFalse($this->foo->hasChild('empty'));
    }

    /**
     * @test
     */
    public function rmdirCanRemoveEmptyDirectoryWithDot()
    {
        vfsStream::newDirectory('empty')->at($this->foo);
        $this->assertTrue($this->foo->hasChild('empty'));
        $this->assertTrue(rmdir($this->fooURL . '/empty/.'));
        $this->assertFalse($this->foo->hasChild('empty'));
    }

    /**
     * rmdir() can remove empty directories
     *
     * @test
     */
    public function rmdirCanRemoveEmptyRoot()
    {
        $this->foo->removeChild('bar');
        $this->foo->removeChild('baz2');
        $this->assertTrue(rmdir($this->fooURL));
        $this->assertFalse(file_exists($this->fooURL)); // make sure statcache was cleared
        $this->assertNull(vfsStreamWrapper::getRoot());
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function rmdirDirCanNotRemoveDirFromNonWritingDirectory()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root', 0000));
        vfsStreamWrapper::getRoot()->addChild(new vfsStreamDirectory('nonRemovableFolder'));
        $this->assertFalse(is_writable(vfsStream::url('root')));
        $this->assertFalse(rmdir(vfsStream::url('root/nonRemovableFolder')));
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('nonRemovableFolder'));
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_17
     */
    public function issue17()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root', 0770));
        vfsStreamWrapper::getRoot()->chgrp(vfsStream::GROUP_USER_1)
                                   ->chown(vfsStream::OWNER_USER_1);
        $this->assertFalse(mkdir(vfsStream::url('root/doesNotWork')));
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('doesNotWork'));
    }

    /**
     * @test
     * @group  bug_19
     */
    public function accessWithDoubleDotReturnsCorrectContent()
    {
        $this->assertEquals('baz2',
                file_get_contents(vfsStream::url('foo/bar/../baz2'))
        );
    }

    /**
     * @test
     * @since  0.11.0
     * @group  issue_23
     */
    public function unlinkCanNotRemoveNonEmptyDirectory()
    {
        try {
            $this->assertFalse(unlink($this->barURL));
        } catch (\PHPUnit_Framework_Error $fe) {
            $this->assertEquals('unlink(vfs://foo/bar): Operation not permitted', $fe->getMessage());
        }

        $this->assertTrue($this->foo->hasChild('bar'));
        $this->assertFileExists($this->barURL);
    }

    /**
     * @test
     * @since  0.11.0
     * @group  issue_23
     */
    public function unlinkCanNotRemoveEmptyDirectory()
    {
        vfsStream::newDirectory('empty')->at($this->foo);
        try {
            $this->assertTrue(unlink($this->fooURL . '/empty'));
        } catch (\PHPUnit_Framework_Error $fe) {
            $this->assertEquals('unlink(vfs://foo/empty): Operation not permitted', $fe->getMessage());
        }

        $this->assertTrue($this->foo->hasChild('empty'));
        $this->assertFileExists($this->fooURL . '/empty');
    }

    /**
     * @test
     * @group  issue_32
     */
    public function canCreateFolderOfSameNameAsParentFolder()
    {
        $root = vfsStream::setup('testFolder');
        mkdir(vfsStream::url('testFolder') . '/testFolder/subTestFolder', 0777, true);
        $this->assertTrue(file_exists(vfsStream::url('testFolder/testFolder/subTestFolder/.')));
    }

    /**
     * @test
     * @group  issue_32
     */
    public function canRetrieveFolderOfSameNameAsParentFolder()
    {
        $root = vfsStream::setup('testFolder');
        mkdir(vfsStream::url('testFolder') . '/testFolder/subTestFolder', 0777, true);
        $this->assertTrue($root->hasChild('testFolder'));
        $this->assertNotNull($root->getChild('testFolder'));
    }
}
?>