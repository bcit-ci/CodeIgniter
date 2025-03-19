<?php

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $criteria = Criteria::create();

        $this->assertInstanceOf('Doctrine\Common\Collections\Criteria', $criteria);
    }

    public function testConstructor()
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria($expr, array("foo" => "ASC"), 10, 20);

        $this->assertSame($expr, $criteria->getWhereExpression());
        $this->assertEquals(array("foo" => "ASC"), $criteria->getOrderings());
        $this->assertEquals(10, $criteria->getFirstResult());
        $this->assertEquals(20, $criteria->getMaxResults());
    }

    public function testWhere()
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->where($expr);

        $this->assertSame($expr, $criteria->getWhereExpression());
    }

    public function testAndWhere()
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->andWhere($expr);

        $where = $criteria->getWhereExpression();
        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\CompositeExpression', $where);

        $this->assertEquals(CompositeExpression::TYPE_AND, $where->getType());
        $this->assertSame(array($expr, $expr), $where->getExpressionList());
    }

    public function testOrWhere()
    {
        $expr     = new Comparison("field", "=", "value");
        $criteria = new Criteria();

        $criteria->where($expr);
        $expr = $criteria->getWhereExpression();
        $criteria->orWhere($expr);

        $where = $criteria->getWhereExpression();
        $this->assertInstanceOf('Doctrine\Common\Collections\Expr\CompositeExpression', $where);

        $this->assertEquals(CompositeExpression::TYPE_OR, $where->getType());
        $this->assertSame(array($expr, $expr), $where->getExpressionList());
    }

    public function testOrderings()
    {
        $criteria = Criteria::create()
            ->orderBy(array("foo" => "ASC"));

        $this->assertEquals(array("foo" => "ASC"), $criteria->getOrderings());
    }

    public function testExpr()
    {
        $this->assertInstanceOf('Doctrine\Common\Collections\ExpressionBuilder', Criteria::expr());
    }
}
