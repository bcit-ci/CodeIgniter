<?php

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Yield_ extends Expr
{
    /** @var null|Expr Key expression */
    public $key;
    /** @var null|Expr Value expression */
    public $value;

    /**
     * Constructs a yield expression node.
     *
     * @param null|Expr $value      Value expression
     * @param null|Expr $key        Key expression
     * @param array     $attributes Additional attributes
     */
    public function __construct(Expr $value = null, Expr $key = null, array $attributes = array()) {
        parent::__construct($attributes);
        $this->key = $key;
        $this->value = $value;
    }

    public function getSubNodeNames() {
        return array('key', 'value');
    }
}
