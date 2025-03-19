<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Array_ extends Expr
{
    /** @var ArrayItem[] Items */
    public $items;

    /**
     * Constructs an array node.
     *
     * @param ArrayItem[] $items      Items of the array
     * @param array       $attributes Additional attributes
     */
    public function __construct(array $items = array(), array $attributes = array()) {
        parent::__construct($attributes);
        $this->items = $items;
    }

    public function getSubNodeNames() {
        return array('items');
    }
}
