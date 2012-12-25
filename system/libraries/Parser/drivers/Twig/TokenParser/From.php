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
 * Imports macros.
 *
 * <pre>
 *   {% from 'forms.html' import forms %}
 * </pre>
 */
class Twig_TokenParser_From extends Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect('import');

        $targets = array();
        do {
            $name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();

            $alias = $name;
            if ($stream->test('as')) {
                $stream->next();

                $alias = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
            }

            $targets[$name] = $alias;

            if (!$stream->test(Twig_Token::PUNCTUATION_TYPE, ',')) {
                break;
            }

            $stream->next();
        } while (true);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $node = new Twig_Node_Import($macro, new Twig_Node_Expression_AssignName($this->parser->getVarName(), $token->getLine()), $token->getLine(), $this->getTag());

        foreach ($targets as $name => $alias) {
            $this->parser->addImportedSymbol('function', $alias, 'get'.$name, $node->getNode('var'));
        }

        return $node;
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'from';
    }
}
