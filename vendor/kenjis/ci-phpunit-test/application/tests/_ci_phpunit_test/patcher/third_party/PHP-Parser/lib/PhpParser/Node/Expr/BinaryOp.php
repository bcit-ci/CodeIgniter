<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

abstract class BinaryOp extends Expr
{
    /** @var Expr The left hand side expression */
    public $left;
    /** @var Expr The right hand side expression */
    public $right;

    /**
     * Constructs a bitwise and node.
     *
     * @param Expr  $left       The left hand side expression
     * @param Expr  $right      The right hand side expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $left, Expr $right, array $attributes = array()) {
        parent::__construct($attributes);
        $this->left = $left;
        $this->right = $right;
    }

    public function getSubNodeNames() {
        return array('left', 'right');
    }
}
