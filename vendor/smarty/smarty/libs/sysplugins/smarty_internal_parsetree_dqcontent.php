<?php
/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse tree  in the template parser
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */

/**
 * Raw chars as part of a double quoted string.
 *
 * @package    Smarty
 * @subpackage Compiler
 * @ignore
 */
class Smarty_Internal_ParseTree_DqContent extends Smarty_Internal_ParseTree
{
    /**
     * Create parse tree buffer with string content
     *
     * @param string $data string section
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Return content as double quoted string
     *
     * @param \Smarty_Internal_Templateparser $parser
     *
     * @return string doubled quoted string
     */
    public function to_smarty_php(Smarty_Internal_Templateparser $parser)
    {
        return '"' . $this->data . '"';
    }
}
