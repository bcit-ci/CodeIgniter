<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Const_ extends Node\Stmt
{
    /** @var Node\Const_[] Constant declarations */
    public $consts;

    /**
     * Constructs a const list node.
     *
     * @param Node\Const_[] $consts     Constant declarations
     * @param array         $attributes Additional attributes
     */
    public function __construct(array $consts, array $attributes = array()) {
        parent::__construct($attributes);
        $this->consts = $consts;
    }

    public function getSubNodeNames() {
        return array('consts');
    }
}
