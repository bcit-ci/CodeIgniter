<?php

/**
 * Smarty Method GetAutoloadFilters
 *
 * Smarty::getAutoloadFilters() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_GetAutoloadFilters extends Smarty_Internal_Method_SetAutoloadFilters
{

    /**
     * Get autoload filters
     *
     * @api Smarty::getAutoloadFilters()
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $type type of filter to get auto loads
     *                                                                              for. Defaults to all autoload
     *                                                                              filters
     *
     * @return array  array( 'type1' => array( 'filter1', 'filter2', … ) ) or array( 'filter1', 'filter2', …) if $type
     *                was specified
     */
    public function getAutoloadFilters(Smarty_Internal_TemplateBase $obj, $type = null)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        if ($type !== null) {
            $this->_checkFilterType($type);
            return isset($smarty->autoload_filters[$type]) ? $smarty->autoload_filters[$type] : array();
        }
        return $smarty->autoload_filters;
    }
}