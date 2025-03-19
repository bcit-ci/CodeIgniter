<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;


class TraitUse extends Node\Stmt
{
    /** @var Node\Name[] Traits */
    public $traits;
    /** @var TraitUseAdaptation[] Adaptations */
    public $adaptations;

    /**
     * Constructs a trait use node.
     *
     * @param Node\Name[]          $traits      Traits
     * @param TraitUseAdaptation[] $adaptations Adaptations
     * @param array                $attributes  Additional attributes
     */
    public function __construct(array $traits, array $adaptations = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->traits = $traits;
        $this->adaptations = $adaptations;
    }

    public function getSubNodeNames() {
        return array('traits', 'adaptations');
    }
}
