<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Node_Expression_Filter extends Twig_Node_Expression
{
    public function __construct(Twig_NodeInterface $node, Twig_Node_Expression_Constant $filterName, Twig_NodeInterface $arguments, $lineno, $tag = null)
    {
        parent::__construct(array('node' => $node, 'filter' => $filterName, 'arguments' => $arguments), array(), $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $name = $this->getNode('filter')->getAttribute('value');

        if (false === $filter = $compiler->getEnvironment()->getFilter($name)) {
            $message = sprintf('The filter "%s" does not exist', $name);
            if ($alternatives = $compiler->getEnvironment()->computeAlternatives($name, array_keys($compiler->getEnvironment()->getFilters()))) {
                $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
            }

            throw new Twig_Error_Syntax($message, $this->getLine(), $compiler->getFilename());
        }

        $this->compileFilter($compiler, $filter);
    }

    protected function compileFilter(Twig_Compiler $compiler, Twig_FilterInterface $filter)
    {
        $compiler
            ->raw($filter->compile().'(')
            ->raw($filter->needsEnvironment() ? '$this->env, ' : '')
            ->raw($filter->needsContext() ? '$context, ' : '')
        ;

        foreach ($filter->getArguments() as $argument) {
            $compiler
                ->string($argument)
                ->raw(', ')
            ;
        }

        $compiler->subcompile($this->getNode('node'));

        foreach ($this->getNode('arguments') as $node) {
            $compiler
                ->raw(', ')
                ->subcompile($node)
            ;
        }

        $compiler->raw(')');
    }
}
