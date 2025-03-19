<?php

/**
 * Smarty Method RegisterPlugin
 *
 * Smarty::registerPlugin() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_RegisterPlugin
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
     * @api  Smarty::registerPlugin()
     * @link http://www.smarty.net/docs/en/api.register.plugin.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $type       plugin type
     * @param  string                                                         $name       name of template tag
     * @param  callback                                                       $callback   PHP callback to register
     * @param  bool                                                           $cacheable  if true (default) this
     *                                                                                    function is cache able
     * @param  mixed                                                          $cache_attr caching attributes if any
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws SmartyException              when the plugin tag is invalid
     */
    public function registerPlugin(Smarty_Internal_TemplateBase $obj, $type, $name, $callback, $cacheable = true, $cache_attr = null)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        if (isset($smarty->registered_plugins[$type][$name])) {
            throw new SmartyException("Plugin tag \"{$name}\" already registered");
        } elseif (!is_callable($callback)) {
            throw new SmartyException("Plugin \"{$name}\" not callable");
        } else {
            $smarty->registered_plugins[$type][$name] = array($callback, (bool) $cacheable, (array) $cache_attr);
        }
        return $obj;
    }
}