<?php

namespace PhpParser\Node\Stmt\TraitUseAdaptation;

use PhpParser\Node;

class Alias extends Node\Stmt\TraitUseAdaptation
{
    /** @var null|int New modifier */
    public $newModifier;
    /** @var null|string New name */
    public $newName;

    /**
     * Constructs a trait use precedence adaptation node.
     *
     * @param null|Node\Name $trait       Trait name
     * @param string         $method      Method name
     * @param null|int       $newModifier New modifier
     * @param null|string    $newName     New name
     * @param array          $attributes  Additional attributes
     */
    public function __construct($trait, $method, $newModifier, $newName, array $attributes = array()) {
        parent::__construct($attributes);
        $this->trait = $trait;
        $this->method = $method;
        $this->newModifier = $newModifier;
        $this->newName = $newName;
    }

    public function getSubNodeNames() {
        return array('trait', 'method', 'newModifier', 'newName');
    }
}
