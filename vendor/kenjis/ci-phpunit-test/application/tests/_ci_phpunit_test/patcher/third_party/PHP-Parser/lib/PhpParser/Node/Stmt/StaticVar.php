<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class StaticVar extends Node\Stmt
{
    /** @var string Name */
    public $name;
    /** @var null|Node\Expr Default value */
    public $default;

    /**
     * Constructs a static variable node.
     *
     * @param string         $name       Name
     * @param null|Node\Expr $default    Default value
     * @param array          $attributes Additional attributes
     */
    public function __construct($name, Node\Expr $default = null, array $attributes = array()) {
        parent::__construct($attributes);
        $this->name = $name;
        $this->default = $default;
    }

    public function getSubNodeNames() {
        return array('name', 'default');
    }
}
