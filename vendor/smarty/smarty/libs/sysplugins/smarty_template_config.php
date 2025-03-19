<?php
/**
 * Smarty Config Source Plugin
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Uwe Tews
 */

/**
 * Smarty Connfig Resource Data Object
 * Meta Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Uwe Tews
 *
 */
class Smarty_Template_Config extends Smarty_Template_Source
{
    /**
     * array of section names, single section or null
     *
     * @var null|string|array
     */
    public $config_sections = null;

    /**
     * scope into which the config variables shall be loaded
     *
     * @var string
     */
    public $scope = 'local';

    /**
     * Flag that source is a config file
     *
     * @var bool
     */
    public $isConfig = true;

    /**
     * create Source Object container
     *
     * @param Smarty_Resource $handler  Resource Handler this source object communicates with
     * @param Smarty          $smarty   Smarty instance this source object belongs to
     * @param string          $resource full template_resource
     * @param string          $type     type of resource
     * @param string          $name     resource name
     */
    public function __construct(Smarty_Resource $handler, Smarty $smarty, $resource, $type, $name)
    {
        // must clone handler as we change class names
        $this->handler = clone $handler; // Note: prone to circular references
        $this->handler->compiler_class = 'Smarty_Internal_Config_File_Compiler';
        $this->handler->template_lexer_class = 'Smarty_Internal_Configfilelexer';
        $this->handler->template_parser_class = 'Smarty_Internal_Configfileparser';
        $this->resource = $resource;
        $this->type = $type;
        $this->name = $name;
        $this->smarty = $smarty;
    }

    /**
     * initialize Source Object for given resource
     * Either [$_template] or [$smarty, $template_resource] must be specified
     *
     * @param  Smarty_Internal_Template $_template         template object
     * @param  Smarty                   $smarty            smarty object
     * @param  string                   $template_resource resource identifier
     *
     * @return Smarty_Template_Config Source Object
     * @throws SmartyException
     */
    public static function load(Smarty_Internal_Template $_template = null, Smarty $smarty = null, $template_resource = null)
    {
        static $_incompatible_resources = array('extends' => true, 'php' => true);
        $template_resource = $_template->template_resource;
        if (empty($template_resource)) {
            throw new SmartyException('Missing config name');
        }
        // parse resource_name, load resource handler
        list($name, $type) = Smarty_Resource::parseResourceName($template_resource, $_template->smarty->default_config_type);
        // make sure configs are not loaded via anything smarty can't handle
        if (isset($_incompatible_resources[$type])) {
            throw new SmartyException ("Unable to use resource '{$type}' for config");
        }
        $resource = Smarty_Resource::load($_template->smarty, $type);
        $source = new Smarty_Template_Config($resource, $_template->smarty, $template_resource, $type, $name);
        $resource->populate($source, $_template);
        if (!$source->exists && isset($_template->smarty->default_config_handler_func)) {
            Smarty_Internal_Method_RegisterDefaultTemplateHandler::_getDefaultTemplate($source);
        }
        $source->unique_resource = $resource->buildUniqueResourceName($_template->smarty, $name, true);
        return $source;
    }
}
