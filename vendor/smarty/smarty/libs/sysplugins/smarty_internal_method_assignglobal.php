<?php

/**
 * Smarty Method AssignGlobal
 *
 * Smarty::assignGlobal() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_AssignGlobal
{
    /**
     * Valid for all objects
     *
     * @var int
     */
    public $objMap = 7;

     /**
     * assigns a global Smarty variable
     *
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $data
     * @param  string  $varName the global variable name
     * @param  mixed   $value   the value to assign
     * @param  boolean $nocache if true any output of this variable will be not cached
     *
     * @return \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty
     */
    public function assignGlobal(Smarty_Internal_Data $data, $varName, $value = null, $nocache = false)
    {
        if ($varName != '') {
            Smarty::$global_tpl_vars[$varName] = new Smarty_Variable($value, $nocache);
            $ptr = $data;
            while ($ptr->_objType == 2) {
                $ptr->tpl_vars[$varName] = clone Smarty::$global_tpl_vars[$varName];
                $ptr = $ptr->parent;
            }
        }
        return $data;
    }
}