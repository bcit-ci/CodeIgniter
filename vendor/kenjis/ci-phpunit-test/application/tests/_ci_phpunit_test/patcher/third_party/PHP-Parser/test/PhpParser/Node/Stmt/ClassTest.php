<?php

namespace PhpParser\Node\Stmt;

class ClassTest extends \PHPUnit_Framework_TestCase
{
    public function testIsAbstract() {
        $class = new Class_('Foo', array('type' => Class_::MODIFIER_ABSTRACT));
        $this->assertTrue($class->isAbstract());

        $class = new Class_('Foo');
        $this->assertFalse($class->isAbstract());
    }

    public function testIsFinal() {
        $class = new Class_('Foo', array('type' => Class_::MODIFIER_FINAL));
        $this->assertTrue($class->isFinal());

        $class = new Class_('Foo');
        $this->assertFalse($class->isFinal());
    }

    public function testGetMethods() {
        $methods = array(
            new ClassMethod('foo'),
            new ClassMethod('bar'),
            new ClassMethod('fooBar'),
        );
        $class = new Class_('Foo', array(
            'stmts' => array(
                new TraitUse(array()),
                $methods[0],
                new ClassConst(array()),
                $methods[1],
                new Property(0, array()),
                $methods[2],
            )
        ));

        $this->assertSame($methods, $class->getMethods());
    }

    public function testGetMethod() {
        $methodConstruct = new ClassMethod('__CONSTRUCT');
        $methodTest = new ClassMethod('test');
        $class = new Class_('Foo', array(
            'stmts' => array(
                new ClassConst(array()),
                $methodConstruct,
                new Property(0, array()),
                $methodTest,
            )
        ));

        $this->assertSame($methodConstruct, $class->getMethod('__construct'));
        $this->assertSame($methodTest, $class->getMethod('test'));
        $this->assertNull($class->getMethod('nonExisting'));
    }
}
