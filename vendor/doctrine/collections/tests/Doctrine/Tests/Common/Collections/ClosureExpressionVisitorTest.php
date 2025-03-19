<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;
use Doctrine\Common\Collections\ExpressionBuilder;

/**
 * @group DDC-1637
 */
class ClosureExpressionVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClosureExpressionVisitor
     */
    private $visitor;

    /**
     * @var ExpressionBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->visitor = new ClosureExpressionVisitor();
        $this->builder = new ExpressionBuilder();
    }

    public function testGetObjectFieldValueIsAccessor()
    {
        $object = new TestObject(1, 2, true);

        $this->assertTrue($this->visitor->getObjectFieldValue($object, 'baz'));
    }

    public function testGetObjectFieldValueMagicCallMethod()
    {
        $object = new TestObject(1, 2, true, 3);

        $this->assertEquals(3, $this->visitor->getObjectFieldValue($object, 'qux'));
    }

    public function testWalkEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->eq("foo", 1));

        $this->assertTrue($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(2)));
    }

    public function testWalkNotEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->neq("foo", 1));

        $this->assertFalse($closure(new TestObject(1)));
        $this->assertTrue($closure(new TestObject(2)));
    }

    public function testWalkLessThanComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->lt("foo", 1));

        $this->assertFalse($closure(new TestObject(1)));
        $this->assertTrue($closure(new TestObject(0)));
    }

    public function testWalkLessThanEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->lte("foo", 1));

        $this->assertFalse($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(1)));
        $this->assertTrue($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanEqualsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->gte("foo", 1));

        $this->assertTrue($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(0)));
    }

    public function testWalkGreaterThanComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->gt("foo", 1));

        $this->assertTrue($closure(new TestObject(2)));
        $this->assertFalse($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(0)));
    }

    public function testWalkInComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->in("foo", array(1, 2, 3)));

        $this->assertTrue($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(0)));
    }

    public function testWalkNotInComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->notIn("foo", array(1, 2, 3)));

        $this->assertFalse($closure(new TestObject(1)));
        $this->assertFalse($closure(new TestObject(2)));
        $this->assertTrue($closure(new TestObject(0)));
        $this->assertTrue($closure(new TestObject(4)));
    }

    public function testWalkContainsComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->contains('foo', 'hello'));

        $this->assertTrue($closure(new TestObject('hello world')));
        $this->assertFalse($closure(new TestObject('world')));
    }

    public function testWalkAndCompositeExpression()
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->andX(
                $this->builder->eq("foo", 1),
                $this->builder->eq("bar", 1)
            )
        );

        $this->assertTrue($closure(new TestObject(1, 1)));
        $this->assertFalse($closure(new TestObject(1, 0)));
        $this->assertFalse($closure(new TestObject(0, 1)));
        $this->assertFalse($closure(new TestObject(0, 0)));
    }

    public function testWalkOrCompositeExpression()
    {
        $closure = $this->visitor->walkCompositeExpression(
            $this->builder->orX(
                $this->builder->eq("foo", 1),
                $this->builder->eq("bar", 1)
            )
        );

        $this->assertTrue($closure(new TestObject(1, 1)));
        $this->assertTrue($closure(new TestObject(1, 0)));
        $this->assertTrue($closure(new TestObject(0, 1)));
        $this->assertFalse($closure(new TestObject(0, 0)));
    }

    public function testSortByFieldAscending()
    {
        $objects = array(new TestObject("b"), new TestObject("a"), new TestObject("c"));
        $sort = ClosureExpressionVisitor::sortByField("foo");

        usort($objects, $sort);

        $this->assertEquals("a", $objects[0]->getFoo());
        $this->assertEquals("b", $objects[1]->getFoo());
        $this->assertEquals("c", $objects[2]->getFoo());
    }

    public function testSortByFieldDescending()
    {
        $objects = array(new TestObject("b"), new TestObject("a"), new TestObject("c"));
        $sort = ClosureExpressionVisitor::sortByField("foo", -1);

        usort($objects, $sort);

        $this->assertEquals("c", $objects[0]->getFoo());
        $this->assertEquals("b", $objects[1]->getFoo());
        $this->assertEquals("a", $objects[2]->getFoo());
    }

    public function testSortDelegate()
    {
        $objects = array(new TestObject("a", "c"), new TestObject("a", "b"), new TestObject("a", "a"));
        $sort = ClosureExpressionVisitor::sortByField("bar", 1);
        $sort = ClosureExpressionVisitor::sortByField("foo", 1, $sort);

        usort($objects, $sort);

        $this->assertEquals("a", $objects[0]->getBar());
        $this->assertEquals("b", $objects[1]->getBar());
        $this->assertEquals("c", $objects[2]->getBar());
    }

    public function testArrayComparison()
    {
        $closure = $this->visitor->walkComparison($this->builder->eq("foo", 42));

        $this->assertTrue($closure(array('foo' => 42)));
    }
}

class TestObject
{
    private $foo;
    private $bar;
    private $baz;
    private $qux;

    public function __construct($foo = null, $bar = null, $baz = null, $qux = null)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->baz = $baz;
        $this->qux = $qux;
    }

    public function __call($name, $arguments)
    {
        if ('getqux' === $name) {
            return $this->qux;
        }
    }

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function isBaz()
    {
        return $this->baz;
    }
}

