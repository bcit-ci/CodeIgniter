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
 * Test for quota related functionality of org\bovigo\vfs\vfsStreamWrapper.
 *
 * @group  issue_35
 */
class vfsStreamWrapperQuotaTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * access to root
     *
     * @type  vfsStreamDirectory
     */
    private $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup();
        vfsStream::setQuota(10);
    }

    /**
     * @test
     */
    public function writeLessThanQuotaWritesEverything()
    {
        $this->assertEquals(9, file_put_contents(vfsStream::url('root/file.txt'), '123456789'));
        $this->assertEquals('123456789', $this->root->getChild('file.txt')->getContent());
    }

    /**
     * @test
     */
    public function writeUpToQotaWritesEverything()
    {
        $this->assertEquals(10, file_put_contents(vfsStream::url('root/file.txt'), '1234567890'));
        $this->assertEquals('1234567890', $this->root->getChild('file.txt')->getContent());
    }

    /**
     * @test
     */
    public function writeMoreThanQotaWritesOnlyUpToQuota()
    {
        try {
            file_put_contents(vfsStream::url('root/file.txt'), '12345678901');
        } catch (\PHPUnit_Framework_Error $e) {
            $this->assertEquals('file_put_contents(): Only 10 of 11 bytes written, possibly out of free disk space',
                                $e->getMessage()
            );
        }

        $this->assertEquals('1234567890', $this->root->getChild('file.txt')->getContent());
    }

    /**
     * @test
     */
    public function considersAllFilesForQuota()
    {
        vfsStream::newFile('foo.txt')
                 ->withContent('foo')
                 ->at(vfsStream::newDirectory('bar')
                               ->at($this->root)
                   );
        try {
            file_put_contents(vfsStream::url('root/file.txt'), '12345678901');
        } catch (\PHPUnit_Framework_Error $e) {
            $this->assertEquals('file_put_contents(): Only 7 of 11 bytes written, possibly out of free disk space',
                                $e->getMessage()
            );
        }

        $this->assertEquals('1234567', $this->root->getChild('file.txt')->getContent());
    }

    /**
     * @test
     * @group  issue_33
     */
    public function truncateToLessThanQuotaWritesEverything()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $fp = fopen(vfsStream::url('root/file.txt'), 'w+');
        $this->assertTrue(ftruncate($fp, 9));
        fclose($fp);
        $this->assertEquals(9,
                            $this->root->getChild('file.txt')->size()
        );
        $this->assertEquals("\0\0\0\0\0\0\0\0\0",
                            $this->root->getChild('file.txt')->getContent()
        );
    }

    /**
     * @test
     * @group  issue_33
     */
    public function truncateUpToQotaWritesEverything()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $fp = fopen(vfsStream::url('root/file.txt'), 'w+');
        $this->assertTrue(ftruncate($fp, 10));
        fclose($fp);
        $this->assertEquals(10,
                            $this->root->getChild('file.txt')->size()
        );
        $this->assertEquals("\0\0\0\0\0\0\0\0\0\0",
                            $this->root->getChild('file.txt')->getContent()
        );
    }

    /**
     * @test
     * @group  issue_33
     */
    public function truncateToMoreThanQotaWritesOnlyUpToQuota()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        $fp = fopen(vfsStream::url('root/file.txt'), 'w+');
        $this->assertTrue(ftruncate($fp, 11));
        fclose($fp);
        $this->assertEquals(10,
                            $this->root->getChild('file.txt')->size()
        );
        $this->assertEquals("\0\0\0\0\0\0\0\0\0\0",
                            $this->root->getChild('file.txt')->getContent()
        );
    }

    /**
     * @test
     * @group  issue_33
     */
    public function truncateConsidersAllFilesForQuota()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        vfsStream::newFile('bar.txt')
                 ->withContent('bar')
                 ->at(vfsStream::newDirectory('bar')
                               ->at($this->root)
                   );
        $fp = fopen(vfsStream::url('root/file.txt'), 'w+');
        $this->assertTrue(ftruncate($fp, 11));
        fclose($fp);
        $this->assertEquals(7,
                            $this->root->getChild('file.txt')->size()
        );
        $this->assertEquals("\0\0\0\0\0\0\0",
                            $this->root->getChild('file.txt')->getContent()
        );
    }

    /**
     * @test
     * @group  issue_33
     */
    public function canNotTruncateToGreaterLengthWhenDiscQuotaReached()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Requires PHP 5.4');
        }

        vfsStream::newFile('bar.txt')
                 ->withContent('1234567890')
                 ->at(vfsStream::newDirectory('bar')
                               ->at($this->root)
                   );
        $fp = fopen(vfsStream::url('root/file.txt'), 'w+');
        $this->assertFalse(ftruncate($fp, 11));
        fclose($fp);
        $this->assertEquals(0,
                            $this->root->getChild('file.txt')->size()
        );
        $this->assertEquals('',
                            $this->root->getChild('file.txt')->getContent()
        );
    }
}
?>