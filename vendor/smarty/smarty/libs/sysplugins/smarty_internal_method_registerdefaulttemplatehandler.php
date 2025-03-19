<?php

/**
 * Smarty Method RegisterDefaultTemplateHandler
 *
 * Smarty::registerDefaultTemplateHandler() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_RegisterDefaultTemplateHandler
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;

    /**
     * Register template default handler
     *
     * @api  Smarty::registerDefaultTemplateHandler()
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  callable                                                       $callback class/method name
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws SmartyException              if $callback is not callable
     */
    public function registerDefaultTemplateHandler(Smarty_Internal_TemplateBase $obj, $callback)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        if (is_callable($callback)) {
            $smarty->default_template_handler_func = $callback;
        } else {
            throw new SmartyException("Default template handler not callable");
        }
        return $obj;
    }

    /**
     * get default content from template or config resource handler
     *
     * @param Smarty_Template_Source $source
     */
    public static function _getDefaultTemplate(Smarty_Template_Source $source)
    {
        if ($source->isConfig) {
            $default_handler = $source->smarty->default_config_handler_func;
        } else {
            $default_handler = $source->smarty->default_template_handler_func;
        }
        $_content = $_timestamp = null;
        $_return = call_user_func_array($default_handler, array($source->type, $source->name, &$_content, &$_timestamp,
                                                                $source->smarty));
        if (is_string($_return)) {
            $source->exists = is_file($_return);
            if ($source->exists) {
                $source->timestamp = filemtime($_return);
            }
            $source->filepath = $_return;
        } elseif ($_return === true) {
            $source->content = $_content;
            $source->timestamp = $_timestamp;
            $source->exists = true;
            $source->handler->recompiled = true;
            $source->filepath = false;
        }
    }
}