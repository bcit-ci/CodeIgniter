<?php

/**
 * Smarty Method UnloadFilter
 *
 * Smarty::unloadFilter() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_UnloadFilter extends Smarty_Internal_Method_LoadFilter
{
    /**
     * load a filter of specified type and name
     *
     * @api  Smarty::unloadFilter()
     *
     * @link http://www.smarty.net/docs/en/api.unload.filter.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $type filter type
     * @param  string                                                         $name filter name
     *
     * @return bool
     */
    public function unloadFilter(Smarty_Internal_TemplateBase $obj, $type, $name)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        $this->_checkFilterType($type);
        if (isset($smarty->registered_filters[$type])) {
            $_filter_name = "smarty_{$type}filter_{$name}";
            if (isset($smarty->registered_filters[$type][$_filter_name])) {
                unset ($smarty->registered_filters[$type][$_filter_name]);
                if (empty($smarty->registered_filters[$type])) {
                    unset($smarty->registered_filters[$type]);
                }
            }
        }
        return $obj;
    }
}