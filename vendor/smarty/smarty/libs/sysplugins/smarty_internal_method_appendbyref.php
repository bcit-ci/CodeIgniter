<?php

/**
 * Smarty Method AppendByRef
 *
 * Smarty::appendByRef() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_AppendByRef
{

    /**
     * appends values to template variables by reference
     *
     * @api  Smarty::appendByRef()
     * @link http://www.smarty.net/docs/en/api.append.by.ref.tpl
     *
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $data
     * @param  string                                                 $tpl_var the template variable name
     * @param  mixed                                                  &$value  the referenced value to append
     * @param  bool                                                   $merge   flag if array elements shall be merged
     *
     * @return \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty
     */
    public static function appendByRef(Smarty_Internal_Data $data, $tpl_var, &$value, $merge = false)
    {
        if ($tpl_var != '' && isset($value)) {
            if (!isset($data->tpl_vars[$tpl_var])) {
                $data->tpl_vars[$tpl_var] = new Smarty_Variable();
            }
            if (!is_array($data->tpl_vars[$tpl_var]->value)) {
                settype($data->tpl_vars[$tpl_var]->value, 'array');
            }
            if ($merge && is_array($value)) {
                foreach ($value as $_key => $_val) {
                    $data->tpl_vars[$tpl_var]->value[$_key] = &$value[$_key];
                }
            } else {
                $data->tpl_vars[$tpl_var]->value[] = &$value;
            }
            if ($data->_objType == 2 && $data->scope) {
                $data->ext->_updateScope->updateScope($data, $tpl_var);
            }
        }
        return $data;
    }
}