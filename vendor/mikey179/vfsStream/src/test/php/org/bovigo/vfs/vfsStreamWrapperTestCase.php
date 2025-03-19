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
 * Test for org\bovigo\vfs\vfsStreamWrapper.
 */
class vfsStreamWrapperTestCase extends vfsStreamWrapperBaseTestCase
{
    /**
     * ensure that a call to vfsStreamWrapper::register() resets the stream
     *
     * Implemented after a request by David Zülke.
     *
     * @test
     */
    public function resetByRegister()
    {
        $this->assertSame($this->foo, vfsStreamWrapper::getRoot());
        vfsStreamWrapper::register();
        $this->assertNull(vfsStreamWrapper::getRoot());
    }

    /**
     * @test
     * @since  0.11.0
     */
    public function setRootReturnsRoot()
    {
        vfsStreamWrapper::register();
        $root = vfsStream::newDirectory('root');
        $this->assertSame($root, vfsStreamWrapper::setRoot($root));
    }

    /**
     * assure that filesize is returned correct
     *
     * @test
     */
    public function filesize()
    {
        $this->assertEquals(0, filesize($this->fooURL));
        $this->assertEquals(0, filesize($this->fooURL . '/.'));
        $this->assertEquals(0, filesize($this->barURL));
        $this->assertEquals(0, filesize($this->barURL . '/.'));
        $this->assertEquals(4, filesize($this->baz2URL));
        $this->assertEquals(5, filesize($this->baz1URL));
    }

    /**
     * assert that file_exists() delivers correct result
     *
     * @test
     */
    public function file_exists()
    {
        $this->assertTrue(file_exists($this->fooURL));
        $this->assertTrue(file_exists($this->fooURL . '/.'));
        $this->assertTrue(file_exists($this->barURL));
        $this->assertTrue(file_exists($this->barURL . '/.'));
        $this->assertTrue(file_exists($this->baz1URL));
        $this->assertTrue(file_exists($this->baz2URL));
        $this->assertFalse(file_exists($this->fooURL . '/another'));
        $this->assertFalse(file_exists(vfsStream::url('another')));
    }

    /**
     * assert that filemtime() delivers correct result
     *
     * @test
     */
    public function filemtime()
    {
        $this->assertEquals(100, filemtime($this->fooURL));
        $this->assertEquals(100, filemtime($this->fooURL . '/.'));
        $this->assertEquals(200, filemtime($this->barURL));
        $this->assertEquals(200, filemtime($this->barURL . '/.'));
        $this->assertEquals(300, filemtime($this->baz1URL));
        $this->assertEquals(400, filemtime($this->baz2URL));
    }

    /**
     * @test
     * @group  issue_23
     */
    public function unlinkRemovesFilesOnly()
    {
        $this->assertTrue(unlink($this->baz2URL));
        $this->assertFalse(file_exists($this->baz2URL)); // make sure statcache was cleared
        $this->assertEquals(array($this->bar), $this->foo->getChildren());
        $this->assertFalse(unlink($this->fooURL . '/another'));
        $this->assertFalse(unlink(vfsStream::url('another')));
        $this->assertEquals(array($this->bar), $this->foo->getChildren());
    }

    /**
     * assert dirname() returns correct directory name
     *
     * @test
     */
    public function dirname()
    {
        $this->assertEquals($this->fooURL, dirname($this->barURL));
        $this->assertEquals($this->barURL, dirname($this->baz1URL));
        # returns "vfs:" instead of "."
        # however this seems not to be fixable because dirname() does not
        # call the stream wrapper
        #$this->assertEquals(dirname(vfsStream::url('doesNotExist')), '.');
    }

    /**
     * assert basename() returns correct file name
     *
     * @test
     */
    public function basename()
    {
        $this->assertEquals('bar', basename($this->barURL));
        $this->assertEquals('baz1', basename($this->baz1URL));
        $this->assertEquals('doesNotExist', basename(vfsStream::url('doesNotExist')));
    }

    /**
     * assert is_readable() works correct
     *
     * @test
     */
    public function is_readable()
    {
        $this->assertTrue(is_readable($this->fooURL));
        $this->assertTrue(is_readable($this->fooURL . '/.'));
        $this->assertTrue(is_readable($this->barURL));
        $this->assertTrue(is_readable($this->barURL . '/.'));
        $this->assertTrue(is_readable($this->baz1URL));
        $this->assertTrue(is_readable($this->baz2URL));
        $this->assertFalse(is_readable($this->fooURL . '/another'));
        $this->assertFalse(is_readable(vfsStream::url('another')));

        $this->foo->chmod(0222);
        $this->assertFalse(is_readable($this->fooURL));

        $this->baz1->chmod(0222);
        $this->assertFalse(is_readable($this->baz1URL));
    }

    /**
     * assert is_writable() works correct
     *
     * @test
     */
    public function is_writable()
    {
        $this->assertTrue(is_writable($this->fooURL));
        $this->assertTrue(is_writable($this->fooURL . '/.'));
        $this->assertTrue(is_writable($this->barURL));
        $this->assertTrue(is_writable($this->barURL . '/.'));
        $this->assertTrue(is_writable($this->baz1URL));
        $this->assertTrue(is_writable($this->baz2URL));
        $this->assertFalse(is_writable($this->fooURL . '/another'));
        $this->assertFalse(is_writable(vfsStream::url('another')));

        $this->foo->chmod(0444);
        $this->assertFalse(is_writable($this->fooURL));

        $this->baz1->chmod(0444);
        $this->assertFalse(is_writable($this->baz1URL));
    }

    /**
     * assert is_executable() works correct
     *
     * @test
     */
    public function is_executable()
    {
        $this->assertFalse(is_executable($this->baz1URL));
        $this->baz1->chmod(0766);
        $this->assertTrue(is_executable($this->baz1URL));
        $this->assertFalse(is_executable($this->baz2URL));
    }

    /**
     * assert is_executable() works correct
     *
     * @test
     */
    public function directoriesAndNonExistingFilesAreNeverExecutable()
    {
        $this->assertFalse(is_executable($this->fooURL));
        $this->assertFalse(is_executable($this->fooURL . '/.'));
        $this->assertFalse(is_executable($this->barURL));
        $this->assertFalse(is_executable($this->barURL . '/.'));
        $this->assertFalse(is_executable($this->fooURL . '/another'));
        $this->assertFalse(is_executable(vfsStream::url('another')));
    }

    /**
     * file permissions
     *
     * @test
     * @group  permissions
     */
    public function chmod()
    {
        $this->assertEquals(40777, decoct(fileperms($this->fooURL)));
        $this->assertEquals(40777, decoct(fileperms($this->fooURL . '/.')));
        $this->assertEquals(40777, decoct(fileperms($this->barURL)));
        $this->assertEquals(40777, decoct(fileperms($this->barURL . '/.')));
        $this->assertEquals(100666, decoct(fileperms($this->baz1URL)));
        $this->assertEquals(100666, decoct(fileperms($this->baz2URL)));

        $this->foo->chmod(0755);
        $this->bar->chmod(0700);
        $this->baz1->chmod(0644);
        $this->baz2->chmod(0600);
        $this->assertEquals(40755, decoct(fileperms($this->fooURL)));
        $this->assertEquals(40755, decoct(fileperms($this->fooURL . '/.')));
        $this->assertEquals(40700, decoct(fileperms($this->barURL)));
        $this->assertEquals(40700, decoct(fileperms($this->barURL . '/.')));
        $this->assertEquals(100644, decoct(fileperms($this->baz1URL)));
        $this->assertEquals(100600, decoct(fileperms($this->baz2URL)));
    }

    /**
     * @test
     * @group  issue_11
     * @group  permissions
     */
    public function chmodModifiesPermissions()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->assertFalse(@chmod($this->fooURL, 0755));
            $this->assertFalse(@chmod($this->barURL, 0711));
            $this->assertFalse(@chmod($this->baz1URL, 0644));
            $this->assertFalse(@chmod($this->baz2URL, 0664));
            $this->assertEquals(40777, decoct(fileperms($this->fooURL)));
            $this->assertEquals(40777, decoct(fileperms($this->barURL)));
            $this->assertEquals(100666, decoct(fileperms($this->baz1URL)));
            $this->assertEquals(100666, decoct(fileperms($this->baz2URL)));
        } else {
            $this->assertTrue(chmod($this->fooURL, 0755));
            $this->assertTrue(chmod($this->barURL, 0711));
            $this->assertTrue(chmod($this->baz1URL, 0644));
            $this->assertTrue(chmod($this->baz2URL, 0664));
            $this->assertEquals(40755, decoct(fileperms($this->fooURL)));
            $this->assertEquals(40711, decoct(fileperms($this->barURL)));
            $this->assertEquals(100644, decoct(fileperms($this->baz1URL)));
            $this->assertEquals(100664, decoct(fileperms($this->baz2URL)));
        }
    }

    /**
     * @test
     * @group  permissions
     */
    public function fileownerIsCurrentUserByDefault()
    {
        $this->assertEquals(vfsStream::getCurrentUser(), fileowner($this->fooURL));
        $this->assertEquals(vfsStream::getCurrentUser(), fileowner($this->fooURL . '/.'));
        $this->assertEquals(vfsStream::getCurrentUser(), fileowner($this->barURL));
        $this->assertEquals(vfsStream::getCurrentUser(), fileowner($this->barURL . '/.'));
        $this->assertEquals(vfsStream::getCurrentUser(), fileowner($this->baz1URL));
        $this->assertEquals(vfsStream::getCurrentUser(), fileowner($this->baz2URL));
    }

    /**
     * @test
     * @group  issue_11
     * @group  permissions
     */
    public function chownChangesUser()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->foo->chown(vfsStream::OWNER_USER_1);
            $this->bar->chown(vfsStream::OWNER_USER_1);
            $this->baz1->chown(vfsStream::OWNER_USER_2);
            $this->baz2->chown(vfsStream::OWNER_USER_2);
        } else {
            chown($this->fooURL, vfsStream::OWNER_USER_1);
            chown($this->barURL, vfsStream::OWNER_USER_1);
            chown($this->baz1URL, vfsStream::OWNER_USER_2);
            chown($this->baz2URL, vfsStream::OWNER_USER_2);
        }

        $this->assertEquals(vfsStream::OWNER_USER_1, fileowner($this->fooURL));
        $this->assertEquals(vfsStream::OWNER_USER_1, fileowner($this->fooURL . '/.'));
        $this->assertEquals(vfsStream::OWNER_USER_1, fileowner($this->barURL));
        $this->assertEquals(vfsStream::OWNER_USER_1, fileowner($this->barURL . '/.'));
        $this->assertEquals(vfsStream::OWNER_USER_2, fileowner($this->baz1URL));
        $this->assertEquals(vfsStream::OWNER_USER_2, fileowner($this->baz2URL));
    }

    /**
     * @test
     * @group  issue_11
     * @group  permissions
     */
    public function chownDoesNotWorkOnVfsStreamUrls()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->assertFalse(@chown($this->fooURL, vfsStream::OWNER_USER_2));
            $this->assertEquals(vfsStream::getCurrentUser(), fileowner($this->fooURL));
        }
    }

    /**
     * @test
     * @group  issue_11
     * @group  permissions
     */
    public function groupIsCurrentGroupByDefault()
    {
        $this->assertEquals(vfsStream::getCurrentGroup(), filegroup($this->fooURL));
        $this->assertEquals(vfsStream::getCurrentGroup(), filegroup($this->fooURL . '/.'));
        $this->assertEquals(vfsStream::getCurrentGroup(), filegroup($this->barURL));
        $this->assertEquals(vfsStream::getCurrentGroup(), filegroup($this->barURL . '/.'));
        $this->assertEquals(vfsStream::getCurrentGroup(), filegroup($this->baz1URL));
        $this->assertEquals(vfsStream::getCurrentGroup(), filegroup($this->baz2URL));
    }

    /**
     * @test
     * @group  issue_11
     * @group  permissions
     */
    public function chgrp()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->foo->chgrp(vfsStream::GROUP_USER_1);
            $this->bar->chgrp(vfsStream::GROUP_USER_1);
            $this->baz1->chgrp(vfsStream::GROUP_USER_2);
            $this->baz2->chgrp(vfsStream::GROUP_USER_2);
        } else {
            chgrp($this->fooURL, vfsStream::GROUP_USER_1);
            chgrp($this->barURL, vfsStream::GROUP_USER_1);
            chgrp($this->baz1URL, vfsStream::GROUP_USER_2);
            chgrp($this->baz2URL, vfsStream::GROUP_USER_2);
        }

        $this->assertEquals(vfsStream::GROUP_USER_1, filegroup($this->fooURL));
        $this->assertEquals(vfsStream::GROUP_USER_1, filegroup($this->fooURL . '/.'));
        $this->assertEquals(vfsStream::GROUP_USER_1, filegroup($this->barURL));
        $this->assertEquals(vfsStream::GROUP_USER_1, filegroup($this->barURL . '/.'));
        $this->assertEquals(vfsStream::GROUP_USER_2, filegroup($this->baz1URL));
        $this->assertEquals(vfsStream::GROUP_USER_2, filegroup($this->baz2URL));
    }

    /**
     * @test
     * @group  issue_11
     * @group  permissions
     */
    public function chgrpDoesNotWorkOnVfsStreamUrls()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->assertFalse(@chgrp($this->fooURL, vfsStream::GROUP_USER_2));
            $this->assertEquals(vfsStream::getCurrentGroup(), filegroup($this->fooURL));
        }
    }

    /**
     * @test
     * @author  Benoit Aubuchon
     */
    public function renameDirectory()
    {
        // move foo/bar to foo/baz3
        $baz3URL = vfsStream::url('foo/baz3');
        $this->assertTrue(rename($this->barURL, $baz3URL));
        $this->assertFileExists($baz3URL);
        $this->assertFileNotExists($this->barURL);
    }

    /**
     * @test
     */
    public function renameDirectoryWithDots()
    {
        // move foo/bar to foo/baz3
        $baz3URL = vfsStream::url('foo/baz3');
        $this->assertTrue(rename($this->barURL . '/.', $baz3URL));
        $this->assertFileExists($baz3URL);
        $this->assertFileNotExists($this->barURL);
    }

    /**
     * @test
     * @group  issue_9
     * @since  0.9.0
     */
    public function renameDirectoryWithDotsInTarget()
    {
        // move foo/bar to foo/baz3
        $baz3URL = vfsStream::url('foo/../foo/baz3/.');
        $this->assertTrue(rename($this->barURL . '/.', $baz3URL));
        $this->assertFileExists($baz3URL);
        $this->assertFileNotExists($this->barURL);
    }

    /**
     * @test
     * @author  Benoit Aubuchon
     */
    public function renameDirectoryOverwritingExistingFile()
    {
        // move foo/bar to foo/baz2
        $this->assertTrue(rename($this->barURL, $this->baz2URL));
        $this->assertFileExists(vfsStream::url('foo/baz2/baz1'));
        $this->assertFileNotExists($this->barURL);
    }

    /**
     * @test
     * @expectedException  PHPUnit_Framework_Error
     */
    public function renameFileIntoFile()
    {
        // foo/baz2 is a file, so it can not be turned into a directory
        $baz3URL = vfsStream::url('foo/baz2/baz3');
        $this->assertTrue(rename($this->baz1URL, $baz3URL));
        $this->assertFileExists($baz3URL);
        $this->assertFileNotExists($this->baz1URL);
    }

    /**
     * @test
     * @author  Benoit Aubuchon
     */
    public function renameFileToDirectory()
    {
        // move foo/bar/baz1 to foo/baz3
        $baz3URL = vfsStream::url('foo/baz3');
        $this->assertTrue(rename($this->baz1URL, $baz3URL));
        $this->assertFileExists($this->barURL);
        $this->assertFileExists($baz3URL);
        $this->assertFileNotExists($this->baz1URL);
    }

    /**
     * assert that trying to rename from a non existing file trigger a warning
     *
     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function renameOnSourceFileNotFound()
    {
        rename(vfsStream::url('notfound'), $this->baz1URL);
    }
    /**
     * assert that trying to rename to a directory that is not found trigger a warning

     * @expectedException PHPUnit_Framework_Error
     * @test
     */
    public function renameOnDestinationDirectoryFileNotFound()
    {
        rename($this->baz1URL, vfsStream::url('foo/notfound/file2'));
    }
    /**
     * stat() and fstat() should return the same result
     *
     * @test
     */
    public function statAndFstatReturnSameResult()
    {
        $fp = fopen($this->baz2URL, 'r');
        $this->assertEquals(stat($this->baz2URL),
                            fstat($fp)
        );
        fclose($fp);
    }

    /**
     * stat() returns full data
     *
     * @test
     */
    public function statReturnsFullDataForFiles()
    {
        $this->assertEquals(array(0         => 0,
                                  1         => 0,
                                  2         => 0100666,
                                  3         => 0,
                                  4         => vfsStream::getCurrentUser(),
                                  5         => vfsStream::getCurrentGroup(),
                                  6         => 0,
                                  7         => 4,
                                  8         => 400,
                                  9         => 400,
                                  10        => 400,
                                  11        => -1,
                                  12        => -1,
                                  'dev'     => 0,
                                  'ino'     => 0,
                                  'mode'    => 0100666,
                                  'nlink'   => 0,
                                  'uid'     => vfsStream::getCurrentUser(),
                                  'gid'     => vfsStream::getCurrentGroup(),
                                  'rdev'    => 0,
                                  'size'    => 4,
                                  'atime'   => 400,
                                  'mtime'   => 400,
                                  'ctime'   => 400,
                                  'blksize' => -1,
                                  'blocks'  => -1
                            ),
                            stat($this->baz2URL)
        );
    }

    /**
     * @test
     */
    public function statReturnsFullDataForDirectories()
    {
        $this->assertEquals(array(0         => 0,
                                  1         => 0,
                                  2         => 0040777,
                                  3         => 0,
                                  4         => vfsStream::getCurrentUser(),
                                  5         => vfsStream::getCurrentGroup(),
                                  6         => 0,
                                  7         => 0,
                                  8         => 100,
                                  9         => 100,
                                  10        => 100,
                                  11        => -1,
                                  12        => -1,
                                  'dev'     => 0,
                                  'ino'     => 0,
                                  'mode'    => 0040777,
                                  'nlink'   => 0,
                                  'uid'     => vfsStream::getCurrentUser(),
                                  'gid'     => vfsStream::getCurrentGroup(),
                                  'rdev'    => 0,
                                  'size'    => 0,
                                  'atime'   => 100,
                                  'mtime'   => 100,
                                  'ctime'   => 100,
                                  'blksize' => -1,
                                  'blocks'  => -1
                            ),
                            stat($this->fooURL)
        );
    }

    /**
     * @test
     */
    public function statReturnsFullDataForDirectoriesWithDot()
    {
        $this->assertEquals(array(0         => 0,
                                  1         => 0,
                                  2         => 0040777,
                                  3         => 0,
                                  4         => vfsStream::getCurrentUser(),
                                  5         => vfsStream::getCurrentGroup(),
                                  6         => 0,
                                  7         => 0,
                                  8         => 100,
                                  9         => 100,
                                  10        => 100,
                                  11        => -1,
                                  12        => -1,
                                  'dev'     => 0,
                                  'ino'     => 0,
                                  'mode'    => 0040777,
                                  'nlink'   => 0,
                                  'uid'     => vfsStream::getCurrentUser(),
                                  'gid'     => vfsStream::getCurrentGroup(),
                                  'rdev'    => 0,
                                  'size'    => 0,
                                  'atime'   => 100,
                                  'mtime'   => 100,
                                  'ctime'   => 100,
                                  'blksize' => -1,
                                  'blocks'  => -1
                            ),
                            stat($this->fooURL . '/.')
        );
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error
     */
    public function openFileWithoutDirectory()
    {
        vfsStreamWrapper::register();
        $this->assertFalse(file_get_contents(vfsStream::url('file.txt')));
    }

    /**
     * @test
     * @group  issue_33
     * @since  1.1.0
     */
    public function truncateRemovesSuperflouosContent()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $handle = fopen($this->baz1URL, "r+");
        $this->assertTrue(ftruncate($handle, 0));
        $this->assertEquals(0, filesize($this->baz1URL));
        $this->assertEquals('', file_get_contents($this->baz1URL));
        fclose($handle);
    }

    /**
     * @test
     * @group  issue_33
     * @since  1.1.0
     */
    public function truncateToGreaterSizeAddsZeroBytes()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $handle = fopen($this->baz1URL, "r+");
        $this->assertTrue(ftruncate($handle, 25));
        $this->assertEquals(25, filesize($this->baz1URL));
        $this->assertEquals("baz 1\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0",
                            file_get_contents($this->baz1URL));
        fclose($handle);
    }

    /**
     * @test
     * @group  issue_11
     */
    public function touchCreatesNonExistingFile()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $this->assertTrue(touch($this->fooURL . '/new.txt'));
        $this->assertTrue($this->foo->hasChild('new.txt'));
    }

    /**
     * @test
     * @group  issue_11
     */
    public function touchChangesAccessAndModificationTimeForFile()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $this->assertTrue(touch($this->baz1URL, 303, 313));
        $this->assertEquals(303, $this->baz1->filemtime());
        $this->assertEquals(313, $this->baz1->fileatime());
    }

    /**
     * @test
     * @group  issue_11
     */
    public function touchDoesNotChangeTimesWhenNoTimesGiven()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $this->assertTrue(touch($this->baz1URL));
        $this->assertEquals(300, $this->baz1->filemtime());
        $this->assertEquals(300, $this->baz1->fileatime());
    }

    /**
     * @test
     * @group  issue_11
     */
    public function touchWithModifiedTimeChangesAccessAndModifiedTime()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $this->assertTrue(touch($this->baz1URL, 303));
        $this->assertEquals(303, $this->baz1->filemtime());
        $this->assertEquals(303, $this->baz1->fileatime());
    }

    /**
     * @test
     * @group  issue_11
     */
    public function touchChangesAccessAndModificationTimeForDirectory()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $this->assertTrue(touch($this->fooURL, 303, 313));
        $this->assertEquals(303, $this->foo->filemtime());
        $this->assertEquals(313, $this->foo->fileatime());
    }
}
?>