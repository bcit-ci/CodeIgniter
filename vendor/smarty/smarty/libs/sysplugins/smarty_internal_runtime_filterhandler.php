<?php
/**
 * Smarty Internal Plugin Filter Handler
 * Smarty filter handler class
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */

/**
 * Class for filter processing
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Runtime_FilterHandler
{
    /**
     * Run filters over content
     * The filters will be lazy loaded if required
     * class name format: Smarty_FilterType_FilterName
     * plugin filename format: filtertype.filtername.php
     * Smarty2 filter plugins could be used
     *
     * @param  string                   $type     the type of filter ('pre','post','output') which shall run
     * @param  string                   $content  the content which shall be processed by the filters
     * @param  Smarty_Internal_Template $template template object
     *
     * @throws SmartyException
     * @return string                   the filtered content
     */
    public function runFilter($type, $content, Smarty_Internal_Template $template)
    {
        // loop over autoload filters of specified type
        if (!empty($template->smarty->autoload_filters[$type])) {
            foreach ((array) $template->smarty->autoload_filters[$type] as $name) {
                $plugin_name = "Smarty_{$type}filter_{$name}";
                if (function_exists($plugin_name)) {
                    $callback = $plugin_name;
                } elseif (class_exists($plugin_name, false) && is_callable(array($plugin_name, 'execute'))) {
                    $callback = array($plugin_name, 'execute');
                } elseif ($template->smarty->loadPlugin($plugin_name, false)) {
                    if (function_exists($plugin_name)) {
                        // use loaded Smarty2 style plugin
                        $callback = $plugin_name;
                    } elseif (class_exists($plugin_name, false) && is_callable(array($plugin_name, 'execute'))) {
                        // loaded class of filter plugin
                        $callback = array($plugin_name, 'execute');
                    } else {
                        throw new SmartyException("Auto load {$type}-filter plugin method \"{$plugin_name}::execute\" not callable");
                    }
                } else {
                    // nothing found, throw exception
                    throw new SmartyException("Unable to auto load {$type}-filter plugin \"{$plugin_name}\"");
                }
                $content = call_user_func($callback, $content, $template);
            }
        }
        // loop over registered filters of specified type
        if (!empty($template->smarty->registered_filters[$type])) {
            foreach ($template->smarty->registered_filters[$type] as $key => $name) {
                $content = call_user_func($template->smarty->registered_filters[$type][$key], $content, $template);
            }
        }
        // return filtered output
        return $content;
    }
}
