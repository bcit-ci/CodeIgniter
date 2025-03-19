<?php

/**
 * Smarty Method CompileAllTemplates
 *
 * Smarty::compileAllTemplates() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_CompileAllTemplates
{
    /**
     * Valid for Smarty object
     *
     * @var int
     */
    public $objMap = 1;

    /**
     * Compile all template files
     *
     * @api  Smarty::compileAllTemplates()
     *
     * @param \Smarty $smarty
     * @param  string $extension     file extension
     * @param  bool   $force_compile force all to recompile
     * @param  int    $time_limit
     * @param  int    $max_errors
     *
     * @return integer number of template files recompiled
     */
    public function compileAllTemplates(Smarty $smarty, $extension = '.tpl', $force_compile = false, $time_limit = 0, $max_errors = null)
    {
        return $this->compileAll($smarty, $extension, $force_compile, $time_limit, $max_errors);
    }

    /**
     * Compile all template or config files
     *
     * @param \Smarty $smarty
     * @param  string $extension     template file name extension
     * @param  bool   $force_compile force all to recompile
     * @param  int    $time_limit    set maximum execution time
     * @param  int    $max_errors    set maximum allowed errors
     * @param bool    $isConfig      flag true if called for config files
     *
     * @return int number of template files compiled
     */
    protected function compileAll(Smarty $smarty, $extension, $force_compile, $time_limit, $max_errors, $isConfig = false)
    {
        // switch off time limit
        if (function_exists('set_time_limit')) {
            @set_time_limit($time_limit);
        }
        $_count = 0;
        $_error_count = 0;
        $sourceDir = $isConfig ? $smarty->getConfigDir() : $smarty->getTemplateDir();
        // loop over array of source directories
        foreach ($sourceDir as $_dir) {
            $_dir_1 = new RecursiveDirectoryIterator($_dir);
            $_dir_2 = new RecursiveIteratorIterator($_dir_1);
            foreach ($_dir_2 as $_fileinfo) {
                $_file = $_fileinfo->getFilename();
                if (substr(basename($_fileinfo->getPathname()), 0, 1) == '.' || strpos($_file, '.svn') !== false) {
                    continue;
                }
                if (!substr_compare($_file, $extension, - strlen($extension)) == 0) {
                    continue;
                }
                if ($_fileinfo->getPath() == !substr($_dir, 0, - 1)) {
                    $_file = substr($_fileinfo->getPath(), strlen($_dir)) . DS . $_file;
                }
                echo "\n<br>", $_dir, '---', $_file;
                flush();
                $_start_time = microtime(true);
                $_smarty = clone $smarty;
                $_smarty->force_compile = $force_compile;
                try {
                    /* @var Smarty_Internal_Template $_tpl */
                    $_tpl = new $smarty->template_class($_file, $_smarty);
                    $_tpl->caching = Smarty::CACHING_OFF;
                    $_tpl->source = $isConfig ? Smarty_Template_Config::load($_tpl) : Smarty_Template_Source::load($_tpl);
                    if ($_tpl->mustCompile()) {
                        $_tpl->compileTemplateSource();
                        $_count ++;
                        echo ' compiled in  ', microtime(true) - $_start_time, ' seconds';
                        flush();
                    } else {
                        echo ' is up to date';
                        flush();
                    }
                }
                catch (Exception $e) {
                    echo "\n<br>        ------>Error: ", $e->getMessage(), "<br><br>\n";
                    $_error_count ++;
                }
                // free memory
                unset($_tpl);
                $_smarty->_cache['template_objects'] = array();
                if ($max_errors !== null && $_error_count == $max_errors) {
                    echo "\n<br><br>too many errors\n";
                    exit();
                }
            }
        }
        echo "\n<br>";
        return $_count;
    }
}