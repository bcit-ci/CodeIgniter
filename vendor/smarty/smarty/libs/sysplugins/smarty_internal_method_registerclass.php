<?php

/**
 * Smarty Method RegisterClass
 *
 * Smarty::registerClass() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_RegisterClass
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;

    /**
     * Registers static classes to be used in templates
     *
     * @api  Smarty::registerClass()
     * @link http://www.smarty.net/docs/en/api.register.class.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $class_name
     * @param  string                                                         $class_impl the referenced PHP class to
     *                                                                                    register
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws \SmartyException
     */
    public function registerClass(Smarty_Internal_TemplateBase $obj, $class_name, $class_impl)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        // test if exists
        if (!class_exists($class_impl)) {
            throw new SmartyException("Undefined class '$class_impl' in register template class");
        }
        // register the class
        $smarty->registered_classes[$class_name] = $class_impl;
        return $obj;
    }
}