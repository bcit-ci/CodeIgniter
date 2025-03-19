<?php

/**
 * Smarty Method AssignByRef
 *
 * Smarty::assignByRef() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_AssignByRef
{

    /**
     * assigns values to template variables by reference
     *
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $data
     * @param string                                                  $tpl_var the template variable name
     * @param                                                         $value
     * @param  boolean                                                $nocache if true any output of this variable will be not cached
     *
     * @return \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty
     */
    public function assignByRef(Smarty_Internal_Data $data, $tpl_var, &$value, $nocache)
    {
        if ($tpl_var != '') {
            $data->tpl_vars[$tpl_var] = new Smarty_Variable(null, $nocache);
            $data->tpl_vars[$tpl_var]->value = &$value;
            if ($data->_objType == 2 && $data->scope) {
                $data->ext->_updateScope->updateScope($data, $tpl_var);
            }
        }
        return $data;
    }
}