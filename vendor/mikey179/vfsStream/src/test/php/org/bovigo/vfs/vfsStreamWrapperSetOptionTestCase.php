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
 * Test for stream_set_option() implementation.
 *
 * @since  0.10.0
 * @see    https://github.com/mikey179/vfsStream/issues/15
 * @group  issue_15
 */
class vfsStreamWrapperSetOptionTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * root directory
     *
     * @var  vfsStreamContainer
     */
    protected $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup();
        vfsStream::newFile('foo.txt')->at($this->root);
    }

    /**
     * @test
     */
    public function setBlockingDoesNotWork()
    {
        $fp = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertFalse(stream_set_blocking($fp, 1));
        fclose($fp);
    }

    /**
     * @test
     */
    public function removeBlockingDoesNotWork()
    {
        $fp = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertFalse(stream_set_blocking($fp, 0));
        fclose($fp);
    }

    /**
     * @test
     */
    public function setTimeoutDoesNotWork()
    {
        $fp = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertFalse(stream_set_timeout($fp, 1));
        fclose($fp);
    }

    /**
     * @test
     */
    public function setWriteBufferDoesNotWork()
    {
        $fp = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertEquals(-1, stream_set_write_buffer($fp, 512));
        fclose($fp);
    }
}
?>