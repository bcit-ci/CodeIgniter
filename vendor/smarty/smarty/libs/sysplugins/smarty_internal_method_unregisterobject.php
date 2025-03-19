<?php

/**
 * Smarty Method UnregisterObject
 *
 * Smarty::unregisterObject() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_UnregisterObject
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;

    /**
     * Registers plugin to be used in templates
     *
     * @api  Smarty::unregisterObject()
     * @link http://www.smarty.net/docs/en/api.unregister.object.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $object_name name of object
     *
     * @return \Smarty|\Smarty_Internal_Template
     */
    public function unregisterObject(Smarty_Internal_TemplateBase $obj, $object_name)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        if (isset($smarty->registered_objects[$object_name])) {
            unset($smarty->registered_objects[$object_name]);
        }
        return $obj;
    }
}