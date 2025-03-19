<?php

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class Goto_ extends Stmt
{
    /** @var string Name of label to jump to */
    public $name;

    /**
     * Constructs a goto node.
     *
     * @param string $name       Name of label to jump to
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
