<?php

/**
 * Smarty Template Resource Base Object
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 */
abstract class Smarty_Template_Resource_Base
{
    /**
     * Compiled Filepath
     *
     * @var string
     */
    public $filepath = null;

    /**
     * Compiled Timestamp
     *
     * @var integer
     */
    public $timestamp = null;

    /**
     * Compiled Existence
     *
     * @var boolean
     */
    public $exists = false;

    /**
     * Template Compile Id (Smarty_Internal_Template::$compile_id)
     *
     * @var string
     */
    public $compile_id = null;

    /**
     * Compiled Content Loaded
     *
     * @var boolean
     */
    public $processed = false;

    /**
     * unique function name for compiled template code
     *
     * @var string
     */
    public $unifunc = '';

    /**
     * flag if template does contain nocache code sections
     *
     * @var bool
     */
    public $has_nocache_code = false;

    /**
     * resource file dependency
     *
     * @var array
     */
    public $file_dependency = array();

    /**
     * Content buffer
     *
     * @var string
     */
    public $content = null;

    /**
     * required plugins
     *
     * @var array
     */
    public $required_plugins = array();

    /**
     * Included subtemplates
     *
     * @var array
     */
    public $includes = array();

    /**
     * Process resource
     *
     * @param Smarty_Internal_Template $_template template object
     */
    abstract public function process(Smarty_Internal_Template $_template);

     /**
     * get rendered template content by calling compiled or cached template code
     *
     * @param string $unifunc function with template code
     *
     * @return string
     * @throws \Exception
     */
    public function getRenderedTemplateCode(Smarty_Internal_Template $_template, $unifunc = null)
    {
        $unifunc = isset($unifunc) ? $unifunc : $this->unifunc;
        $level = ob_get_level();
        try {
            if (empty($unifunc) || !is_callable($unifunc)) {
                throw new SmartyException("Invalid compiled template for '{$_template->template_resource}'");
            }
            if (isset($_template->smarty->security_policy)) {
                $_template->smarty->security_policy->startTemplate($_template);
            }
            //
            // render compiled or saved template code
            //
            if (!isset($_template->_cache['capture_stack'])) {
                $_template->_cache['capture_stack'] = array();
            }
            $_saved_capture_level = count($_template->_cache['capture_stack']);
            $unifunc($_template);
            // any unclosed {capture} tags ?
            if ($_saved_capture_level != count($_template->_cache['capture_stack'])) {
                $_template->capture_error();
            }
            if (isset($_template->smarty->security_policy)) {
                $_template->smarty->security_policy->exitTemplate();
            }
            return null;
        }
        catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
             if (isset($_template->smarty->security_policy)) {
                $_template->smarty->security_policy->exitTemplate();
            }
            throw $e;
        }
    }

    /**
     * Get compiled time stamp
     *
     * @return int
     */
    public function getTimeStamp()
    {
        if ($this->exists && !isset($this->timestamp)) {
            $this->timestamp = @filemtime($this->filepath);
        }
        return $this->timestamp;
    }
}
