<?php

/**
 * Smarty Resource Data Object
 * Meta Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 *
 */
class Smarty_Template_Source
{
    /**
     * Unique Template ID
     *
     * @var string
     */
    public $uid = null;

    /**
     * Template Resource (Smarty_Internal_Template::$template_resource)
     *
     * @var string
     */
    public $resource = null;

    /**
     * Resource Type
     *
     * @var string
     */
    public $type = null;

    /**
     * Resource Name
     *
     * @var string
     */
    public $name = null;

    /**
     * Unique Resource Name
     *
     * @var string
     */
    public $unique_resource = null;

    /**
     * Source Filepath
     *
     * @var string
     */
    public $filepath = null;

    /**
     * Source Timestamp
     *
     * @var integer
     */
    public $timestamp = null;

    /**
     * Source Existence
     *
     * @var boolean
     */
    public $exists = false;

    /**
     * Source File Base name
     *
     * @var string
     */
    public $basename = null;

    /**
     * The Components an extended template is made of
     *
     * @var \Smarty_Template_Source[]
     */
    public $components = null;

    /**
     * Resource Handler
     *
     * @var \Smarty_Resource
     */
    public $handler = null;

    /**
     * Smarty instance
     *
     * @var Smarty
     */
    public $smarty = null;

    /**
     * Resource is source
     *
     * @var bool
     */
    public $isConfig = false;

    /**
     * cache for Smarty_Template_Compiled instances
     *
     * @var Smarty_Template_Compiled[]
     */
    public $compileds = array();

    /**
     * Template source content eventually set by default handler
     *
     * @var string
     */
    public $content = null;

    /**
     * create Source Object container
     *
     * @param Smarty_Resource $handler  Resource Handler this source object communicates with
     * @param Smarty          $smarty   Smarty instance this source object belongs to
     * @param string          $resource full template_resource
     * @param string          $type     type of resource
     * @param string          $name     resource name
     *
     */
    public function __construct(Smarty_Resource $handler, Smarty $smarty, $resource, $type, $name)
    {
        $this->handler = $handler; // Note: prone to circular references
        $this->smarty = $smarty;
        $this->resource = $resource;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * initialize Source Object for given resource
     * Either [$_template] or [$smarty, $template_resource] must be specified
     *
     * @param  Smarty_Internal_Template $_template         template object
     * @param  Smarty                   $smarty            smarty object
     * @param  string                   $template_resource resource identifier
     *
     * @return Smarty_Template_Source Source Object
     * @throws SmartyException
     */
    public static function load(Smarty_Internal_Template $_template = null, Smarty $smarty = null,
                                $template_resource = null)
    {
        if ($_template) {
            $smarty = $_template->smarty;
            $template_resource = $_template->template_resource;
        }
        if (empty($template_resource)) {
            throw new SmartyException('Missing template name');
        }
        // parse resource_name, load resource handler, identify unique resource name
        if (preg_match('/^([A-Za-z0-9_\-]{2,})[:]([\s\S]*)$/', $template_resource, $match)) {
            $type = $match[1];
            $name = $match[2];
        } else {
            // no resource given, use default
            // or single character before the colon is not a resource type, but part of the filepath
            $type = $smarty->default_resource_type;
            $name = $template_resource;
        }

        $handler = isset($smarty->_cache['resource_handlers'][$type]) ?
            $smarty->_cache['resource_handlers'][$type] :
            Smarty_Resource::load($smarty, $type);
        // if resource is not recompiling and resource name is not dotted we can check the source cache
        if (($smarty->resource_cache_mode & Smarty::RESOURCE_CACHE_ON) && !$handler->recompiled &&
            !(isset($name[1]) && $name[0] == '.' && ($name[1] == '.' || $name[1] == '/'))
        ) {
            $unique_resource = $handler->buildUniqueResourceName($smarty, $name);
            if (isset($smarty->_cache['source_objects'][$unique_resource])) {
                return $smarty->_cache['source_objects'][$unique_resource];
            }
        } else {
            $unique_resource = null;
        }
        // create new source  object
        $source = new Smarty_Template_Source($handler, $smarty, $template_resource, $type, $name);
        $handler->populate($source, $_template);
        if (!$source->exists && isset($_template->smarty->default_template_handler_func)) {
            Smarty_Internal_Method_RegisterDefaultTemplateHandler::_getDefaultTemplate($source);
        }
        // on recompiling resources we are done
        if (($smarty->resource_cache_mode & Smarty::RESOURCE_CACHE_ON) && !$handler->recompiled) {
            // may by we have already $unique_resource
            $is_relative = false;
            if (!isset($unique_resource)) {
                $is_relative = isset($name[1]) && $name[0] == '.' && ($name[1] == '.' || $name[1] == '/') &&
                    ($type == 'file' ||
                        (isset($_template->parent->source) && $_template->parent->source->type == 'extends'));
                $unique_resource =
                    $handler->buildUniqueResourceName($smarty, $is_relative ? $source->filepath . $name : $name);
            }
            $source->unique_resource = $unique_resource;
            // save in runtime cache if not relative
            if (!$is_relative) {
                $smarty->_cache['source_objects'][$unique_resource] = $source;
            }
        }
        return $source;
    }

    /**
     * render the uncompiled source
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @return string
     * @throws \Exception
     */
    public function renderUncompiled(Smarty_Internal_Template $_template)
    {
        $this->handler->renderUncompiled($_template->source, $_template);
    }

    /**
     * Render uncompiled source
     *
     * @param \Smarty_Internal_Template $_template
     */
    public function render(Smarty_Internal_Template $_template)
    {
        if ($_template->source->handler->uncompiled) {
            if ($_template->smarty->debugging) {
                $_template->smarty->_debug->start_render($_template);
            }
            $this->handler->renderUncompiled($_template->source, $_template);
            if (isset($_template->parent) && $_template->parent->_objType == 2 && !empty($_template->tpl_function)) {
                $_template->parent->tpl_function =
                    array_merge($_template->parent->tpl_function, $_template->tpl_function);
            }
            if ($_template->smarty->debugging) {
                $_template->smarty->_debug->end_render($_template);
            }
        }
    }

    /**
     * Get source time stamp
     *
     * @return int
     */
    public function getTimeStamp()
    {
        if (!isset($this->timestamp)) {
            $this->handler->populateTimestamp($this);
        }
        return $this->timestamp;
    }

    /**
     * Get source content
     *
     * @return string
     */
    public function getContent()
    {
        return isset($this->content) ? $this->content : $this->handler->getContent($this);
    }
}
