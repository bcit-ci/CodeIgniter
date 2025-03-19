<?php

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Const_ extends NodeAbstract
{
    /** @var string Name */
    public $name;
    /** @var Expr Value */
    public $value;

    /**
     * Constructs a const node for use in class const and const statements.
     *
     * @param string  $name       Name
     * @param Expr    $value      Value
     * @param array   $attributes Additional attributes
     */
    public function __construct($name, Expr $value, array $attributes = array()) {
        parent::__construct($attributes);
        $this->name = $name;
        $this->value = $value;
    }

    public function getSubNodeNames() {
        return array('name', 'value');
    }
}
