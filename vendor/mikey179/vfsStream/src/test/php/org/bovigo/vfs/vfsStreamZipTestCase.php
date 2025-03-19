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
 * Test for org\bovigo\vfs\vfsStreamWrapper in conjunction with ext/zip.
 *
 * @group  zip
 */
class vfsStreamZipTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('zip') === false) {
            $this->markTestSkipped('No ext/zip installed, skipping test.');
        }

        $this->markTestSkipped('Zip extension can not work with vfsStream urls.');

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(vfsStream::newDirectory('root'));

    }

    /**
     * @test
     */
    public function createZipArchive()
    {
        $zip = new ZipArchive();
        $this->assertTrue($zip->open(vfsStream::url('root/test.zip'), ZIPARCHIVE::CREATE));
        $this->assertTrue($zip->addFromString("testfile1.txt", "#1 This is a test string added as testfile1.txt.\n"));
        $this->assertTrue($zip->addFromString("testfile2.txt", "#2 This is a test string added as testfile2.txt.\n"));
        $zip->setArchiveComment('a test');
        var_dump($zip);
        $this->assertTrue($zip->close());
        var_dump($zip->getStatusString());
        var_dump($zip->close());
        var_dump($zip->getStatusString());
        var_dump($zip);
        var_dump(file_exists(vfsStream::url('root/test.zip')));
    }
}
?>