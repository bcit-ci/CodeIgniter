<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2010 Arnaud Le Blanc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a function template function.
 *
 * @package    twig
 * @author     Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class Twig_Function_Function extends Twig_Function
{
    protected $function;

    public function __construct($function, array $options = array())
    {
        parent::__construct($options);

        $this->function = $function;
    }

    public function compile()
    {
        return $this->function;
    }
}
