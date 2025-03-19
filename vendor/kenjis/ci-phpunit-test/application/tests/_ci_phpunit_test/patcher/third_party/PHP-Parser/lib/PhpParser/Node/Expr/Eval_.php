<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Eval_ extends Expr
{
    /** @var Expr Expression */
    public $expr;

    /**
     * Constructs an eval() node.
     *
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $expr, array $attributes = array()) {
        parent::__construct($attributes);
        $this->expr = $expr;
    }

    public function getSubNodeNames() {
        return array('expr');
    }
}
