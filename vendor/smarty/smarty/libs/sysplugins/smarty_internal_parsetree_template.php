<?php
/**
 * Smarty Internal Plugin Templateparser Parse Tree
 * These are classes to build parse tree in the template parser
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Thue Kristensen
 * @author     Uwe Tews
 */

/**
 * Template element
 *
 * @package    Smarty
 * @subpackage Compiler
 * @ignore
 */
class Smarty_Internal_ParseTree_Template extends Smarty_Internal_ParseTree
{

    /**
     * Array of template elements
     *
     * @var array
     */
    public $subtrees = Array();

    /**
     * Create root of parse tree for template elements
     *
     */
    public function __construct()
    {
    }

    /**
     * Append buffer to subtree
     *
     * @param \Smarty_Internal_Templateparser $parser
     * @param Smarty_Internal_ParseTree       $subtree
     */
    public function append_subtree(Smarty_Internal_Templateparser $parser, Smarty_Internal_ParseTree $subtree)
    {
        if (!empty($subtree->subtrees)) {
            $this->subtrees = array_merge($this->subtrees, $subtree->subtrees);
        } else {
            if ($subtree->data !== '') {
                $this->subtrees[] = $subtree;
            }
        }
    }

    /**
     * Append array to subtree
     *
     * @param \Smarty_Internal_Templateparser $parser
     * @param \Smarty_Internal_ParseTree[]    $array
     */
    public function append_array(Smarty_Internal_Templateparser $parser, $array = array())
    {
        if (!empty($array)) {
            $this->subtrees = array_merge($this->subtrees, (array) $array);
        }
    }

    /**
     * Prepend array to subtree
     *
     * @param \Smarty_Internal_Templateparser $parser
     * @param \Smarty_Internal_ParseTree[]    $array
     */
    public function prepend_array(Smarty_Internal_Templateparser $parser, $array = array())
    {
        if (!empty($array)) {
            $this->subtrees = array_merge((array) $array, $this->subtrees);
        }
    }

    /**
     * Sanitize and merge subtree buffers together
     *
     * @param \Smarty_Internal_Templateparser $parser
     *
     * @return string template code content
     */
    public function to_smarty_php(Smarty_Internal_Templateparser $parser)
    {
        $code = '';
        for ($key = 0, $cnt = count($this->subtrees); $key < $cnt; $key ++) {
            if ($this->subtrees[$key] instanceof Smarty_Internal_ParseTree_Text) {
                $subtree = $this->subtrees[$key]->to_smarty_php($parser);
                while ($key + 1 < $cnt && ($this->subtrees[$key + 1] instanceof Smarty_Internal_ParseTree_Text ||
                        $this->subtrees[$key + 1]->data == '')) {
                    $key ++;
                    if ($this->subtrees[$key]->data == '') {
                        continue;
                    }
                    $subtree .= $this->subtrees[$key]->to_smarty_php($parser);
                }
                if ($subtree == '') {
                    continue;
                }
                $code .= preg_replace('/((<%)|(%>)|(<\?php)|(<\?)|(\?>)|(<\/?script))/', "<?php echo '\$1'; ?>\n",
                                      $subtree);
                continue;
            }
            if ($this->subtrees[$key] instanceof Smarty_Internal_ParseTree_Tag) {
                $subtree = $this->subtrees[$key]->to_smarty_php($parser);
                while ($key + 1 < $cnt && ($this->subtrees[$key + 1] instanceof Smarty_Internal_ParseTree_Tag ||
                        $this->subtrees[$key + 1]->data == '')) {
                    $key ++;
                    if ($this->subtrees[$key]->data == '') {
                        continue;
                    }
                    $subtree = $parser->compiler->appendCode($subtree, $this->subtrees[$key]->to_smarty_php($parser));
                }
                if ($subtree == '') {
                    continue;
                }
                $code .= $subtree;
                continue;
            }
            $code .= $this->subtrees[$key]->to_smarty_php($parser);
        }
        return $code;
    }
}
