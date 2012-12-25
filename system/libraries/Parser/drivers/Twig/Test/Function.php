<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a function template test.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class Twig_Test_Function implements Twig_TestInterface
{
    protected $function;

    public function __construct($function)
    {
        $this->function = $function;
    }

    public function compile()
    {
        return $this->function;
    }
}
