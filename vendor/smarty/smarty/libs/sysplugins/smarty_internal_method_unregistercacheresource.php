<?php

/**
 * Smarty Method UnregisterCacheResource
 *
 * Smarty::unregisterCacheResource() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_UnregisterCacheResource
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;

    /**
     * Registers a resource to fetch a template
     *
     * @api  Smarty::unregisterCacheResource()
     * @link http://www.smarty.net/docs/en/api.unregister.cacheresource.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $type     name of cache resource type
      *
     * @return \Smarty|\Smarty_Internal_Template
     */
    public function unregisterCacheResource(Smarty_Internal_TemplateBase $obj, $name)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        if (isset($smarty->registered_cache_resources[$name])) {
            unset($smarty->registered_cache_resources[$name]);
        }
        return $obj;
    }
}