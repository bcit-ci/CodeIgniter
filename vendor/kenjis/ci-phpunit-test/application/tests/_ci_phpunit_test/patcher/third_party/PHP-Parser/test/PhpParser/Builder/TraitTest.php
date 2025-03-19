<?php

namespace PhpParser\Builder;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class TraitTest extends \PHPUnit_Framework_TestCase
{
    protected function createTraitBuilder($class) {
        return new Trait_($class);
    }

    public function testStmtAddition() {
        $method1 = new Stmt\ClassMethod('test1');
        $method2 = new Stmt\ClassMethod('test2');
        $method3 = new Stmt\ClassMethod('test3');
        $prop = new Stmt\Property(Stmt\Class_::MODIFIER_PUBLIC, array(
            new Stmt\PropertyProperty('test')
        ));
        $trait = $this->createTraitBuilder('TestTrait')
            ->setDocComment('/** Nice trait */')
            ->addStmt($method1)
            ->addStmts(array($method2, $method3))
            ->addStmt($prop)
            ->getNode();
        $this->assertEquals(new Stmt\Trait_('TestTrait', array(
            $prop, $method1, $method2, $method3
        ), array(
            'comments' => array(
                new Comment\Doc('/** Nice trait */')
            )
        )), $trait);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unexpected node of type "Stmt_Echo"
     */
    public function testInvalidStmtError() {
        $this->createTraitBuilder('Test')
            ->addStmt(new Stmt\Echo_(array()))
        ;
    }
}
