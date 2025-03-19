<?php
/**
 * Smarty Internal Plugin Compile While
 * Compiles the {while} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile While Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_While extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {while} tag
     *
     * @param  array                                       $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                       $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $compiler->loopNesting++;
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        $this->openTag($compiler, 'while', $compiler->nocache);

        if (!array_key_exists("if condition", $parameter)) {
            $compiler->trigger_template_error("missing while condition", null, true);
        }

        // maybe nocache because of nocache variables
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        $_output = "<?php\n";
        if (is_array($parameter['if condition'])) {
            if ($compiler->nocache) {
                $_nocache = ',true';
                // create nocache var to make it know for further compiling
                if (is_array($parameter['if condition']['var'])) {
                    $var = trim($parameter['if condition']['var']['var'], "'");
                } else {
                    $var = trim($parameter['if condition']['var'], "'");
                }
                if (isset($compiler->template->tpl_vars[$var])) {
                    $compiler->template->tpl_vars[$var]->nocache = true;
                } else {
                    $compiler->template->tpl_vars[$var] = new Smarty_Variable(null, true);
                }
            } else {
                $_nocache = '';
            }
            if (is_array($parameter['if condition']['var'])) {
                $_output .= "if (!isset(\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var']['var'] .
                    "]) || !is_array(\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var']['var'] .
                    "]->value)) \$_smarty_tpl->smarty->ext->_var->createLocalArrayVariable(\$_smarty_tpl, " . $parameter['if condition']['var']['var'] .
                    "$_nocache);\n";
                $_output .= "while (\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var']['var'] . "]->value" .
                    $parameter['if condition']['var']['smarty_internal_index'] . " = " .
                    $parameter['if condition']['value'] . ") {?>";
            } else {
                $_output .= "if (!isset(\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var'] .
                    "])) \$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var'] .
                    "] = new Smarty_Variable(null{$_nocache});";
                $_output .= "while (\$_smarty_tpl->tpl_vars[" . $parameter['if condition']['var'] . "]->value = " .
                    $parameter['if condition']['value'] . ") {?>";
            }
        } else {
            $_output .= "while ({$parameter['if condition']}) {?>";
         }
        return $_output;
    }
}

/**
 * Smarty Internal Plugin Compile Whileclose Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Whileclose extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {/while} tag
     *
     * @param  array                                       $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $compiler->loopNesting--;
        // must endblock be nocache?
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }
        $compiler->nocache = $this->closeTag($compiler, array('while'));
        return "<?php }?>\n";
    }
}
