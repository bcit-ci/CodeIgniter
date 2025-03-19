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
 * Helper class for the test.
 */
class TestvfsStreamAbstractContent extends vfsStreamAbstractContent
{
    /**
     * returns default permissions for concrete implementation
     *
     * @return  int
     * @since   0.8.0
     */
    protected function getDefaultPermissions()
    {
        return 0777;
    }

    /**
     * returns size of content
     *
     * @return  int
     */
    public function size()
    {
        return 0;
    }
}
/**
 * Test for org\bovigo\vfs\vfsStreamAbstractContent.
 */
class vfsStreamAbstractContentTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function noPermissionsForEveryone()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0000);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function executePermissionsForUser()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0100);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function executePermissionsForGroup()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0010);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function executePermissionsForOther()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0001);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         -1
                                            )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function writePermissionsForUser()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0200);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function writePermissionsForGroup()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0020);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function writePermissionsForOther()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0002);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function executeAndWritePermissionsForUser()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0300);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function executeAndWritePermissionsForGroup()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0030);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function executeAndWritePermissionsForOther()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0003);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         -1
                                            )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readPermissionsForUser()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0400);
        $this->assertTrue($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readPermissionsForGroup()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0040);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readPermissionsForOther()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0004);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readAndExecutePermissionsForUser()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0500);
        $this->assertTrue($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readAndExecutePermissionsForGroup()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0050);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readAndExecutePermissionsForOther()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0005);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         -1
                                            )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readAndWritePermissionsForUser()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0600);
        $this->assertTrue($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readAndWritePermissionsForGroup()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0060);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function readAndWritePermissionsForOther()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0006);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function allPermissionsForUser()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0700);
        $this->assertTrue($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function allPermissionsForGroup()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0070);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        -1
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         vfsStream::getCurrentGroup()
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          -1
                                             )
               );
    }

    /**
     * @test
     * @group  permissions
     * @group  bug_15
     */
    public function allPermissionsForOther()
    {
        $abstractContent = new TestvfsStreamAbstractContent('foo', 0007);
        $this->assertFalse($abstractContent->isReadable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isReadable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isReadable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isWritable(vfsStream::getCurrentUser(),
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isWritable(-1,
                                                        vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isWritable(-1,
                                                       -1
                                            )
               );
        $this->assertFalse($abstractContent->isExecutable(vfsStream::getCurrentUser(),
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertFalse($abstractContent->isExecutable(-1,
                                                          vfsStream::getCurrentGroup()
                                             )
               );
        $this->assertTrue($abstractContent->isExecutable(-1,
                                                         -1
                                            )
               );
    }
}
?>