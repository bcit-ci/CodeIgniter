<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Catch_ extends Node\Stmt
{
    /** @var Node\Name Class of exception */
    public $type;
    /** @var string Variable for exception */
    public $var;
    /** @var Node[] Statements */
    public $stmts;

    /**
     * Constructs a catch node.
     *
     * @param Node\Name $type       Class of exception
     * @param string    $var        Variable for exception
     * @param Node[]    $stmts      Statements
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Name $type, $var, array $stmts = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->type = $type;
        $this->var = $var;
        $this->stmts = $stmts;
    }

    public function getSubNodeNames() {
        return array('type', 'var', 'stmts');
    }
}
