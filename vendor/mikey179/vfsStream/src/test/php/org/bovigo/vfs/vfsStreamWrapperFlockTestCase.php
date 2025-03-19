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
 * Test for flock() implementation.
 *
 * @package     bovigo_vfs
 * @subpackage  test
 * @since       0.10.0
 * @see         https://github.com/mikey179/vfsStream/issues/6
 * @group       issue_6
 */
class vfsStreamWrapperFlockTestCase extends \PHPUnit_Framework_TestCase
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
    }

    /**
     * @test
     */
    public function fileIsNotLockedByDefault()
    {
        $this->assertFalse(vfsStream::newFile('foo.txt')->isLocked());
    }

    /**
     * @test
     */
    public function streamIsNotLockedByDefault()
    {
        file_put_contents(vfsStream::url('root/foo.txt'), 'content');
        $this->assertFalse($this->root->getChild('foo.txt')->isLocked());
    }

    /**
     * @test
     */
    public function canAquireSharedLock()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertTrue(flock($fp, LOCK_SH));
        $this->assertTrue($file->isLocked());
        $this->assertTrue($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
        fclose($fp);

    }

    /**
     * @test
     */
    public function canAquireSharedLockWithNonBlockingFlockCall()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertTrue(flock($fp, LOCK_SH | LOCK_NB));
        $this->assertTrue($file->isLocked());
        $this->assertTrue($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
        fclose($fp);

    }

    /**
     * @test
     */
    public function canAquireEclusiveLock()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertTrue(flock($fp, LOCK_EX));
        $this->assertTrue($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertTrue($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @test
     */
    public function canAquireEclusiveLockWithNonBlockingFlockCall()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $this->assertTrue(flock($fp, LOCK_EX | LOCK_NB));
        $this->assertTrue($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertTrue($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @test
     */
    public function canRemoveLock()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $file->lock(LOCK_EX);
        $this->assertTrue(flock($fp, LOCK_UN));
        $this->assertFalse($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @test
     */
    public function canRemoveLockWithNonBlockingFlockCall()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $file->lock(LOCK_EX);
        $this->assertTrue(flock($fp, LOCK_UN | LOCK_NB));
        $this->assertFalse($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @see    https://github.com/mikey179/vfsStream/issues/31
     * @test
     * @group  issue_31
     */
    public function canNotAquireExclusiveLockIfAlreadyExclusivelyLocked()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $file->lock(LOCK_EX);
        $this->assertFalse(flock($fp, LOCK_EX + LOCK_NB));
        $this->assertTrue($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertTrue($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @see    https://github.com/mikey179/vfsStream/issues/31
     * @test
     * @group  issue_31
     */
    public function canNotAquireExclusiveLockIfAlreadySharedLocked()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $file->lock(LOCK_SH);
        $this->assertFalse(flock($fp, LOCK_EX));
        $this->assertTrue($file->isLocked());
        $this->assertTrue($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @see    https://github.com/mikey179/vfsStream/issues/31
     * @test
     * @group  issue_31
     */
    public function canNotAquireSharedLockIfAlreadyExclusivelyLocked()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $file->lock(LOCK_EX);
        $this->assertFalse(flock($fp, LOCK_SH + LOCK_NB));
        $this->assertTrue($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertTrue($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @see    https://github.com/mikey179/vfsStream/issues/31
     * @test
     * @group  issue_31
     */
    public function canAquireSharedLockIfAlreadySharedLocked()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $fp   = fopen(vfsStream::url('root/foo.txt'), 'rb');
        $file->lock(LOCK_SH);
        $this->assertTrue(flock($fp, LOCK_SH));
        $this->assertTrue($file->isLocked());
        $this->assertTrue($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
        fclose($fp);
    }

    /**
     * @see    https://github.com/mikey179/vfsStream/issues/31
     * @test
     * @group  issue_31
     */
    public function removesExclusiveLockOnStreamClose()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $file->lock(LOCK_EX);
        fclose(fopen(vfsStream::url('root/foo.txt'), 'rb'));
        $this->assertFalse($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
    }

    /**
     * @see    https://github.com/mikey179/vfsStream/issues/31
     * @test
     * @group  issue_31
     */
    public function removesSharedLockOnStreamClose()
    {
        $file = vfsStream::newFile('foo.txt')->at($this->root);
        $file->lock(LOCK_SH);
        fclose(fopen(vfsStream::url('root/foo.txt'), 'rb'));
        $this->assertFalse($file->isLocked());
        $this->assertFalse($file->hasSharedLock());
        $this->assertFalse($file->hasExclusiveLock());
    }
}
?>