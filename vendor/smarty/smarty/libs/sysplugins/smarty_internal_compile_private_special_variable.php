<?php
/**
 * Smarty Internal Plugin Compile Special Smarty Variable
 * Compiles the special $smarty variables
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile special Smarty Variable Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Private_Special_Variable extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the special $smarty variables
     *
     * @param  array                                       $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase        $compiler compiler object
     * @param                                              $parameter
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $_index = preg_split("/\]\[/", substr($parameter, 1, strlen($parameter) - 2));
        $variable = strtolower($compiler->getId($_index[ 0 ]));
        if ($variable === false) {
            $compiler->trigger_template_error("special \$Smarty variable name index can not be variable", null, true);
        }
        if (!isset($compiler->smarty->security_policy) ||
            $compiler->smarty->security_policy->isTrustedSpecialSmartyVar($variable, $compiler)
        ) {
            switch ($variable) {
                case 'foreach':
                case 'section':
                    if (!isset($compiler->_tag_objects[ $variable ])) {
                        $class = 'Smarty_Internal_Compile_' . ucfirst($variable);
                        $compiler->_tag_objects[ $variable ] = new $class;
                    }
                    return $compiler->_tag_objects[ $variable ]->compileSpecialVariable(array(), $compiler, $_index);
                case 'capture':
                    if (class_exists('Smarty_Internal_Compile_Capture')) {
                        return Smarty_Internal_Compile_Capture::compileSpecialVariable(array(), $compiler, $_index);
                    }
                    return '';
                case 'now':
                    return 'time()';
                case 'cookies':
                    if (isset($compiler->smarty->security_policy) &&
                        !$compiler->smarty->security_policy->allow_super_globals
                    ) {
                        $compiler->trigger_template_error("(secure mode) super globals not permitted");
                        break;
                    }
                    $compiled_ref = '$_COOKIE';
                    break;
                case 'get':
                case 'post':
                case 'env':
                case 'server':
                case 'session':
                case 'request':
                    if (isset($compiler->smarty->security_policy) &&
                        !$compiler->smarty->security_policy->allow_super_globals
                    ) {
                        $compiler->trigger_template_error("(secure mode) super globals not permitted");
                        break;
                    }
                    $compiled_ref = '$_' . strtoupper($variable);
                    break;

                case 'template':
                    return 'basename($_smarty_tpl->source->filepath)';

                case 'template_object':
                    return '$_smarty_tpl';

                case 'current_dir':
                    return 'dirname($_smarty_tpl->source->filepath)';

                case 'version':
                     return "Smarty::SMARTY_VERSION";

                case 'const':
                    if (isset($compiler->smarty->security_policy) &&
                        !$compiler->smarty->security_policy->allow_constants
                    ) {
                        $compiler->trigger_template_error("(secure mode) constants not permitted");
                        break;
                    }
                    if (strpos($_index[ 1 ], '$') === false && strpos($_index[ 1 ], '\'') === false) {
                        return "@constant('{$_index[1]}')";
                    } else {
                        return "@constant({$_index[1]})";
                    }

                case 'config':
                    if (isset($_index[ 2 ])) {
                        return "(is_array(\$tmp = \$_smarty_tpl->smarty->ext->configload->_getConfigVariable(\$_smarty_tpl, $_index[1])) ? \$tmp[$_index[2]] : null)";
                    } else {
                        return "\$_smarty_tpl->smarty->ext->configload->_getConfigVariable(\$_smarty_tpl, $_index[1])";
                    }
                case 'ldelim':
                    return "\$_smarty_tpl->smarty->left_delimiter";
                case 'rdelim':
                    return "\$_smarty_tpl->smarty->right_delimiter";
                default:
                    $compiler->trigger_template_error('$smarty.' . trim($_index[ 0 ], "'") . ' is not defined');
                    break;
            }
            if (isset($_index[ 1 ])) {
                array_shift($_index);
                foreach ($_index as $_ind) {
                    $compiled_ref = $compiled_ref . "[$_ind]";
                }
            }
            return $compiled_ref;
        }
    }
}
