<?php

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a template filter as a node.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class Twig_Filter_Node extends Twig_Filter
{
    protected $class;

    public function __construct($class, array $options = array())
    {
        parent::__construct($options);

        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function compile()
    {
    }
}
