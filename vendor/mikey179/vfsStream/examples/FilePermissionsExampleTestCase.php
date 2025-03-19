<?php
/**
 * This file is part of vfsStream.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\bovigo\vfs
 */
namespace org\bovigo\vfs\example;
use org\bovigo\vfs\vfsStream;
require_once 'FilePermissionsExample.php';
/**
 * Test for FilePermissionsExample.
 */
class FilePermissionsExampleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function directoryWritable()
    {
        vfsStream::setup('exampleDir');
        $example = new FilePermissionsExample();
        $example->writeConfig(array('foo' => 'bar'),
                              vfsStream::url('exampleDir/writable.ini')
        );

        // assertions here
    }

    /**
     * @test
     */
    public function directoryNotWritable()
    {
        vfsStream::setup('exampleDir', 0444);
        $example = new FilePermissionsExample();
        $example->writeConfig(array('foo' => 'bar'),
                              vfsStream::url('exampleDir/notWritable.ini')
        );
    }
}
?>