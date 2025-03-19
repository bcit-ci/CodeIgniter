<?php

/**
 * Runtime Methods updateScope
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 **/
class Smarty_Internal_Runtime_UpdateScope
{
    /**
     * Update new assigned template variable in other effected scopes
     *
     * @param \Smarty_Internal_Template $tpl     template object
     * @param string $varName variable name
     * @param int    $scope   scope to which bubble up variable value
     */
    public function updateScope(Smarty_Internal_Template $tpl, $varName, $scope = Smarty::SCOPE_LOCAL)
    {
        if (!$scope && !$tpl->scope) {
            return;
        }
        foreach (array($scope, $tpl->scope) as $s) {
            $s = ($bubble_up = $s >= Smarty::SCOPE_BUBBLE_UP) ? $s - Smarty::SCOPE_BUBBLE_UP : $s;
            if ($bubble_up && $s) {
                $ptr = $tpl->parent;
                if (isset($ptr)) {
                    $ptr->tpl_vars[$varName] = $tpl->tpl_vars[$varName];
                    $ptr = $ptr->parent;
                }
                if ($s == Smarty::SCOPE_PARENT) {
                    continue;
                }
                while (isset($ptr) && $ptr->_objType == 2) {
                    $ptr->tpl_vars[$varName] = $tpl->tpl_vars[$varName];
                    $ptr = $ptr->parent;
                }
                if ($s == Smarty::SCOPE_TPL_ROOT) {
                    continue;
                } elseif ($s == Smarty::SCOPE_SMARTY) {
                    $tpl->smarty->tpl_vars[$varName] = $tpl->tpl_vars[$varName];
                } elseif ($s == Smarty::SCOPE_GLOBAL) {
                    Smarty::$global_tpl_vars[$varName] = $tpl->tpl_vars[$varName];
                } elseif ($s == Smarty::SCOPE_ROOT) {
                    while (isset($ptr->parent)) {
                        $ptr = $ptr->parent;
                    }
                    $ptr->tpl_vars[$varName] = $tpl->tpl_vars[$varName];
                }
            }
        }
    }
}
