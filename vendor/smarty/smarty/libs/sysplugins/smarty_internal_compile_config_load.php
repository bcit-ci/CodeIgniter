<?php
/**
 * Smarty Internal Plugin Compile Config Load
 * Compiles the {config load} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Config Load Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Config_Load extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('file');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('file', 'section');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('section', 'scope', 'bubble_up');

    /**
     * Valid scope names
     * 
     * @var array
     */
    public $valid_scopes = array('local'  => true, 'parent' => true, 'root' => true, 'global' => true,
                                 'smarty' => true, 'tpl_root' => true);

    /**
     * Compiles code for the {config_load} tag
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        if ($_attr['nocache'] === true) {
            $compiler->trigger_template_error('nocache option not allowed', null, true);
        }

        // save possible attributes
        $conf_file = $_attr['file'];
        if (isset($_attr['section'])) {
            $section = $_attr['section'];
        } else {
            $section = 'null';
        }
        $_scope = Smarty::SCOPE_LOCAL;
        if (isset($_attr['scope'])) {
            $_attr['scope'] = trim($_attr['scope'], "'\"");
            if (!isset($this->valid_scopes[$_attr['scope']])) {
                $compiler->trigger_template_error("illegal value '{$_attr['scope']}' for \"scope\" attribute", null, true);
            }
            if ($_attr['scope'] != 'local') {
                if ($_attr['scope'] == 'parent') {
                    $_scope = Smarty::SCOPE_PARENT;
                } elseif ($_attr['scope'] == 'root') {
                    $_scope = Smarty::SCOPE_ROOT;
                } elseif ($_attr['scope'] == 'global') {
                    $_scope = Smarty::SCOPE_GLOBAL;
                } elseif ($_attr['scope'] == 'smarty') {
                    $_scope = Smarty::SCOPE_SMARTY;
                } elseif ($_attr['scope'] == 'tpl_root') {
                    $_scope = Smarty::SCOPE_TPL_ROOT;
                }
                $_scope += (isset($_attr['bubble_up']) && $_attr['bubble_up'] == 'false') ? 0 : Smarty::SCOPE_BUBBLE_UP;
            }
        }

        // create config object
        $_output =
            "<?php\n\$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile(\$_smarty_tpl, {$conf_file}, {$section}, {$_scope});\n?>\n";

        return $_output;
    }
}
