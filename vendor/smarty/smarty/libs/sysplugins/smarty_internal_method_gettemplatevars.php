<?php

/**
 * Smarty Method GetTemplateVars
 *
 * Smarty::getTemplateVars() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_GetTemplateVars
{
    /**
     * Valid for all objects
     *
     * @var int
     */
    public $objMap = 7;

    /**
     * Returns a single or all template variables
     *
     * @api  Smarty::getTemplateVars()
     * @link http://www.smarty.net/docs/en/api.get.template.vars.tpl
     *
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $data
     * @param  string                                                 $varName        variable name or null
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $_ptr           optional pointer to data object
     * @param  bool                                                   $searchParents include parent templates?
     *
     * @return mixed variable value or or array of variables
     */
    public function getTemplateVars(Smarty_Internal_Data $data, $varName = null, Smarty_Internal_Data $_ptr = null, $searchParents = true)
    {
        if (isset($varName)) {
            $_var = $this->_getVariable($data, $varName, $_ptr, $searchParents, false);
            if (is_object($_var)) {
                return $_var->value;
            } else {
                return null;
            }
        } else {
            $_result = array();
            if ($_ptr === null) {
                $_ptr = $data;
            }
            while ($_ptr !== null) {
                foreach ($_ptr->tpl_vars AS $key => $var) {
                    if (!array_key_exists($key, $_result)) {
                        $_result[$key] = $var->value;
                    }
                }
                // not found, try at parent
                if ($searchParents) {
                    $_ptr = $_ptr->parent;
                } else {
                    $_ptr = null;
                }
            }
            if ($searchParents && isset(Smarty::$global_tpl_vars)) {
                foreach (Smarty::$global_tpl_vars AS $key => $var) {
                    if (!array_key_exists($key, $_result)) {
                        $_result[$key] = $var->value;
                    }
                }
            }
            return $_result;
        }
    }

    /**
     * gets the object of a Smarty variable
     *
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $data
     * @param string                                                 $varName       the name of the Smarty variable
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $_ptr           optional pointer to data object
     * @param bool                                                   $searchParents search also in parent data
     * @param bool                                                    $errorEnable
     *
     * @return \Smarty_Variable
     */
    public function _getVariable(Smarty_Internal_Data $data, $varName, Smarty_Internal_Data $_ptr = null, $searchParents = true, $errorEnable = true)
    {
        if ($_ptr === null) {
            $_ptr = $data;
        }
        while ($_ptr !== null) {
            if (isset($_ptr->tpl_vars[$varName])) {
                // found it, return it
                return $_ptr->tpl_vars[$varName];
            }
            // not found, try at parent
            if ($searchParents) {
                $_ptr = $_ptr->parent;
            } else {
                $_ptr = null;
            }
        }
        if (isset(Smarty::$global_tpl_vars[$varName])) {
            // found it, return it
            return Smarty::$global_tpl_vars[$varName];
        }
        /* @var \Smarty $smarty */
        $smarty = isset($data->smarty) ? $data->smarty : $data;
        if ($smarty->error_unassigned && $errorEnable) {
            // force a notice
            $x = $$varName;
        }

        return new Smarty_Undefined_Variable;
    }

}