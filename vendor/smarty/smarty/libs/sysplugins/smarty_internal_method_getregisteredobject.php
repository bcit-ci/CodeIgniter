<?php

/**
 * Smarty Method GetRegisteredObject
 *
 * Smarty::getRegisteredObject() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_GetRegisteredObject
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;

    /**
     * return a reference to a registered object
     *
     * @api  Smarty::getRegisteredObject()
     * @link http://www.smarty.net/docs/en/api.get.registered.object.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $object_name object name
     *
     * @return object
     * @throws \SmartyException if no such object is found
     */
    public function getRegisteredObject(Smarty_Internal_TemplateBase $obj, $object_name)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        if (!isset($smarty->registered_objects[$object_name])) {
            throw new SmartyException("'$object_name' is not a registered object");
        }
        if (!is_object($smarty->registered_objects[$object_name][0])) {
            throw new SmartyException("registered '$object_name' is not an object");
        }
        return $smarty->registered_objects[$object_name][0];
    }
}