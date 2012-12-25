<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2012 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mustache Parser class.
 *
 * This class is responsible for turning a set of Mustache tokens into a parse tree.
 */
class Mustache_Parser
{

    /**
     * Process an array of Mustache tokens and convert them into a parse tree.
     *
     * @param array $tokens Set of Mustache tokens
     *
     * @return array Mustache token parse tree
     */
    public function parse(array $tokens = array())
    {
        return $this->buildTree(new ArrayIterator($tokens));
    }

    /**
     * Helper method for recursively building a parse tree.
     *
     * @param ArrayIterator $tokens Stream of Mustache tokens
     * @param array         $parent Parent token (default: null)
     *
     * @return array Mustache Token parse tree
     *
     * @throws LogicException when nesting errors or mismatched section tags are encountered.
     */
    private function buildTree(ArrayIterator $tokens, array $parent = null)
    {
        $nodes = array();

        do {
            $token = $tokens->current();
            $tokens->next();

            if ($token === null) {
                continue;
            } else {
                switch ($token[Mustache_Tokenizer::TYPE]) {
                    case Mustache_Tokenizer::T_SECTION:
                    case Mustache_Tokenizer::T_INVERTED:
                        $nodes[] = $this->buildTree($tokens, $token);
                        break;

                    case Mustache_Tokenizer::T_END_SECTION:
                        if (!isset($parent)) {
                            throw new LogicException('Unexpected closing tag: /'. $token[Mustache_Tokenizer::NAME]);
                        }

                        if ($token[Mustache_Tokenizer::NAME] !== $parent[Mustache_Tokenizer::NAME]) {
                            throw new LogicException('Nesting error: ' . $parent[Mustache_Tokenizer::NAME] . ' vs. ' . $token[Mustache_Tokenizer::NAME]);
                        }

                        $parent[Mustache_Tokenizer::END]   = $token[Mustache_Tokenizer::INDEX];
                        $parent[Mustache_Tokenizer::NODES] = $nodes;

                        return $parent;
                        break;

                    default:
                        $nodes[] = $token;
                        break;
                }
            }

        } while ($tokens->valid());

        if (isset($parent)) {
            throw new LogicException('Missing closing tag: ' . $parent[Mustache_Tokenizer::NAME]);
        }

        return $nodes;
    }
}
