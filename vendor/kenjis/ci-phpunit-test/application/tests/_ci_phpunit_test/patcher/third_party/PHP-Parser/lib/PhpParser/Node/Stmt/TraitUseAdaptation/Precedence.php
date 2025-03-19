<?php

namespace PhpParser\Node\Stmt\TraitUseAdaptation;

use PhpParser\Node;

class Precedence extends Node\Stmt\TraitUseAdaptation
{
    /** @var Node\Name[] Overwritten traits */
    public $insteadof;

    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param Node\Name   $trait       Trait name
     * @param string      $method      Method name
     * @param Node\Name[] $insteadof   Overwritten traits
     * @param array       $attributes  Additional attributes
     */
    public function __construct(Node\Name $trait, $method, array $insteadof, array $attributes = array()) {
        parent::__construct($attributes);
        $this->trait = $trait;
        $this->method = $method;
        $this->insteadof = $insteadof;
    }

    public function getSubNodeNames() {
        return array('trait', 'method', 'insteadof');
    }
}
