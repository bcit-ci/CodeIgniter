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
 * Mustache Template rendering Context.
 */
class Mustache_Context
{
    private $stack = array();

    /**
     * Mustache rendering Context constructor.
     *
     * @param mixed $context Default rendering context (default: null)
     */
    public function __construct($context = null)
    {
        if ($context !== null) {
            $this->stack = array($context);
        }
    }

    /**
     * Push a new Context frame onto the stack.
     *
     * @param mixed $value Object or array to use for context
     */
    public function push($value)
    {
        array_push($this->stack, $value);
    }

    /**
     * Pop the last Context frame from the stack.
     *
     * @return mixed Last Context frame (object or array)
     */
    public function pop()
    {
        return array_pop($this->stack);
    }

    /**
     * Get the last Context frame.
     *
     * @return mixed Last Context frame (object or array)
     */
    public function last()
    {
        return end($this->stack);
    }

    /**
     * Find a variable in the Context stack.
     *
     * Starting with the last Context frame (the context of the innermost section), and working back to the top-level
     * rendering context, look for a variable with the given name:
     *
     *  * If the Context frame is an associative array which contains the key $id, returns the value of that element.
     *  * If the Context frame is an object, this will check first for a public method, then a public property named
     *    $id. Failing both of these, it will try `__isset` and `__get` magic methods.
     *  * If a value named $id is not found in any Context frame, returns an empty string.
     *
     * @param string $id Variable name
     *
     * @return mixed Variable value, or '' if not found
     */
    public function find($id)
    {
        return $this->findVariableInStack($id, $this->stack);
    }

    /**
     * Find a 'dot notation' variable in the Context stack.
     *
     * Note that dot notation traversal bubbles through scope differently than the regular find method. After finding
     * the initial chunk of the dotted name, each subsequent chunk is searched for only within the value of the previous
     * result. For example, given the following context stack:
     *
     *     $data = array(
     *         'name' => 'Fred',
     *         'child' => array(
     *             'name' => 'Bob'
     *         ),
     *     );
     *
     * ... and the Mustache following template:
     *
     *     {{ child.name }}
     *
     * ... the `name` value is only searched for within the `child` value of the global Context, not within parent
     * Context frames.
     *
     * @param string $id Dotted variable selector
     *
     * @return mixed Variable value, or '' if not found
     */
    public function findDot($id)
    {
        $chunks = explode('.', $id);
        $first  = array_shift($chunks);
        $value  = $this->findVariableInStack($first, $this->stack);

        foreach ($chunks as $chunk) {
            if ($value === '') {
                return $value;
            }

            $value = $this->findVariableInStack($chunk, array($value));
        }

        return $value;
    }

    /**
     * Helper function to find a variable in the Context stack.
     *
     * @see Mustache_Context::find
     *
     * @param string $id    Variable name
     * @param array  $stack Context stack
     *
     * @return mixed Variable value, or '' if not found
     */
    private function findVariableInStack($id, array $stack)
    {
        for ($i = count($stack) - 1; $i >= 0; $i--) {
            if (is_object($stack[$i])) {
                if (method_exists($stack[$i], $id)) {
                    return $stack[$i]->$id();
                } elseif (isset($stack[$i]->$id)) {
                    return $stack[$i]->$id;
                }
            } elseif (is_array($stack[$i]) && array_key_exists($id, $stack[$i])) {
                return $stack[$i][$id];
            }
        }

        return '';
    }
}
