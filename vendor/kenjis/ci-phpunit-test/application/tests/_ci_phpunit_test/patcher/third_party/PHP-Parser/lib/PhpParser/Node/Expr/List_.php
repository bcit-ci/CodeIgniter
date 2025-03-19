<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class List_ extends Expr
{
    /** @var Expr[] List of variables to assign to */
    public $vars;

    /**
     * Constructs a list() destructuring node.
     *
     * @param Expr[] $vars       List of variables to assign to
     * @param array  $attributes Additional attributes
     */
    public function __construct(array $vars, array $attributes = array()) {
        parent::__construct($attributes);
        $this->vars = $vars;
    }

    public function getSubNodeNames() {
        return array('vars');
    }
}
