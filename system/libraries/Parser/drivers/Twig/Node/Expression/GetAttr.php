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
class Twig_Node_Expression_GetAttr extends Twig_Node_Expression
{
    public function __construct(Twig_Node_Expression $node, Twig_Node_Expression $attribute, Twig_Node_Expression_Array $arguments, $type, $lineno)
    {
        parent::__construct(array('node' => $node, 'attribute' => $attribute, 'arguments' => $arguments), array('type' => $type, 'is_defined_test' => false, 'ignore_strict_check' => false, 'disable_c_ext' => false), $lineno);
    }

    public function compile(Twig_Compiler $compiler)
    {
        if (function_exists('twig_template_get_attributes') && !$this->getAttribute('disable_c_ext')) {
            $compiler->raw('twig_template_get_attributes($this, ');
        } else {
            $compiler->raw('$this->getAttribute(');
        }

        if ($this->getAttribute('ignore_strict_check')) {
            $this->getNode('node')->setAttribute('ignore_strict_check', true);
        }

        $compiler->subcompile($this->getNode('node'));

        $compiler->raw(', ')->subcompile($this->getNode('attribute'));

        if (count($this->getNode('arguments')) || Twig_TemplateInterface::ANY_CALL !== $this->getAttribute('type') || $this->getAttribute('is_defined_test') || $this->getAttribute('ignore_strict_check')) {
            $compiler->raw(', ')->subcompile($this->getNode('arguments'));

            if (Twig_TemplateInterface::ANY_CALL !== $this->getAttribute('type') || $this->getAttribute('is_defined_test') || $this->getAttribute('ignore_strict_check')) {
                $compiler->raw(', ')->repr($this->getAttribute('type'));
            }

            if ($this->getAttribute('is_defined_test') || $this->getAttribute('ignore_strict_check')) {
                $compiler->raw(', '.($this->getAttribute('is_defined_test') ? 'true' : 'false'));
            }

            if ($this->getAttribute('ignore_strict_check')) {
                $compiler->raw(', '.($this->getAttribute('ignore_strict_check') ? 'true' : 'false'));
            }
        }

        $compiler->raw(')');
    }
}
