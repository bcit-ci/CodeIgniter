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
 * Represents a template filter.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
interface Twig_FilterInterface
{
    /**
     * Compiles a filter.
     *
     * @return string The PHP code for the filter
     */
    public function compile();

    public function needsEnvironment();

    public function needsContext();

    public function getSafe(Twig_Node $filterArgs);

    public function getPreservesSafety();

    public function getPreEscape();

    public function setArguments($arguments);

    public function getArguments();
}
