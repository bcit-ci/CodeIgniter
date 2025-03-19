<?php

/**
 * Smarty Resource Data Object
 * Meta Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 * @property string $content compiled content
 */
class Smarty_Template_Compiled extends Smarty_Template_Resource_Base
{

    /**
     * nocache hash
     *
     * @var string|null
     */
    public $nocache_hash = null;

    /**
     * get a Compiled Object of this source
     *
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return Smarty_Template_Compiled compiled object
     */
    static function load($_template)
    {
        // check runtime cache
        if (!$_template->source->handler->recompiled &&
            ($_template->smarty->resource_cache_mode & Smarty::RESOURCE_CACHE_ON)
        ) {
            $_cache_key = $_template->source->unique_resource . '#';
            if ($_template->caching) {
                $_cache_key .= 'caching#';
            }
            $_cache_key .= $_template->compile_id;
            if (isset($_template->source->compileds[$_cache_key])) {
                return $_template->source->compileds[$_cache_key];
            }
        }
        $compiled = new Smarty_Template_Compiled();
        if ($_template->source->handler->hasCompiledHandler) {
            $_template->source->handler->populateCompiledFilepath($compiled, $_template);
        } else {
            $compiled->populateCompiledFilepath($_template);
        }
        // runtime cache
        if (!$_template->source->handler->recompiled &&
            ($_template->smarty->resource_cache_mode & Smarty::RESOURCE_CACHE_ON)
        ) {
            $_template->source->compileds[$_cache_key] = $compiled;
        }
        return $compiled;
    }

    /**
     * populate Compiled Object with compiled filepath
     *
     * @param Smarty_Internal_Template $_template template object
     **/
    public function populateCompiledFilepath(Smarty_Internal_Template $_template)
    {
        $_compile_id = isset($_template->compile_id) ? preg_replace('![^\w]+!', '_', $_template->compile_id) : null;
        if ($_template->source->isConfig) {
            $_flag = '_' .
                ((int) $_template->smarty->config_read_hidden + (int) $_template->smarty->config_booleanize * 2 +
                    (int) $_template->smarty->config_overwrite * 4);
        } else {
            $_flag =
                '_' . ((int) $_template->smarty->merge_compiled_includes + (int) $_template->smarty->escape_html * 2);
        }
        $_filepath = $_template->source->uid . $_flag;
        // if use_sub_dirs, break file into directories
        if ($_template->smarty->use_sub_dirs) {
            $_filepath = substr($_filepath, 0, 2) . DS . substr($_filepath, 2, 2) . DS . substr($_filepath, 4, 2) . DS .
                $_filepath;
        }
        $_compile_dir_sep = $_template->smarty->use_sub_dirs ? DS : '^';
        if (isset($_compile_id)) {
            $_filepath = $_compile_id . $_compile_dir_sep . $_filepath;
        }
        // caching token
        if ($_template->caching) {
            $_cache = '.cache';
        } else {
            $_cache = '';
        }
        $_compile_dir = $_template->smarty->getCompileDir();
        // set basename if not specified
        $_basename = $_template->source->handler->getBasename($_template->source);
        if ($_basename === null) {
            $_basename = basename(preg_replace('![^\w]+!', '_', $_template->source->name));
        }
        // separate (optional) basename by dot
        if ($_basename) {
            $_basename = '.' . $_basename;
        }

        $this->filepath = $_compile_dir . $_filepath . '.' . $_template->source->type . $_basename . $_cache . '.php';
        $this->exists = is_file($this->filepath);
        if (!$this->exists) {
            $this->timestamp = false;
        }
    }

    /**
     * load compiled template or compile from source
     *
     * @param Smarty_Internal_Template $_template
     *
     * @throws Exception
     */
    public function process(Smarty_Internal_Template $_template)
    {
        $_smarty_tpl = $_template;
        if ($_template->source->handler->recompiled || !$_template->compiled->exists ||
            $_template->smarty->force_compile || ($_template->smarty->compile_check &&
                $_template->source->getTimeStamp() > $_template->compiled->getTimeStamp())
        ) {
            $this->compileTemplateSource($_template);
            $compileCheck = $_template->smarty->compile_check;
            $_template->smarty->compile_check = false;
            if ($_template->source->handler->recompiled) {
                $level = ob_get_level();
                ob_start();
                try {
                    eval("?>" . $this->content);
                }
                catch (Exception $e) {
                    while (ob_get_level() > $level) {
                        ob_end_clean();
                    }
                    throw $e;
                }
                ob_get_clean();
                $this->content = null;
            } else {
                $this->loadCompiledTemplate($_template);
            }
            $_template->smarty->compile_check = $compileCheck;
        } else {
            $_template->mustCompile = true;
            @include($_template->compiled->filepath);
            if ($_template->mustCompile) {
                $this->compileTemplateSource($_template);
                $compileCheck = $_template->smarty->compile_check;
                $_template->smarty->compile_check = false;
                $this->loadCompiledTemplate($_template);
                $_template->smarty->compile_check = $compileCheck;
            }
        }
        $_template->smarty->ext->_subTemplate->registerSubTemplates($_template);

        $this->processed = true;
    }

    /**
     * Load fresh compiled template by including the PHP file
     * HHVM requires a work around because of a PHP incompatibility
     *
     * @param \Smarty_Internal_Template $_template
     */
    private function loadCompiledTemplate(Smarty_Internal_Template $_template)
    {
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($_template->compiled->filepath);
        }
        $_smarty_tpl = $_template;
        if (defined('HHVM_VERSION')) {
            $_template->smarty->ext->_hhvm->includeHhvm($_template, $_template->compiled->filepath);
        } else {
            include($_template->compiled->filepath);
        }
    }

    /**
     * render compiled template code
     *
     * @param Smarty_Internal_Template $_template
     *
     * @return string
     * @throws Exception
     */
    public function render(Smarty_Internal_Template $_template)
    {
        if ($_template->smarty->debugging) {
            $_template->smarty->_debug->start_render($_template);
        }
        if (!$this->processed) {
            $this->process($_template);
        }
        if (isset($_template->cached)) {
            $_template->cached->file_dependency =
                array_merge($_template->cached->file_dependency, $this->file_dependency);
        }
        $this->getRenderedTemplateCode($_template);
        if ($_template->caching && $this->has_nocache_code) {
            $_template->cached->hashes[$this->nocache_hash] = true;
        }
        if (isset($_template->parent) && $_template->parent->_objType == 2 && !empty($_template->tpl_function)) {
            $_template->parent->tpl_function = array_merge($_template->parent->tpl_function, $_template->tpl_function);
        }
        if ($_template->smarty->debugging) {
            $_template->smarty->_debug->end_render($_template);
        }
    }

    /**
     * compile template from source
     *
     * @param Smarty_Internal_Template $_template
     *
     * @return string
     * @throws Exception
     */
    public function compileTemplateSource(Smarty_Internal_Template $_template)
    {
        $_template->source->compileds = array();
        $this->file_dependency = array();
        $this->tpl_function = array();
        $this->includes = array();
        $this->nocache_hash = null;
        $this->unifunc = null;
        // compile locking
        if (!$_template->source->handler->recompiled) {
            if ($saved_timestamp = $_template->compiled->getTimeStamp()) {
                touch($_template->compiled->filepath);
            }
        }
        // call compiler
        try {
            $_template->loadCompiler();
            $code = $_template->compiler->compileTemplate($_template);
        }
        catch (Exception $e) {
            // restore old timestamp in case of error
            if (!$_template->source->handler->recompiled && $saved_timestamp) {
                touch($_template->compiled->filepath, $saved_timestamp);
            }
            throw $e;
        }
        // compiling succeeded
        if ($_template->compiler->write_compiled_code) {
            // write compiled template
            $this->write($_template, $code);
            $code = '';
        }
        // release compiler object to free memory
        unset($_template->compiler);
        return $code;
    }

    /**
     * Write compiled code by handler
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $code      compiled code
     *
     * @return boolean success
     */
    public function write(Smarty_Internal_Template $_template, $code)
    {
        if (!$_template->source->handler->recompiled) {
            if ($_template->smarty->ext->_writeFile->writeFile($this->filepath, $code, $_template->smarty) === true) {
                $this->timestamp = $this->exists = is_file($this->filepath);
                if ($this->exists) {
                    $this->timestamp = filemtime($this->filepath);
                    return true;
                }
            }
            return false;
        } else {
            $this->content = $code;
        }
        $this->timestamp = time();
        $this->exists = true;
        return true;
    }

    /**
     * Read compiled content from handler
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @return string content
     */
    public function read(Smarty_Internal_Template $_template)
    {
        if (!$_template->source->handler->recompiled) {
            return file_get_contents($this->filepath);
        }
        return isset($this->content) ? $this->content : false;
    }
}
