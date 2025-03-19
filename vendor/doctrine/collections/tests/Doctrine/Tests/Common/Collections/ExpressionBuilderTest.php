<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;

/**
 * @group DDC-1637
 */
class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExpressionBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->builder = new ExpressionBuilder();
    }

    public function testAndX()
    {
        $expr = $this->builder->andX($this->builder->eq("a", "b"));

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\CompositeExpression', $expr);
        $this->assertEquals(CompositeExpression::TYPE_AND, $expr->getType());
    }

    public function testOrX()
    {
        $expr = $this->builder->orX($this->builder->eq("a", "b"));

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\CompositeExpression', $expr);
        $this->assertEquals(CompositeExpression::TYPE_OR, $expr->getType());
    }

    public function testInvalidAndXArgument()
    {
        $this->setExpectedException("RuntimeException");
        $this->builder->andX("foo");
    }

    public function testEq()
    {
        $expr = $this->builder->eq("a", "b");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::EQ, $expr->getOperator());
    }

    public function testNeq()
    {
        $expr = $this->builder->neq("a", "b");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::NEQ, $expr->getOperator());
    }

    public function testLt()
    {
        $expr = $this->builder->lt("a", "b");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::LT, $expr->getOperator());
    }

    public function testGt()
    {
        $expr = $this->builder->gt("a", "b");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::GT, $expr->getOperator());
    }

    public function testGte()
    {
        $expr = $this->builder->gte("a", "b");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::GTE, $expr->getOperator());
    }

    public function testLte()
    {
        $expr = $this->builder->lte("a", "b");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::LTE, $expr->getOperator());
    }

    public function testIn()
    {
        $expr = $this->builder->in("a", array("b"));

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::IN, $expr->getOperator());
    }

    public function testNotIn()
    {
        $expr = $this->builder->notIn("a", array("b"));

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::NIN, $expr->getOperator());
    }

    public function testIsNull()
    {
        $expr = $this->builder->isNull("a");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::EQ, $expr->getOperator());
    }

    public function testContains()
    {
        $expr = $this->builder->contains("a", "b");

        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\Comparison', $expr);
        $this->assertEquals(Comparison::CONTAINS, $expr->getOperator());
    }
}
