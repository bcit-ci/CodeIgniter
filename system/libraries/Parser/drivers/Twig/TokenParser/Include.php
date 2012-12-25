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
 * Includes a template.
 *
 * <pre>
 *   {% include 'header.html' %}
 *     Body
 *   {% include 'footer.html' %}
 * </pre>
 */
class Twig_TokenParser_Include extends Twig_TokenParser
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
        $expr = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        return new Twig_Node_Include($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    protected function parseArguments()
    {
        $stream = $this->parser->getStream();

        $ignoreMissing = false;
        if ($stream->test(Twig_Token::NAME_TYPE, 'ignore')) {
            $stream->next();
            $stream->expect(Twig_Token::NAME_TYPE, 'missing');

            $ignoreMissing = true;
        }

        $variables = null;
        if ($stream->test(Twig_Token::NAME_TYPE, 'with')) {
            $stream->next();

            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;
        if ($stream->test(Twig_Token::NAME_TYPE, 'only')) {
            $stream->next();

            $only = true;
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return array($variables, $only, $ignoreMissing);
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'include';
    }
}
