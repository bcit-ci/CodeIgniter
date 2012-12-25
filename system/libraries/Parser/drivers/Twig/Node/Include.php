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

/**
 * Represents an include node.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class Twig_Node_Include extends Twig_Node implements Twig_NodeOutputInterface
{
    public function __construct(Twig_Node_Expression $expr, Twig_Node_Expression $variables = null, $only = false, $ignoreMissing = false, $lineno, $tag = null)
    {
        parent::__construct(array('expr' => $expr, 'variables' => $variables), array('only' => (Boolean) $only, 'ignore_missing' => (Boolean) $ignoreMissing), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        if ($this->getAttribute('ignore_missing')) {
            $compiler
                ->write("try {\n")
                ->indent()
            ;
        }

        $this->addGetTemplate($compiler);

        $compiler->raw('->display(');

        $this->addTemplateArguments($compiler);

        $compiler->raw(");\n");

        if ($this->getAttribute('ignore_missing')) {
            $compiler
                ->outdent()
                ->write("} catch (Twig_Error_Loader \$e) {\n")
                ->indent()
                ->write("// ignore missing template\n")
                ->outdent()
                ->write("}\n\n")
            ;
        }
    }

    protected function addGetTemplate(Twig_Compiler $compiler)
    {
        if ($this->getNode('expr') instanceof Twig_Node_Expression_Constant) {
            $compiler
                ->write("\$this->env->loadTemplate(")
                ->subcompile($this->getNode('expr'))
                ->raw(")")
            ;
        } else {
            $compiler
                ->write("\$template = \$this->env->resolveTemplate(")
                ->subcompile($this->getNode('expr'))
                ->raw(");\n")
                ->write('$template')
            ;
        }
    }

    protected function addTemplateArguments(Twig_Compiler $compiler)
    {
        if (false === $this->getAttribute('only')) {
            if (null === $this->getNode('variables')) {
                $compiler->raw('$context');
            } else {
                $compiler
                    ->raw('array_merge($context, ')
                    ->subcompile($this->getNode('variables'))
                    ->raw(')')
                ;
            }
        } else {
            if (null === $this->getNode('variables')) {
                $compiler->raw('array()');
            } else {
                $compiler->subcompile($this->getNode('variables'));
            }
        }
    }
}
