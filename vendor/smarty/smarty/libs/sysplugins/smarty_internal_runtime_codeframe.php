<?php
/**
 * Smarty Internal Extension
 * This file contains the Smarty template extension to create a code frame
 *
 * @package    Smarty
 * @subpackage Template
 * @author     Uwe Tews
 */

/**
 * Class Smarty_Internal_Extension_CodeFrame
 * Create code frame for compiled and cached templates
 */
class Smarty_Internal_Runtime_CodeFrame
{
    /**
     * Create code frame for compiled and cached templates
     *
     * @param Smarty_Internal_Template              $_template
     * @param string                                $content optional template content
     * @param string                                $functions compiled template function and block code
     * @param bool                                  $cache   flag for cache file
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     *
     * @return string
     */
    public function create(Smarty_Internal_Template $_template, $content = '', $functions = '', $cache = false,
                           Smarty_Internal_TemplateCompilerBase $compiler = null)
    {
        // build property code
        $properties[ 'has_nocache_code' ] = $_template->compiled->has_nocache_code;
        $properties[ 'version' ] = Smarty::SMARTY_VERSION;
        $properties[ 'unifunc' ] = 'content_' . str_replace(array('.', ','), '_', uniqid('', true));
        if (!$cache) {
            $properties[ 'file_dependency' ] = $_template->compiled->file_dependency;
            $properties[ 'includes' ] = $_template->compiled->includes;
            if (!empty($compiler->tpl_function)) {
                $properties[ 'tpl_function' ] = $compiler->tpl_function;
            }
        } else {
            $properties[ 'file_dependency' ] = $_template->cached->file_dependency;
            $properties[ 'cache_lifetime' ] = $_template->cache_lifetime;
            if (!empty($_template->tpl_function)) {
                $properties[ 'tpl_function' ] = $_template->tpl_function;
            }
        }
        $output = "<?php\n";
        $output .= "/* Smarty version " . Smarty::SMARTY_VERSION . ", created on " . strftime("%Y-%m-%d %H:%M:%S") .
            "\n  from \"" . $_template->source->filepath . "\" */\n\n";

        $dec = "\$_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties(\$_smarty_tpl, " .
            var_export($properties, true) . ',' . ($cache ? 'true' : 'false') . ")";
        $output .= "if ({$dec}) {\n";
        $output .= "function {$properties['unifunc']} (\$_smarty_tpl) {\n";
        // include code for plugins
        if (!$cache) {
            if (!empty($_template->compiled->required_plugins[ 'compiled' ])) {
                foreach ($_template->compiled->required_plugins[ 'compiled' ] as $tmp) {
                    foreach ($tmp as $data) {
                        $file = addslashes($data[ 'file' ]);
                        if (is_array($data[ 'function' ])) {
                            $output .= "if (!is_callable(array('{$data['function'][0]}','{$data['function'][1]}'))) require_once '{$file}';\n";
                        } else {
                            $output .= "if (!is_callable('{$data['function']}')) require_once '{$file}';\n";
                        }
                    }
                }
            }
            if ($_template->caching && !empty($_template->compiled->required_plugins[ 'nocache' ])) {
                $_template->compiled->has_nocache_code = true;
                $output .= "echo '/*%%SmartyNocache:{$_template->compiled->nocache_hash}%%*/<?php \$_smarty = \$_smarty_tpl->smarty; ";
                foreach ($_template->compiled->required_plugins[ 'nocache' ] as $tmp) {
                    foreach ($tmp as $data) {
                        $file = addslashes($data[ 'file' ]);
                        if (is_Array($data[ 'function' ])) {
                            $output .= addslashes("if (!is_callable(array('{$data['function'][0]}','{$data['function'][1]}'))) require_once '{$file}';\n");
                        } else {
                            $output .= addslashes("if (!is_callable('{$data['function']}')) require_once '{$file}';\n");
                        }
                    }
                }
                $output .= "?>/*/%%SmartyNocache:{$_template->compiled->nocache_hash}%%*/';\n";
            }
        }
        $output .= "?>\n";
        $output .= $content;
        $output .= "<?php }\n?>";
        $output .= $functions;
        $output .= "<?php }\n";
        // remove unneeded PHP tags
        return preg_replace(array('/\s*\?>[\n]?<\?php\s*/', '/\?>\s*$/'), array("\n", ''), $output);
    }
}