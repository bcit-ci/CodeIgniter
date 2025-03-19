<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;

class StaticCall extends Expr
{
    /** @var Node\Name|Expr Class name */
    public $class;
    /** @var string|Expr Method name */
    public $name;
    /** @var Node\Arg[] Arguments */
    public $args;

    /**
     * Constructs a static method call node.
     *
     * @param Node\Name|Expr $class      Class name
     * @param string|Expr    $name       Method name
     * @param Node\Arg[]     $args       Arguments
     * @param array          $attributes Additional attributes
     */
    public function __construct($class, $name, array $args = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->class = $class;
        $this->name = $name;
        $this->args = $args;
    }

    public function getSubNodeNames() {
        return array('class', 'name', 'args');
    }
}
