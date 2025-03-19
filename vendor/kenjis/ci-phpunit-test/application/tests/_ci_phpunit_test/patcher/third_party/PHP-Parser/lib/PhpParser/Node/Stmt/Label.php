<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class Label extends Stmt
{
    /** @var string Name */
    public $name;

    /**
     * Constructs a label node.
     *
     * @param string $name       Name
     * @param array  $attributes Additional attributes
     */
    public function __construct($name, array $attributes = array()) {
        parent::__construct($attributes);
        $this->name = $name;
    }

    public function getSubNodeNames() {
        return array('name');
    }
}
