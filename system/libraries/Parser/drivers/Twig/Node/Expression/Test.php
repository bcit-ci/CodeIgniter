<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Node_Expression_Test extends Twig_Node_Expression
{
    public function __construct(Twig_NodeInterface $node, $name, Twig_NodeInterface $arguments = null, $lineno)
    {
        parent::__construct(array('node' => $node, 'arguments' => $arguments), array('name' => $name), $lineno);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $testMap = $compiler->getEnvironment()->getTests();
        if (!isset($testMap[$name])) {
            $message = sprintf('The test "%s" does not exist', $name);
            if ($alternatives = $compiler->getEnvironment()->computeAlternatives($name, array_keys($compiler->getEnvironment()->getTests()))) {
                $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
            }

            throw new Twig_Error_Syntax($message, $this->getLine(), $compiler->getFilename());
        }

        $name = $this->getAttribute('name');
        $node = $this->getNode('node');

        $compiler
            ->raw($testMap[$name]->compile().'(')
            ->subcompile($node)
        ;

        if (null !== $this->getNode('arguments')) {
            $compiler->raw(', ');

            $max = count($this->getNode('arguments')) - 1;
            foreach ($this->getNode('arguments') as $i => $arg) {
                $compiler->subcompile($arg);

                if ($i != $max) {
                    $compiler->raw(', ');
                }
            }
        }

        $compiler->raw(')');
    }
}
