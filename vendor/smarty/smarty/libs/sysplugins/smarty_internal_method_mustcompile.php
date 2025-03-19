<?php

/**
 * Smarty Method UnloadFilter
 *
 * Smarty_Internal_Template::mustCompile() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_MustCompile
{
    /**
     * Valid for template object
     *
     * @var int
     */
    public $objMap = 2;

    /**
     * Returns if the current template must be compiled by the Smarty compiler
     * It does compare the timestamps of template source and the compiled templates and checks the force compile
     * configuration
     *
     * @param \Smarty_Internal_Template $_template
     *
     * @return bool
     * @throws \SmartyException
     */
    public function mustCompile(Smarty_Internal_Template $_template)
    {
        if (!$_template->source->exists) {
            if (isset($_template->parent) && $_template->parent->_objType == 2) {
                $parent_resource = " in '$_template->parent->template_resource}'";
            } else {
                $parent_resource = '';
            }
            throw new SmartyException("Unable to load template {$_template->source->type} '{$_template->source->name}'{$parent_resource}");
        }
        if ($_template->mustCompile === null) {
            $_template->mustCompile = (!$_template->source->handler->uncompiled &&
                ($_template->smarty->force_compile || $_template->source->handler->recompiled || !$_template->compiled->exists ||
                    ($_template->smarty->compile_check && $_template->compiled->getTimeStamp() < $_template->source->getTimeStamp())));
        }

        return $_template->mustCompile;
    }
}