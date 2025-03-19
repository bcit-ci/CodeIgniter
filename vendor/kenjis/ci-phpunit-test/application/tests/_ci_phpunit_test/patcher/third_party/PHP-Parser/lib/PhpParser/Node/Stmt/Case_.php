<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Case_ extends Node\Stmt
{
    /** @var null|Node\Expr $cond Condition (null for default) */
    public $cond;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a case node.
     *
     * @param null|Node\Expr $cond       Condition (null for default)
     * @param Node[]         $stmts      Statements
     * @param array          $attributes Additional attributes
     */
    public function __construct($cond, array $stmts = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->cond = $cond;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames() {
        return array('cond', 'stmts');
    }
}
