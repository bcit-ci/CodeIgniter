<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class If_ extends Node\Stmt
{
    /** @var Node\Expr Condition expression */
    public $cond;
    /** @var Node[] Statements */
    public $stmts;
    /** @var ElseIf_[] Elseif clauses */
    public $elseifs;
    /** @var null|Else_ Else clause */
    public $else;

    /**
     * Constructs an if node.
     *
     * @param Node\Expr $cond       Condition
     * @param array     $subNodes   Array of the following optional subnodes:
     *                              'stmts'   => array(): Statements
     *                              'elseifs' => array(): Elseif clauses
     *                              'else'    => null   : Else clause
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Expr $cond, array $subNodes = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->cond = $cond;
        $this->stmts = isset($subNodes['stmts']) ? $subNodes['stmts'] : array();
        $this->elseifs = isset($subNodes['elseifs']) ? $subNodes['elseifs'] : array();
        $this->else = isset($subNodes['else']) ? $subNodes['else'] : null;
    }

    public function getSubNodeNames() {
        return array('cond', 'stmts', 'elseifs', 'else');
    }
}
