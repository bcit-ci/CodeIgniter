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
 * Test for org\bovigo\vfs\vfsStream.
 */
class vfsStreamTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        vfsStreamWrapper::register();
    }

    /**
     * assure that path2url conversion works correct
     *
     * @test
     */
    public function url()
    {
        $this->assertEquals('vfs://foo', vfsStream::url('foo'));
        $this->assertEquals('vfs://foo/bar.baz', vfsStream::url('foo/bar.baz'));
        $this->assertEquals('vfs://foo/bar.baz', vfsStream::url('foo\bar.baz'));
    }

    /**
     * assure that url2path conversion works correct
     *
     * @test
     */
    public function path()
    {
        $this->assertEquals('foo', vfsStream::path('vfs://foo'));
        $this->assertEquals('foo/bar.baz', vfsStream::path('vfs://foo/bar.baz'));
        $this->assertEquals('foo/bar.baz', vfsStream::path('vfs://foo\bar.baz'));
    }

    /**
     * windows directory separators are converted into default separator
     *
     * @author  Gabriel Birke
     * @test
     */
    public function pathConvertsWindowsDirectorySeparators()
    {
        $this->assertEquals('foo/bar', vfsStream::path('vfs://foo\\bar'));
    }

    /**
     * trailing whitespace should be removed
     *
     * @author  Gabriel Birke
     * @test
     */
    public function pathRemovesTrailingWhitespace()
    {
        $this->assertEquals('foo/bar', vfsStream::path('vfs://foo/bar '));
    }

    /**
     * trailing slashes are removed
     *
     * @author  Gabriel Birke
     * @test
     */
    public function pathRemovesTrailingSlash()
    {
        $this->assertEquals('foo/bar', vfsStream::path('vfs://foo/bar/'));
    }

    /**
     * trailing slash and whitespace should be removed
     *
     * @author  Gabriel Birke
     * @test
     */
    public function pathRemovesTrailingSlashAndWhitespace()
    {
        $this->assertEquals('foo/bar', vfsStream::path('vfs://foo/bar/ '));
    }

    /**
     * double slashes should be replaced by single slash
     *
     * @author  Gabriel Birke
     * @test
     */
    public function pathRemovesDoubleSlashes()
    {
        // Regular path
        $this->assertEquals('my/path', vfsStream::path('vfs://my/path'));
        // Path with double slashes
        $this->assertEquals('my/path', vfsStream::path('vfs://my//path'));
    }

    /**
     * test to create a new file
     *
     * @test
     */
    public function newFile()
    {
        $file = vfsStream::newFile('filename.txt');
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamFile', $file);
        $this->assertEquals('filename.txt', $file->getName());
        $this->assertEquals(0666, $file->getPermissions());
    }

    /**
     * test to create a new file with non-default permissions
     *
     * @test
     * @group  permissions
     */
    public function newFileWithDifferentPermissions()
    {
        $file = vfsStream::newFile('filename.txt', 0644);
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamFile', $file);
        $this->assertEquals('filename.txt', $file->getName());
        $this->assertEquals(0644, $file->getPermissions());
    }

    /**
     * test to create a new directory structure
     *
     * @test
     */
    public function newSingleDirectory()
    {
        $foo = vfsStream::newDirectory('foo');
        $this->assertEquals('foo', $foo->getName());
        $this->assertEquals(0, count($foo->getChildren()));
        $this->assertEquals(0777, $foo->getPermissions());
    }

    /**
     * test to create a new directory structure with non-default permissions
     *
     * @test
     * @group  permissions
     */
    public function newSingleDirectoryWithDifferentPermissions()
    {
        $foo = vfsStream::newDirectory('foo', 0755);
        $this->assertEquals('foo', $foo->getName());
        $this->assertEquals(0, count($foo->getChildren()));
        $this->assertEquals(0755, $foo->getPermissions());
    }

    /**
     * test to create a new directory structure
     *
     * @test
     */
    public function newDirectoryStructure()
    {
        $foo = vfsStream::newDirectory('foo/bar/baz');
        $this->assertEquals('foo', $foo->getName());
        $this->assertEquals(0777, $foo->getPermissions());
        $this->assertTrue($foo->hasChild('bar'));
        $this->assertTrue($foo->hasChild('bar/baz'));
        $this->assertFalse($foo->hasChild('baz'));
        $bar = $foo->getChild('bar');
        $this->assertEquals('bar', $bar->getName());
        $this->assertEquals(0777, $bar->getPermissions());
        $this->assertTrue($bar->hasChild('baz'));
        $baz1 = $bar->getChild('baz');
        $this->assertEquals('baz', $baz1->getName());
        $this->assertEquals(0777, $baz1->getPermissions());
        $baz2 = $foo->getChild('bar/baz');
        $this->assertSame($baz1, $baz2);
    }

    /**
     * test that correct directory structure is created
     *
     * @test
     */
    public function newDirectoryWithSlashAtStart()
    {
        $foo = vfsStream::newDirectory('/foo/bar/baz', 0755);
        $this->assertEquals('foo', $foo->getName());
        $this->assertEquals(0755, $foo->getPermissions());
        $this->assertTrue($foo->hasChild('bar'));
        $this->assertTrue($foo->hasChild('bar/baz'));
        $this->assertFalse($foo->hasChild('baz'));
        $bar = $foo->getChild('bar');
        $this->assertEquals('bar', $bar->getName());
        $this->assertEquals(0755, $bar->getPermissions());
        $this->assertTrue($bar->hasChild('baz'));
        $baz1 = $bar->getChild('baz');
        $this->assertEquals('baz', $baz1->getName());
        $this->assertEquals(0755, $baz1->getPermissions());
        $baz2 = $foo->getChild('bar/baz');
        $this->assertSame($baz1, $baz2);
    }

    /**
     * @test
     * @group  setup
     * @since  0.7.0
     */
    public function setupRegistersStreamWrapperAndCreatesRootDirectoryWithDefaultNameAndPermissions()
    {
        $root = vfsStream::setup();
        $this->assertSame($root, vfsStreamWrapper::getRoot());
        $this->assertEquals('root', $root->getName());
        $this->assertEquals(0777, $root->getPermissions());
    }

    /**
     * @test
     * @group  setup
     * @since  0.7.0
     */
    public function setupRegistersStreamWrapperAndCreatesRootDirectoryWithGivenNameAndDefaultPermissions()
    {
        $root = vfsStream::setup('foo');
        $this->assertSame($root, vfsStreamWrapper::getRoot());
        $this->assertEquals('foo', $root->getName());
        $this->assertEquals(0777, $root->getPermissions());
    }

    /**
     * @test
     * @group  setup
     * @since  0.7.0
     */
    public function setupRegistersStreamWrapperAndCreatesRootDirectoryWithGivenNameAndPermissions()
    {
        $root = vfsStream::setup('foo', 0444);
        $this->assertSame($root, vfsStreamWrapper::getRoot());
        $this->assertEquals('foo', $root->getName());
        $this->assertEquals(0444, $root->getPermissions());
    }

    /**
     * @test
     * @group  issue_14
     * @group  issue_20
     * @since  0.10.0
     */
    public function setupWithEmptyArrayIsEqualToSetup()
    {
        $root = vfsStream::setup('example',
                                 0755,
                                 array()
                );
        $this->assertEquals('example', $root->getName());
        $this->assertEquals(0755, $root->getPermissions());
        $this->assertFalse($root->hasChildren());
    }

    /**
     * @test
     * @group  issue_14
     * @group  issue_20
     * @since  0.10.0
     */
    public function setupArraysAreTurnedIntoSubdirectories()
    {
        $root = vfsStream::setup('root',
                                 null,
                                 array('test' => array())
                );
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($root->hasChild('test'));
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory',
                                $root->getChild('test')
        );
        $this->assertFalse($root->getChild('test')->hasChildren());
    }

    /**
     * @test
     * @group  issue_14
     * @group  issue_20
     * @since  0.10.0
     */
    public function setupStringsAreTurnedIntoFilesWithContent()
    {
        $root = vfsStream::setup('root',
                                 null,
                                 array('test.txt' => 'some content')
                );
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($root->hasChild('test.txt'));
        $this->assertVfsFile($root->getChild('test.txt'), 'some content');
    }

    /**
     * @test
     * @group  issue_14
     * @group  issue_20
     * @since  0.10.0
     */
    public function setupWorksRecursively()
    {
        $root = vfsStream::setup('root',
                                 null,
                                 array('test' => array('foo'     => array('test.txt' => 'hello'),
                                                       'baz.txt' => 'world'
                                                 )
                                 )
                );
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($root->hasChild('test'));
        $test = $root->getChild('test');
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory', $test);
        $this->assertTrue($test->hasChildren());
        $this->assertTrue($test->hasChild('baz.txt'));
        $this->assertVfsFile($test->getChild('baz.txt'), 'world');

        $this->assertTrue($test->hasChild('foo'));
        $foo = $test->getChild('foo');
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory', $foo);
        $this->assertTrue($foo->hasChildren());
        $this->assertTrue($foo->hasChild('test.txt'));
        $this->assertVfsFile($foo->getChild('test.txt'), 'hello');
    }

    /**
    * @test
    * @group  issue_17
    * @group  issue_20
    */
    public function setupCastsNumericDirectoriesToStrings()
    {
        $root = vfsStream::setup('root',
                                 null,
                                 array(2011 => array ('test.txt' => 'some content'))
                );
        $this->assertTrue($root->hasChild('2011'));

        $directory = $root->getChild('2011');
        $this->assertVfsFile($directory->getChild('test.txt'), 'some content');

        $this->assertTrue(file_exists('vfs://2011/test.txt'));
    }

    /**
     * @test
     * @group  issue_20
     * @since  0.11.0
     */
    public function createArraysAreTurnedIntoSubdirectories()
    {
        $baseDir = vfsStream::create(array('test' => array()), new vfsStreamDirectory('baseDir'));
        $this->assertTrue($baseDir->hasChildren());
        $this->assertTrue($baseDir->hasChild('test'));
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory',
                                $baseDir->getChild('test')
        );
        $this->assertFalse($baseDir->getChild('test')->hasChildren());
    }

    /**
     * @test
     * @group  issue_20
     * @since  0.11.0
     */
    public function createArraysAreTurnedIntoSubdirectoriesOfRoot()
    {
        $root = vfsStream::setup();
        $this->assertSame($root, vfsStream::create(array('test' => array())));
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($root->hasChild('test'));
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory',
                                $root->getChild('test')
        );
        $this->assertFalse($root->getChild('test')->hasChildren());
    }

    /**
     * @test
     * @group  issue_20
     * @expectedException  \InvalidArgumentException
     * @since  0.11.0
     */
    public function createThrowsExceptionIfNoBaseDirGivenAndNoRootSet()
    {
        vfsStream::create(array('test' => array()));
    }

    /**
     * @test
     * @group  issue_20
     * @since  0.11.0
     */
    public function createWorksRecursively()
    {
        $baseDir = vfsStream::create(array('test' => array('foo'     => array('test.txt' => 'hello'),
                                                           'baz.txt' => 'world'
                                                     )
                                     ),
                                     new vfsStreamDirectory('baseDir')
                   );
        $this->assertTrue($baseDir->hasChildren());
        $this->assertTrue($baseDir->hasChild('test'));
        $test = $baseDir->getChild('test');
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory', $test);
        $this->assertTrue($test->hasChildren());
        $this->assertTrue($test->hasChild('baz.txt'));
        $this->assertVfsFile($test->getChild('baz.txt'), 'world');

        $this->assertTrue($test->hasChild('foo'));
        $foo = $test->getChild('foo');
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory', $foo);
        $this->assertTrue($foo->hasChildren());
        $this->assertTrue($foo->hasChild('test.txt'));
        $this->assertVfsFile($foo->getChild('test.txt'), 'hello');
    }

    /**
     * @test
     * @group  issue_20
     * @since  0.11.0
     */
    public function createWorksRecursivelyWithRoot()
    {
        $root = vfsStream::setup();
        $this->assertSame($root,
                          vfsStream::create(array('test' => array('foo'     => array('test.txt' => 'hello'),
                                                                  'baz.txt' => 'world'
                                                            )
                                            )
                          )
        );
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($root->hasChild('test'));
        $test = $root->getChild('test');
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory', $test);
        $this->assertTrue($test->hasChildren());
        $this->assertTrue($test->hasChild('baz.txt'));
        $this->assertVfsFile($test->getChild('baz.txt'), 'world');

        $this->assertTrue($test->hasChild('foo'));
        $foo = $test->getChild('foo');
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamDirectory', $foo);
        $this->assertTrue($foo->hasChildren());
        $this->assertTrue($foo->hasChild('test.txt'));
        $this->assertVfsFile($foo->getChild('test.txt'), 'hello');
    }

    /**
     * @test
     * @group  issue_20
     * @since  0.10.0
     */
    public function createStringsAreTurnedIntoFilesWithContent()
    {
        $baseDir = vfsStream::create(array('test.txt' => 'some content'), new vfsStreamDirectory('baseDir'));
        $this->assertTrue($baseDir->hasChildren());
        $this->assertTrue($baseDir->hasChild('test.txt'));
        $this->assertVfsFile($baseDir->getChild('test.txt'), 'some content');
    }

    /**
     * @test
     * @group  issue_20
     * @since  0.11.0
     */
    public function createStringsAreTurnedIntoFilesWithContentWithRoot()
    {
        $root = vfsStream::setup();
        $this->assertSame($root,
                          vfsStream::create(array('test.txt' => 'some content'))
        );
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($root->hasChild('test.txt'));
        $this->assertVfsFile($root->getChild('test.txt'), 'some content');
    }

    /**
    * @test
    * @group  issue_20
    * @since  0.11.0
    */
    public function createCastsNumericDirectoriesToStrings()
    {
        $baseDir = vfsStream::create(array(2011 => array ('test.txt' => 'some content')), new vfsStreamDirectory('baseDir'));
        $this->assertTrue($baseDir->hasChild('2011'));

        $directory = $baseDir->getChild('2011');
        $this->assertVfsFile($directory->getChild('test.txt'), 'some content');
    }

    /**
    * @test
    * @group  issue_20
    * @since  0.11.0
    */
    public function createCastsNumericDirectoriesToStringsWithRoot()
    {
        $root = vfsStream::setup();
        $this->assertSame($root,
                          vfsStream::create(array(2011 => array ('test.txt' => 'some content')))
        );
        $this->assertTrue($root->hasChild('2011'));

        $directory = $root->getChild('2011');
        $this->assertVfsFile($directory->getChild('test.txt'), 'some content');
    }

    /**
     * helper function for assertions on vfsStreamFile
     *
     * @param  vfsStreamFile  $file
     * @param  string         $content
     */
    protected function assertVfsFile(vfsStreamFile $file, $content)
    {
        $this->assertInstanceOf('org\\bovigo\\vfs\\vfsStreamFile',
                                $file
        );
        $this->assertEquals($content,
                            $file->getContent()
        );
    }

    /**
     * @test
     * @group  issue_10
     * @since  0.10.0
     */
    public function inspectWithContentGivesContentToVisitor()
    {
        $mockContent = $this->getMock('org\\bovigo\\vfs\\vfsStreamContent');
        $mockVisitor = $this->getMock('org\\bovigo\\vfs\\visitor\\vfsStreamVisitor');
        $mockVisitor->expects($this->once())
                    ->method('visit')
                    ->with($this->equalTo($mockContent))
                    ->will($this->returnValue($mockVisitor));
        $this->assertSame($mockVisitor, vfsStream::inspect($mockVisitor, $mockContent));
    }

    /**
     * @test
     * @group  issue_10
     * @since  0.10.0
     */
    public function inspectWithoutContentGivesRootToVisitor()
    {
        $root = vfsStream::setup();
        $mockVisitor = $this->getMock('org\\bovigo\\vfs\\visitor\\vfsStreamVisitor');
        $mockVisitor->expects($this->once())
                    ->method('visitDirectory')
                    ->with($this->equalTo($root))
                    ->will($this->returnValue($mockVisitor));
        $this->assertSame($mockVisitor, vfsStream::inspect($mockVisitor));
    }

    /**
     * @test
     * @group  issue_10
     * @expectedException  \InvalidArgumentException
     * @since  0.10.0
     */
    public function inspectWithoutContentAndWithoutRootThrowsInvalidArgumentException()
    {
        $mockVisitor = $this->getMock('org\\bovigo\\vfs\\visitor\\vfsStreamVisitor');
        $mockVisitor->expects($this->never())
                    ->method('visit');
        $mockVisitor->expects($this->never())
                    ->method('visitDirectory');
        vfsStream::inspect($mockVisitor);
    }

    /**
     * returns path to file system copy resource directory
     *
     * @return  string
     */
    protected function getFileSystemCopyDir()
    {
        return realpath(dirname(__FILE__) . '/../../../../resources/filesystemcopy');
    }

    /**
     * @test
     * @group  issue_4
     * @expectedException  \InvalidArgumentException
     * @since  0.11.0
     */
    public function copyFromFileSystemThrowsExceptionIfNoBaseDirGivenAndNoRootSet()
    {
        vfsStream::copyFromFileSystem($this->getFileSystemCopyDir());
    }

    /**
     * @test
     * @group  issue_4
     * @since  0.11.0
     */
    public function copyFromEmptyFolder()
    {
        $baseDir = vfsStream::copyFromFileSystem($this->getFileSystemCopyDir() . '/emptyFolder',
                                                 vfsStream::newDirectory('test')
                   );
        $baseDir->removeChild('.gitignore');
        $this->assertFalse($baseDir->hasChildren());
    }

    /**
     * @test
     * @group  issue_4
     * @since  0.11.0
     */
    public function copyFromEmptyFolderWithRoot()
    {
        $root = vfsStream::setup();
        $this->assertEquals($root,
                            vfsStream::copyFromFileSystem($this->getFileSystemCopyDir() . '/emptyFolder')
        );
        $root->removeChild('.gitignore');
        $this->assertFalse($root->hasChildren());
    }

    /**
     * @test
     * @group  issue_4
     * @since  0.11.0
     */
    public function copyFromWithSubFolders()
    {
        $baseDir = vfsStream::copyFromFileSystem($this->getFileSystemCopyDir(),
                                                 vfsStream::newDirectory('test'),
                                                 3
                   );
        $this->assertTrue($baseDir->hasChildren());
        $this->assertTrue($baseDir->hasChild('emptyFolder'));
        $this->assertTrue($baseDir->hasChild('withSubfolders'));
        $subfolderDir = $baseDir->getChild('withSubfolders');
        $this->assertTrue($subfolderDir->hasChild('subfolder1'));
        $this->assertTrue($subfolderDir->getChild('subfolder1')->hasChild('file1.txt'));
        $this->assertVfsFile($subfolderDir->getChild('subfolder1/file1.txt'), '');
        $this->assertTrue($subfolderDir->hasChild('subfolder2'));
        $this->assertTrue($subfolderDir->hasChild('aFile.txt'));
        $this->assertVfsFile($subfolderDir->getChild('aFile.txt'), 'foo');
    }

    /**
     * @test
     * @group  issue_4
     * @since  0.11.0
     */
    public function copyFromWithSubFoldersWithRoot()
    {
        $root = vfsStream::setup();
        $this->assertEquals($root,
                            vfsStream::copyFromFileSystem($this->getFileSystemCopyDir(),
                                                          null,
                                                          3
                            )
        );
        $this->assertTrue($root->hasChildren());
        $this->assertTrue($root->hasChild('emptyFolder'));
        $this->assertTrue($root->hasChild('withSubfolders'));
        $subfolderDir = $root->getChild('withSubfolders');
        $this->assertTrue($subfolderDir->hasChild('subfolder1'));
        $this->assertTrue($subfolderDir->getChild('subfolder1')->hasChild('file1.txt'));
        $this->assertVfsFile($subfolderDir->getChild('subfolder1/file1.txt'), '');
        $this->assertTrue($subfolderDir->hasChild('subfolder2'));
        $this->assertTrue($subfolderDir->hasChild('aFile.txt'));
        $this->assertVfsFile($subfolderDir->getChild('aFile.txt'), 'foo');
    }

    /**
     * @test
     * @group  issue_4
     * @group  issue_29
     * @since  0.11.2
     */
    public function copyFromPreservesFilePermissions()
    {
        if (DIRECTORY_SEPARATOR !== '/') {
            $this->markTestSkipped('Only applicable on Linux style systems.');
        }

        $copyDir = $this->getFileSystemCopyDir();
        $root    = vfsStream::setup();
        $this->assertEquals($root,
                            vfsStream::copyFromFileSystem($copyDir,
                                                          null
                            )
        );
        $this->assertEquals(fileperms($copyDir . '/withSubfolders') - vfsStreamContent::TYPE_DIR,
                            $root->getChild('withSubfolders')
                                 ->getPermissions()
        );
        $this->assertEquals(fileperms($copyDir . '/withSubfolders/aFile.txt') - vfsStreamContent::TYPE_FILE,
                            $root->getChild('withSubfolders/aFile.txt')
                                 ->getPermissions()
        );
    }
}
?>