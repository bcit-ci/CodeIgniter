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
 * Test for org\bovigo\vfs\vfsStreamContainerIterator.
 */
class vfsStreamContainerIteratorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * test method to be used for iterating
     *
     * @test
     */
    public function iteration()
    {
        $dir = new vfsStreamDirectory('foo');
        $mockChild1 = $this->getMock('org\\bovigo\\vfs\\vfsStreamContent');
        $mockChild1->expects($this->any())
                   ->method('getName')
                   ->will($this->returnValue('bar'));
        $dir->addChild($mockChild1);
        $mockChild2 = $this->getMock('org\\bovigo\\vfs\\vfsStreamContent');
        $mockChild2->expects($this->any())
                   ->method('getName')
                   ->will($this->returnValue('baz'));
        $dir->addChild($mockChild2);
        $dirIterator = $dir->getIterator();
        $this->assertEquals('bar', $dirIterator->key());
        $this->assertTrue($dirIterator->valid());
        $bar = $dirIterator->current();
        $this->assertSame($mockChild1, $bar);
        $dirIterator->next();
        $this->assertEquals('baz', $dirIterator->key());
        $this->assertTrue($dirIterator->valid());
        $baz = $dirIterator->current();
        $this->assertSame($mockChild2, $baz);
        $dirIterator->next();
        $this->assertFalse($dirIterator->valid());
        $this->assertNull($dirIterator->key());
        $this->assertNull($dirIterator->current());
        $dirIterator->rewind();
        $this->assertTrue($dirIterator->valid());
        $this->assertEquals('bar', $dirIterator->key());
        $bar2 = $dirIterator->current();
        $this->assertSame($mockChild1, $bar2);
    }
}
?>