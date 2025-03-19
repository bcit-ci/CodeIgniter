Code generation
===============

It is also possible to generate code using the parser, by first creating an Abstract Syntax Tree and then using the
pretty printer to convert it to PHP code. To simplify code generation, the project comes with builders which allow
creating node trees using a fluid interface, instead of instantiating all nodes manually. Builders are available for
the following syntactic elements:

 * namespaces and use statements
 * classes, interfaces and traits
 * methods, functions and parameters
 * properties

Here is an example:

```php
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node;

$factory = new BuilderFactory;
$node = $factory->namespace('Name\Space')
    ->addStmt($factory->use('Some\Other\Thingy')->as('SomeOtherClass'))
    ->addStmt($factory->class('SomeClass')
        ->extend('SomeOtherClass')
        ->implement('A\Few', '\Interfaces')
        ->makeAbstract() // ->makeFinal()

        ->addStmt($factory->method('someMethod')
            ->makePublic()
            ->makeAbstract() // ->makeFinal()
            ->addParam($factory->param('someParam')->setTypeHint('SomeClass'))
            ->setDocComment('/**
                              * This method does something.
                              *
                              * @param SomeClass And takes a parameter
                              */')
        )

        ->addStmt($factory->method('anotherMethod')
            ->makeProtected() // ->makePublic() [default], ->makePrivate()
            ->addParam($factory->param('someParam')->setDefault('test'))
            // it is possible to add manually created nodes
            ->addStmt(new Node\Expr\Print_(new Node\Expr\Variable('someParam')))
        )

        // properties will be correctly reordered above the methods
        ->addStmt($factory->property('someProperty')->makeProtected())
        ->addStmt($factory->property('anotherProperty')->makePrivate()->setDefault(array(1, 2, 3)))
    )

    ->getNode()
;

$stmts = array($node);
$prettyPrinter = new PrettyPrinter\Standard();
echo $prettyPrinter->prettyPrintFile($stmts);
```

This will produce the following output with the standard pretty printer:

```php
<?php

namespace Name\Space;

use Some\Other\Thingy as SomeClass;
abstract class SomeClass extends SomeOtherClass implements A\Few, \Interfaces
{
    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);
    /**
     * This method does something.
     *
     * @param SomeClass And takes a parameter
     */
    public abstract function someMethod(SomeClass $someParam);
    protected function anotherMethod($someParam = 'test')
    {
        print $someParam;
    }
}
```
