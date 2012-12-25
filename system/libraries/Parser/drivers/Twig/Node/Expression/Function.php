<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Node_Expression_Function extends Twig_Node_Expression
{
    public function __construct($name, Twig_NodeInterface $arguments, $lineno)
    {
        parent::__construct(array('arguments' => $arguments), array('name' => $name), $lineno);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $name = $this->getAttribute('name');

        if (false === $function = $compiler->getEnvironment()->getFunction($name)) {
            $message = sprintf('The function "%s" does not exist', $name);
            if ($alternatives = $compiler->getEnvironment()->computeAlternatives($name, array_keys($compiler->getEnvironment()->getFunctions()))) {
                $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
            }

            throw new Twig_Error_Syntax($message, $this->getLine(), $compiler->getFilename());
        }

        $compiler->raw($function->compile().'(');

        $first = true;

        if ($function->needsEnvironment()) {
            $compiler->raw('$this->env');
            $first = false;
        }

        if ($function->needsContext()) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $compiler->raw('$context');
            $first = false;
        }

        foreach ($function->getArguments() as $argument) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $compiler->string($argument);
            $first = false;
        }

        foreach ($this->getNode('arguments') as $node) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $compiler->subcompile($node);
            $first = false;
        }

        $compiler->raw(')');
    }
}
