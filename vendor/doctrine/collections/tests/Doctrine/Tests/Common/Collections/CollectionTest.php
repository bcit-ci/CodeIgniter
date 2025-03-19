<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    private $collection;

    protected function setUp()
    {
        $this->collection = new ArrayCollection();
    }

    public function testIssetAndUnset()
    {
        $this->assertFalse(isset($this->collection[0]));
        $this->collection->add('testing');
        $this->assertTrue(isset($this->collection[0]));
        unset($this->collection[0]);
        $this->assertFalse(isset($this->collection[0]));
    }

    public function testToString()
    {
        $this->collection->add('testing');
        $this->assertTrue(is_string((string) $this->collection));
    }

    public function testRemovingNonExistentEntryReturnsNull()
    {
        $this->assertEquals(null, $this->collection->remove('testing_does_not_exist'));
    }

    public function testExists()
    {
        $this->collection->add("one");
        $this->collection->add("two");
        $exists = $this->collection->exists(function($k, $e) { return $e == "one"; });
        $this->assertTrue($exists);
        $exists = $this->collection->exists(function($k, $e) { return $e == "other"; });
        $this->assertFalse($exists);
    }

    public function testMap()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $res = $this->collection->map(function($e) { return $e * 2; });
        $this->assertEquals(array(2, 4), $res->toArray());
    }

    public function testFilter()
    {
        $this->collection->add(1);
        $this->collection->add("foo");
        $this->collection->add(3);
        $res = $this->collection->filter(function($e) { return is_numeric($e); });
        $this->assertEquals(array(0 => 1, 2 => 3), $res->toArray());
    }

    public function testFirstAndLast()
    {
        $this->collection->add('one');
        $this->collection->add('two');

        $this->assertEquals($this->collection->first(), 'one');
        $this->assertEquals($this->collection->last(), 'two');
    }

    public function testArrayAccess()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertEquals($this->collection[0], 'one');
        $this->assertEquals($this->collection[1], 'two');

        unset($this->collection[0]);
        $this->assertEquals($this->collection->count(), 1);
    }

    public function testContainsKey()
    {
        $this->collection[5] = 'five';
        $this->assertTrue($this->collection->containsKey(5));
    }

    public function testContains()
    {
        $this->collection[0] = 'test';
        $this->assertTrue($this->collection->contains('test'));
    }

    public function testSearch()
    {
        $this->collection[0] = 'test';
        $this->assertEquals(0, $this->collection->indexOf('test'));
    }

    public function testGet()
    {
        $this->collection[0] = 'test';
        $this->assertEquals('test', $this->collection->get(0));
    }

    public function testGetKeys()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals(array(0, 1), $this->collection->getKeys());
    }

    public function testGetValues()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals(array('one', 'two'), $this->collection->getValues());
    }

    public function testCount()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->count(), 2);
        $this->assertEquals(count($this->collection), 2);
    }

    public function testForAll()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->assertEquals($this->collection->forAll(function($k, $e) { return is_string($e); }), true);
        $this->assertEquals($this->collection->forAll(function($k, $e) { return is_array($e); }), false);
    }

    public function testPartition()
    {
        $this->collection[] = true;
        $this->collection[] = false;
        $partition = $this->collection->partition(function($k, $e) { return $e == true; });
        $this->assertEquals($partition[0][0], true);
        $this->assertEquals($partition[1][0], false);
    }

    public function testClear()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection->clear();
        $this->assertEquals($this->collection->isEmpty(), true);
    }

    public function testRemove()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $el = $this->collection->remove(0);

        $this->assertEquals('one', $el);
        $this->assertEquals($this->collection->contains('one'), false);
        $this->assertNull($this->collection->remove(0));
    }

    public function testRemoveElement()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';

        $this->assertTrue($this->collection->removeElement('two'));
        $this->assertFalse($this->collection->contains('two'));
        $this->assertFalse($this->collection->removeElement('two'));
    }

    public function testSlice()
    {
        $this->collection[] = 'one';
        $this->collection[] = 'two';
        $this->collection[] = 'three';

        $slice = $this->collection->slice(0, 1);
        $this->assertInternalType('array', $slice);
        $this->assertEquals(array('one'), $slice);

        $slice = $this->collection->slice(1);
        $this->assertEquals(array(1 => 'two', 2 => 'three'), $slice);

        $slice = $this->collection->slice(1, 1);
        $this->assertEquals(array(1 => 'two'), $slice);
    }

    public function fillMatchingFixture()
    {
        $std1 = new \stdClass();
        $std1->foo = "bar";
        $this->collection[] = $std1;

        $std2 = new \stdClass();
        $std2->foo = "baz";
        $this->collection[] = $std2;
    }

    /**
     * @group DDC-1637
     */
    public function testMatching()
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(Criteria::expr()->eq("foo", "bar")));
        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $col);
        $this->assertNotSame($col, $this->collection);
        $this->assertEquals(1, count($col));
    }

    /**
     * @group DDC-1637
     */
    public function testMatchingOrdering()
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, array('foo' => 'DESC')));

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $col);
        $this->assertNotSame($col, $this->collection);
        $this->assertEquals(2, count($col));
        $this->assertEquals('baz', $col->first()->foo);
        $this->assertEquals('bar', $col->last()->foo);
    }

    /**
     * @group DDC-1637
     */
    public function testMatchingSlice()
    {
        $this->fillMatchingFixture();

        $col = $this->collection->matching(new Criteria(null, null, 1, 1));

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $col);
        $this->assertNotSame($col, $this->collection);
        $this->assertEquals(1, count($col));
        $this->assertEquals('baz', $col[0]->foo);
    }

    public function testCanRemoveNullValuesByKey()
    {
        $this->collection->add(null);
        $this->collection->remove(0);
        $this->assertTrue($this->collection->isEmpty());
    }

    public function testCanVerifyExistingKeysWithNullValues()
    {
        $this->collection->set('key', null);
        $this->assertTrue($this->collection->containsKey('key'));
    }
}
