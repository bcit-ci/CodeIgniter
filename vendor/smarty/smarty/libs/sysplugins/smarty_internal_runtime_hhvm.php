<?php

/**
 * Runtime Extension Hhvm
 *
 * include patch for modified compiled or cached templates
 * HHVM does not check if file was modified when including same file multiple times
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Runtime_Hhvm
{
    /**
     * @param \Smarty_Internal_Template $_template
     * @param string                    $file file name
     *
     * @return mixed
     */
    static function includeHhvm(Smarty_Internal_Template $_template, $file)
    {
        $_smarty_tpl = $_template;
        $tmp_file = $file . preg_replace('![^\w]+!', '_', uniqid(rand(), true)) . '.php';
        file_put_contents($tmp_file, file_get_contents($file));
        $result = @include $tmp_file;
        @unlink($tmp_file);
        return $result;
    }
}