<?php
/**
 * Smarty Internal Plugin Compile Foreach
 * Compiles the {foreach} {foreachelse} {/foreach} tags
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Foreach Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreach extends Smarty_Internal_Compile_Private_ForeachSection
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('from', 'item');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('name', 'key');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('from', 'item', 'key', 'name');

    /**
     * counter
     *
     * @var int
     */
    public $counter = 0;

    /**
     * Name of this tag
     *
     * @var string
     */
    public $tagName = 'foreach';

    /**
     * Valid properties of $smarty.foreach.name.xxx variable
     *
     * @var array
     */
    public $nameProperties = array('first', 'last', 'index', 'iteration', 'show', 'total');

    /**
     * Valid properties of $item@xxx variable
     *
     * @var array
     */
    public $itemProperties = array('first', 'last', 'index', 'iteration', 'show', 'total', 'key');

    /**
     * Flag if tag had name attribute
     *
     * @var bool
     */
    public $isNamed = false;

    /**
     * Compiles code for the {foreach} tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $compiler->loopNesting ++;
        // init
        $this->isNamed = false;
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        $from = $_attr['from'];
        $item = $compiler->getId($_attr['item']);
        if ($item === false) {
            $item = $compiler->getVariableName($_attr['item']);
        }
        $attributes = array('item' => $item);
        if (isset($_attr['key'])) {
            $key = $compiler->getId($_attr['key']);
            if ($key === false) {
                $key = $compiler->getVariableName($_attr['key']);
            }
            $attributes['key'] = $key;
        }
        if (isset($_attr['name'])) {
            $this->isNamed = true;
            $attributes['name'] = $compiler->getId($_attr['name']);
        }
        foreach ($attributes as $a => $v) {
            if ($v === false) {
                $compiler->trigger_template_error("'{$a}' attribute/variable has illegal value", null, true);
            }
        }
        $fromName = $compiler->getVariableName($_attr['from']);
        if ($fromName) {
            foreach (array('item', 'key') as $a) {
                if (isset($attributes[$a]) && $attributes[$a] == $fromName) {
                    $compiler->trigger_template_error("'{$a}' and 'from' may not have same variable name '{$fromName}'",
                                                      null, true);
                }
            }
        }

        $itemVar = "\$_smarty_tpl->tpl_vars['{$item}']";
        $local = '$__foreach_' . (isset($attributes['name']) ? $attributes['name'] : $attributes['item']) . '_' .
            $this->counter ++ . '_';
        $needIteration = false;
        // search for used tag attributes
        $itemAttr = array();
        $namedAttr = array();
        $this->scanForProperties($attributes, $compiler);
        if (!empty($this->matchResults['item'])) {
            $itemAttr = $this->matchResults['item'];
        }
        if (!empty($this->matchResults['named'])) {
            $namedAttr = $this->matchResults['named'];
        }
        if (isset($itemAttr['last'])) {
            $needIteration = true;
        }
        if (isset($namedAttr['last'])) {
            $needIteration = true;
        }

        $keyTerm = '';
        if (isset($itemAttr['key'])) {
            $keyTerm = "{$itemVar}->key => ";
        } elseif (isset($attributes['key'])) {
            $keyTerm = "\$_smarty_tpl->tpl_vars['{$key}']->value => ";
        }

        $saveVars = array();
        $restoreVars = array();
        if ($this->isNamed) {
            $foreachVar = "\$_smarty_tpl->tpl_vars['__smarty_foreach_{$attributes['name']}']";
            if (!empty($namedAttr)) {
                $saveVars['saved'] = "isset({$foreachVar}) ? {$foreachVar} : false;";
                $restoreVars[] = "if ({$local}saved) {\n{$foreachVar} = {$local}saved;\n}\n";
            }
        }
        foreach (array('item', 'key') as $a) {
            if (isset($attributes[$a])) {
                $saveVars['saved_' . $a] =
                    "isset(\$_smarty_tpl->tpl_vars['{$attributes[$a]}']) ? \$_smarty_tpl->tpl_vars['{$attributes[$a]}'] : false;";
                $restoreVars[] =
                    "if ({$local}saved_{$a}) {\n\$_smarty_tpl->tpl_vars['{$attributes[$a]}'] = {$local}saved_{$a};\n}\n";
            }
        }
        $this->openTag($compiler, 'foreach',
                       array('foreach', $compiler->nocache, $local, $restoreVars, $itemVar, true));
        // maybe nocache because of nocache variables
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;

        // generate output code
        $output = "<?php\n";
        $output .= "\$_from = $from;\n";
        $output .= "if (!is_array(\$_from) && !is_object(\$_from)) {\n";
        $output .= "settype(\$_from, 'array');\n";
        $output .= "}\n";
        foreach ($saveVars as $k => $code) {
            $output .= "{$local}{$k} = {$code}\n";
        }
        if (isset($itemAttr['show']) || isset($itemAttr['total']) || isset($namedAttr['total']) || isset($namedAttr['show']) || isset($itemAttr['last']) || isset($namedAttr['last'])) {
            $output .= "{$local}total = \$_smarty_tpl->smarty->ext->_foreach->count(\$_from);\n";
        }
        $output .= "{$itemVar} = new Smarty_Variable();\n";
        if (isset($itemAttr['show'])) {
            $output .= "{$itemVar}->show = ({$local}total > 0);\n";
        }
        if (isset($itemAttr['total'])) {
            $output .= "{$itemVar}->total= {$local}total;\n";
        }
        if ($this->isNamed) {
            $prop = array();
            if (isset($namedAttr['total'])) {
                $prop['total'] = "'total' => {$local}total";
            }
            if (isset($namedAttr['iteration'])) {
                $prop['iteration'] = "'iteration' => 0";
            }
            if (isset($namedAttr['index'])) {
                $prop['index'] = "'index' => -1";
            }
            if (isset($namedAttr['show'])) {
                $prop['show'] = "'show' => ({$local}total > 0)";
            }
            if (!empty($namedAttr)) {
                $_vars = 'array(' . join(', ', $prop) . ')';
                $output .= "{$foreachVar} = new Smarty_Variable({$_vars});\n";
            }
        }
        if (isset($attributes['key'])) {
            $output .= "\$_smarty_tpl->tpl_vars['{$key}'] = new Smarty_Variable();\n";
        }
        if (isset($namedAttr['first']) || isset($itemAttr['first'])) {
            $output .= "{$local}first = true;\n";
        }
        if (isset($itemAttr['iteration'])) {
            $output .= "{$itemVar}->iteration=0;\n";
        }
        if (isset($itemAttr['index'])) {
            $output .= "{$itemVar}->index=-1;\n";
        }
        if ($needIteration) {
            $output .= "{$local}iteration=0;\n";
        }
        $output .= "{$itemVar}->_loop = false;\n";
        $output .= "foreach (\$_from as {$keyTerm}{$itemVar}->value) {\n";
        $output .= "{$itemVar}->_loop = true;\n";
        if (isset($attributes['key']) && isset($itemAttr['key'])) {
            $output .= "\$_smarty_tpl->tpl_vars['{$key}']->value = {$itemVar}->key;\n";
        }
        if (isset($itemAttr['iteration'])) {
            $output .= "{$itemVar}->iteration++;\n";
        }
        if (isset($itemAttr['index'])) {
            $output .= "{$itemVar}->index++;\n";
        }
        if ($needIteration) {
            $output .= "{$local}iteration++;\n";
        }
        if (isset($itemAttr['first'])) {
            $output .= "{$itemVar}->first = {$local}first;\n";
        }
        if (isset($itemAttr['last'])) {
            $output .= "{$itemVar}->last = {$local}iteration == {$local}total;\n";
        }
        if ($this->isNamed) {
            if (isset($namedAttr['iteration'])) {
                $output .= "{$foreachVar}->value['iteration']++;\n";
            }
            if (isset($namedAttr['index'])) {
                $output .= "{$foreachVar}->value['index']++;\n";
            }
            if (isset($namedAttr['first'])) {
                $output .= "{$foreachVar}->value['first'] = {$local}first;\n";
            }
            if (isset($namedAttr['last'])) {
                $output .= "{$foreachVar}->value['last'] = {$local}iteration == {$local}total;\n";
            }
        }
        if (isset($namedAttr['first']) || isset($itemAttr['first'])) {
            $output .= "{$local}first = false;\n";
        }
        $output .= "{$local}saved_local_item = {$itemVar};\n";
        $output .= "?>";

        return $output;
    }
}

/**
 * Smarty Internal Plugin Compile Foreachelse Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreachelse extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {foreachelse} tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        list($openTag, $nocache, $local, $restoreVars, $itemVar, $foo) = $this->closeTag($compiler, array('foreach'));
        $this->openTag($compiler, 'foreachelse', array('foreachelse', $nocache, $local, $restoreVars, $itemVar, false));
        $output = "<?php\n";
        $output .= "{$itemVar} = {$local}saved_local_item;\n";
        $output .= "}\n";
        $output .= "if (!{$itemVar}->_loop) {\n?>";
        return $output;
    }
}

/**
 * Smarty Internal Plugin Compile Foreachclose Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreachclose extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {/foreach} tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $compiler->loopNesting --;
        // must endblock be nocache?
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($openTag, $compiler->nocache, $local, $restoreVars, $itemVar, $restore) =
            $this->closeTag($compiler, array('foreach', 'foreachelse'));
        $output = "<?php\n";

        if ($restore) {
            $output .= "{$itemVar} = {$local}saved_local_item;\n";
        }
        $output .= "}\n";
        foreach ($restoreVars as $restore) {
            $output .= $restore;
        }
        $output .= "?>";

        return $output;
    }
}
