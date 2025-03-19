<?php

/**
 * Extends All Resource
 * Resource Implementation modifying the extends-Resource to walk
 * through the template_dirs and inherit all templates of the same name
 *
 * @package Resource-examples
 * @author  Rodney Rehm
 */
class Smarty_Resource_Extendsall extends Smarty_Internal_Resource_Extends
{
    /**
     * populate Source Object with meta data from Resource
     *
     * @param  Smarty_Template_Source   $source    source object
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return void
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $uid = '';
        $sources = array();
        $timestamp = 0;
        foreach ($_template->smarty->getTemplateDir() as $key => $directory) {
            try {
                $s = Smarty_Resource::source(null, $source->smarty, 'file:' . '[' . $key . ']' . $source->name);
                if (!$s->exists) {
                    continue;
                }
                $sources[ $s->uid ] = $s;
                $uid .= $s->filepath;
                $timestamp = $s->timestamp > $timestamp ? $s->timestamp : $timestamp;
            }
            catch (SmartyException $e) {
            }
        }
        if (!$sources) {
            $source->exists = false;
            return;
        }

        $sources = array_reverse($sources, true);
        reset($sources);
        $s = current($sources);
        $source->components = $sources;
        $source->filepath = $s->filepath;
        $source->uid = sha1($uid . $_template->smarty->_joined_template_dir);
        $source->exists = true;
        $source->timestamp = $timestamp;
    }

    /*
     * Disable timestamp checks for extendsall resource.
     * The individual source components will be checked.
     *
     * @return bool
     */
    public function checkTimestamps()
    {
        return false;
    }

}
