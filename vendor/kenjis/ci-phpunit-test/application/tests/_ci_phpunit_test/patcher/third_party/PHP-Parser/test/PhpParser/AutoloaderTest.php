<?php

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

/* The autoloader is already active at this point, so we only check effects here. */

class AutoloaderTest extends \PHPUnit_Framework_TestCase {
    public function testClassExists() {
        $this->assertTrue(class_exists('PhpParser\NodeVisitorAbstract'));
        $this->assertFalse(class_exists('PHPParser_NodeVisitor_NameResolver'));

        $this->assertFalse(class_exists('PhpParser\FooBar'));
        $this->assertFalse(class_exists('PHPParser_FooBar'));
    }
}
