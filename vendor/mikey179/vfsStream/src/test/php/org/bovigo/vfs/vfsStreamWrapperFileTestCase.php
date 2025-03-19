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
class vfsStreamWrapperFileTestCase extends vfsStreamWrapperBaseTestCase
{
    /**
     * assert that file_get_contents() delivers correct file contents
     *
     * @test
     */
    public function file_get_contents()
    {
        $this->assertEquals('baz2', file_get_contents($this->baz2URL));
        $this->assertEquals('baz 1', file_get_contents($this->baz1URL));
        $this->assertFalse(@file_get_contents($this->barURL));
        $this->assertFalse(@file_get_contents($this->fooURL));
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function file_get_contentsNonReadableFile()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root'));
        vfsStream::newFile('new.txt', 0000)->at(vfsStreamWrapper::getRoot())->withContent('content');
        $this->assertEquals('', @file_get_contents(vfsStream::url('root/new.txt')));
    }

    /**
     * assert that file_put_contents() delivers correct file contents
     *
     * @test
     */
    public function file_put_contentsExistingFile()
    {
        $this->assertEquals(14, file_put_contents($this->baz2URL, 'baz is not bar'));
        $this->assertEquals('baz is not bar', $this->baz2->getContent());
        $this->assertEquals(6, file_put_contents($this->baz1URL, 'foobar'));
        $this->assertEquals('foobar', $this->baz1->getContent());
        $this->assertFalse(@file_put_contents($this->barURL, 'This does not work.'));
        $this->assertFalse(@file_put_contents($this->fooURL, 'This does not work, too.'));
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function file_put_contentsExistingFileNonWritableDirectory()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root', 0000));
        vfsStream::newFile('new.txt')->at(vfsStreamWrapper::getRoot())->withContent('content');
        $this->assertEquals(15, @file_put_contents(vfsStream::url('root/new.txt'), 'This does work.'));
        $this->assertEquals('This does work.', file_get_contents(vfsStream::url('root/new.txt')));

    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function file_put_contentsExistingNonWritableFile()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root'));
        vfsStream::newFile('new.txt', 0400)->at(vfsStreamWrapper::getRoot())->withContent('content');
        $this->assertFalse(@file_put_contents(vfsStream::url('root/new.txt'), 'This does not work.'));
        $this->assertEquals('content', file_get_contents(vfsStream::url('root/new.txt')));
    }

    /**
     * assert that file_put_contents() delivers correct file contents
     *
     * @test
     */
    public function file_put_contentsNonExistingFile()
    {
        $this->assertEquals(14, file_put_contents($this->fooURL . '/baznot.bar', 'baz is not bar'));
        $this->assertEquals(3, count($this->foo->getChildren()));
        $this->assertEquals(14, file_put_contents($this->barURL . '/baznot.bar', 'baz is not bar'));
        $this->assertEquals(2, count($this->bar->getChildren()));
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function file_put_contentsNonExistingFileNonWritableDirectory()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root', 0000));
        $this->assertFalse(@file_put_contents(vfsStream::url('root/new.txt'), 'This does not work.'));
        $this->assertFalse(file_exists(vfsStream::url('root/new.txt')));

    }

    /**
     * using a file pointer should work without any problems
     *
     * @test
     */
    public function usingFilePointer()
    {
        $fp = fopen($this->baz1URL, 'r');
        $this->assertEquals(0, ftell($fp));
        $this->assertFalse(feof($fp));
        $this->assertEquals(0, fseek($fp, 2));
        $this->assertEquals(2, ftell($fp));
        $this->assertEquals(0, fseek($fp, 1, SEEK_CUR));
        $this->assertEquals(3, ftell($fp));
        $this->assertEquals(0, fseek($fp, 1, SEEK_END));
        $this->assertEquals(6, ftell($fp));
        $this->assertTrue(feof($fp));
        $this->assertEquals(0, fseek($fp, 2));
        $this->assertFalse(feof($fp));
        $this->assertEquals(2, ftell($fp));
        $this->assertEquals('z', fread($fp, 1));
        $this->assertEquals(3, ftell($fp));
        $this->assertEquals(' 1', fread($fp, 8092));
        $this->assertEquals(5, ftell($fp));
        $this->assertTrue(fclose($fp));
    }

    /**
     * assert is_file() returns correct result
     *
     * @test
     */
    public function is_file()
    {
        $this->assertFalse(is_file($this->fooURL));
        $this->assertFalse(is_file($this->barURL));
        $this->assertTrue(is_file($this->baz1URL));
        $this->assertTrue(is_file($this->baz2URL));
        $this->assertFalse(is_file($this->fooURL . '/another'));
        $this->assertFalse(is_file(vfsStream::url('another')));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function issue13CanNotOverwriteFiles()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        file_put_contents($vfsFile, 'd');
        $this->assertEquals('d', file_get_contents($vfsFile));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function appendContentIfOpenedWithModeA()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        $fp = fopen($vfsFile, 'ab');
        fwrite($fp, 'd');
        fclose($fp);
        $this->assertEquals('testd', file_get_contents($vfsFile));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canOverwriteNonExistingFileWithModeX()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        $fp = fopen($vfsFile, 'xb');
        fwrite($fp, 'test');
        fclose($fp);
        $this->assertEquals('test', file_get_contents($vfsFile));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotOverwriteExistingFileWithModeX()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        $this->assertFalse(@fopen($vfsFile, 'xb'));
        $this->assertEquals('test', file_get_contents($vfsFile));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotOpenNonExistingFileReadonly()
    {
        $this->assertFalse(@fopen(vfsStream::url('foo/doesNotExist.txt'), 'rb'));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotOpenNonExistingFileReadAndWrite()
    {
        $this->assertFalse(@fopen(vfsStream::url('foo/doesNotExist.txt'), 'rb+'));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotOpenWithIllegalMode()
    {
        $this->assertFalse(@fopen($this->baz2URL, 'invalid'));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotWriteToReadOnlyFile()
    {
        $fp = fopen($this->baz2URL, 'rb');
        $this->assertEquals('baz2', fread($fp, 4096));
        $this->assertEquals(0, fwrite($fp, 'foo'));
        fclose($fp);
        $this->assertEquals('baz2', file_get_contents($this->baz2URL));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotReadFromWriteOnlyFileWithModeW()
    {
        $fp = fopen($this->baz2URL, 'wb');
        $this->assertEquals('', fread($fp, 4096));
        $this->assertEquals(3, fwrite($fp, 'foo'));
        fseek($fp, 0);
        $this->assertEquals('', fread($fp, 4096));
        fclose($fp);
        $this->assertEquals('foo', file_get_contents($this->baz2URL));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotReadFromWriteOnlyFileWithModeA()
    {
        $fp = fopen($this->baz2URL, 'ab');
        $this->assertEquals('', fread($fp, 4096));
        $this->assertEquals(3, fwrite($fp, 'foo'));
        fseek($fp, 0);
        $this->assertEquals('', fread($fp, 4096));
        fclose($fp);
        $this->assertEquals('baz2foo', file_get_contents($this->baz2URL));
    }

    /**
     * @test
     * @group  issue7
     * @group  issue13
     */
    public function canNotReadFromWriteOnlyFileWithModeX()
    {
        $vfsFile = vfsStream::url('foo/modeXtest.txt');
        $fp = fopen($vfsFile, 'xb');
        $this->assertEquals('', fread($fp, 4096));
        $this->assertEquals(3, fwrite($fp, 'foo'));
        fseek($fp, 0);
        $this->assertEquals('', fread($fp, 4096));
        fclose($fp);
        $this->assertEquals('foo', file_get_contents($vfsFile));
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function canNotRemoveFileWithoutWritePermissions()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root'));
        vfsStream::newFile('new.txt', 0000)->at(vfsStreamWrapper::getRoot());
        $this->assertFalse(unlink(vfsStream::url('root/new.txt')));
        $this->assertTrue(file_exists(vfsStream::url('root/new.txt')));
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function canNotRemoveFileFromDirectoryWithoutWritePermissions()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root', 0000));
        vfsStream::newFile('new.txt')->at(vfsStreamWrapper::getRoot());
        $this->assertFalse(unlink(vfsStream::url('root/new.txt')));
        $this->assertTrue(file_exists(vfsStream::url('root/new.txt')));
    }

    /**
     * @test
     * @group  issue_30
     */
    public function truncatesFileWhenOpenedWithModeW()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        $fp = fopen($vfsFile, 'wb');
        $this->assertEquals('', file_get_contents($vfsFile));
        fclose($fp);
    }

    /**
     * @test
     * @group  issue_30
     */
    public function createsNonExistingFileWhenOpenedWithModeC()
    {
        $vfsFile = vfsStream::url('foo/tobecreated.txt');
        $fp = fopen($vfsFile, 'cb');
        fwrite($fp, 'some content');
        $this->assertTrue($this->foo->hasChild('tobecreated.txt'));
        fclose($fp);
        $this->assertEquals('some content', file_get_contents($vfsFile));
    }

    /**
     * @test
     * @group  issue_30
     */
    public function createsNonExistingFileWhenOpenedWithModeCplus()
    {
        $vfsFile = vfsStream::url('foo/tobecreated.txt');
        $fp = fopen($vfsFile, 'cb+');
        fwrite($fp, 'some content');
        $this->assertTrue($this->foo->hasChild('tobecreated.txt'));
        fclose($fp);
        $this->assertEquals('some content', file_get_contents($vfsFile));
    }

    /**
     * @test
     * @group  issue_30
     */
    public function doesNotTruncateFileWhenOpenedWithModeC()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        $fp = fopen($vfsFile, 'cb');
        $this->assertEquals('test', file_get_contents($vfsFile));
        fclose($fp);
    }

    /**
     * @test
     * @group  issue_30
     */
    public function setsPointerToStartWhenOpenedWithModeC()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        $fp = fopen($vfsFile, 'cb');
        $this->assertEquals(0, ftell($fp));
        fclose($fp);
    }

    /**
     * @test
     * @group  issue_30
     */
    public function doesNotTruncateFileWhenOpenedWithModeCplus()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        $fp = fopen($vfsFile, 'cb+');
        $this->assertEquals('test', file_get_contents($vfsFile));
        fclose($fp);
    }

    /**
     * @test
     * @group  issue_30
     */
    public function setsPointerToStartWhenOpenedWithModeCplus()
    {
        $vfsFile = vfsStream::url('foo/overwrite.txt');
        file_put_contents($vfsFile, 'test');
        $fp = fopen($vfsFile, 'cb+');
        $this->assertEquals(0, ftell($fp));
        fclose($fp);
    }

    /**
     * @test
     */
    public function cannotOpenExistingNonwritableFileWithModeA()
    {
        $this->baz1->chmod(0400);
        $this->assertFalse(@fopen($this->baz1URL, 'a'));
    }

    /**
     * @test
     */
    public function cannotOpenExistingNonwritableFileWithModeW()
    {
        $this->baz1->chmod(0400);
        $this->assertFalse(@fopen($this->baz1URL, 'w'));
    }

    /**
     * @test
     */
    public function cannotOpenNonReadableFileWithModeR()
    {
        $this->baz1->chmod(0);
        $this->assertFalse(@fopen($this->baz1URL, 'r'));
    }

    /**
     * @test
     */
    public function cannotRenameToNonWritableDir()
    {
        $this->bar->chmod(0);
        $this->assertFalse(@rename($this->baz2URL, vfsStream::url('foo/bar/baz3')));
    }

    /**
     * @test
     * @group issue_38
     */
    public function cannotReadFileFromNonReadableDir()
    {
        $this->markTestSkipped("Issue #38.");
        $this->bar->chmod(0);
        $this->assertFalse(@file_get_contents($this->baz1URL));
    }
}
?>