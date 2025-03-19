<?php
/**
 * Smarty Internal Plugin Debug
 * Class to collect data for the Smarty Debugging Console
 *
 * @package    Smarty
 * @subpackage Debug
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Debug Class
 *
 * @package    Smarty
 * @subpackage Debug
 */
class Smarty_Internal_Debug extends Smarty_Internal_Data
{
    /**
     * template data
     *
     * @var array
     */
    public $template_data = array();

    /**
     * List of uid's which shall be ignored
     *
     * @var array
     */
    public $ignore_uid = array();

    /**
     * Index of display() and fetch() calls
     *
     * @var int
     */
    public $index = 0;

    /**
     * Counter for window offset
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Start logging template
     *
     * @param \Smarty_Internal_Template $template template
     * @param null                      $mode     true: display   false: fetch  null: subtemplate
     */
    public function start_template(Smarty_Internal_Template $template, $mode = null)
    {
        if (isset($mode)) {
            $this->index ++;
            $this->offset ++;
            $this->template_data[$this->index] = null;
        }
        $key = $this->get_key($template);
        $this->template_data[$this->index][$key]['start_template_time'] = microtime(true);
    }

    /**
     * End logging of cache time
     *
     * @param \Smarty_Internal_Template $template cached template
     */
    public function end_template(Smarty_Internal_Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[$this->index][$key]['total_time'] +=
            microtime(true) - $this->template_data[$this->index][$key]['start_template_time'];
        //$this->template_data[$this->index][$key]['properties'] = $template->properties;
    }

    /**
     * Start logging of compile time
     *
     * @param \Smarty_Internal_Template $template
     */
    public function start_compile(Smarty_Internal_Template $template)
    {
        static $_is_stringy = array('string' => true, 'eval' => true);
        if (!empty($template->compiler->trace_uid)) {
            $key = $template->compiler->trace_uid;
            if (!isset($this->template_data[$this->index][$key])) {
                if (isset($_is_stringy[$template->source->type])) {
                    $this->template_data[$this->index][$key]['name'] =
                        '\'' . substr($template->source->name, 0, 25) . '...\'';
                } else {
                    $this->template_data[$this->index][$key]['name'] = $template->source->filepath;
                }
                $this->template_data[$this->index][$key]['compile_time'] = 0;
                $this->template_data[$this->index][$key]['render_time'] = 0;
                $this->template_data[$this->index][$key]['cache_time'] = 0;
            }
        } else {
            if (isset($this->ignore_uid[$template->source->uid])) {
                return;
            }
            $key = $this->get_key($template);
        }
        $this->template_data[$this->index][$key]['start_time'] = microtime(true);
    }

    /**
     * End logging of compile time
     *
     * @param \Smarty_Internal_Template $template
     */
    public function end_compile(Smarty_Internal_Template $template)
    {
        if (!empty($template->compiler->trace_uid)) {
            $key = $template->compiler->trace_uid;
        } else {
            if (isset($this->ignore_uid[$template->source->uid])) {
                return;
            }

            $key = $this->get_key($template);
        }
        $this->template_data[$this->index][$key]['compile_time'] +=
            microtime(true) - $this->template_data[$this->index][$key]['start_time'];
    }

    /**
     * Start logging of render time
     *
     * @param \Smarty_Internal_Template $template
     */
    public function start_render(Smarty_Internal_Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[$this->index][$key]['start_time'] = microtime(true);
    }

    /**
     * End logging of compile time
     *
     * @param \Smarty_Internal_Template $template
     */
    public function end_render(Smarty_Internal_Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[$this->index][$key]['render_time'] +=
            microtime(true) - $this->template_data[$this->index][$key]['start_time'];
    }

    /**
     * Start logging of cache time
     *
     * @param \Smarty_Internal_Template $template cached template
     */
    public function start_cache(Smarty_Internal_Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[$this->index][$key]['start_time'] = microtime(true);
    }

    /**
     * End logging of cache time
     *
     * @param \Smarty_Internal_Template $template cached template
     */
    public function end_cache(Smarty_Internal_Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[$this->index][$key]['cache_time'] +=
            microtime(true) - $this->template_data[$this->index][$key]['start_time'];
    }

    /**
     * Register template object
     *
     * @param \Smarty_Internal_Template $template cached template
     */
    public function register_template(Smarty_Internal_Template $template)
    {
    }

    /**
     * Register data object
     *
     * @param \Smarty_Data $data data object
     */
    public static function register_data(Smarty_Data $data)
    {
    }

    /**
     * Opens a window for the Smarty Debugging Console and display the data
     *
     * @param Smarty_Internal_Template|Smarty $obj object to debug
     * @param bool                            $full
     */
    public function display_debug($obj, $full = false)
    {
        if (!$full) {
            $this->offset ++;
            $savedIndex = $this->index;
            $this->index = 9999;
        }
        if ($obj->_objType == 1) {
            $smarty = $obj;
        } else {
            $smarty = $obj->smarty;
        }
        // create fresh instance of smarty for displaying the debug console
        // to avoid problems if the application did overload the Smarty class
        $debObj = new Smarty();
        // copy the working dirs from application
        $debObj->setCompileDir($smarty->getCompileDir());
        // init properties by hand as user may have edited the original Smarty class
        $debObj->setPluginsDir(is_dir(__DIR__ . '/../plugins') ? __DIR__ . '/../plugins' : $smarty->getPluginsDir());
        $debObj->force_compile = false;
        $debObj->compile_check = true;
        $debObj->left_delimiter = '{';
        $debObj->right_delimiter = '}';
        $debObj->security_policy = null;
        $debObj->debugging = false;
        $debObj->debugging_ctrl = 'NONE';
        $debObj->error_reporting = E_ALL & ~E_NOTICE;
        $debObj->debug_tpl = isset($smarty->debug_tpl) ? $smarty->debug_tpl : 'file:' . __DIR__ . '/../debug.tpl';
        $debObj->registered_plugins = array();
        $debObj->registered_resources = array();
        $debObj->registered_filters = array();
        $debObj->autoload_filters = array();
        $debObj->default_modifiers = array();
        $debObj->escape_html = true;
        $debObj->caching = false;
        $debObj->compile_id = null;
        $debObj->cache_id = null;
        // prepare information of assigned variables
        $ptr = $this->get_debug_vars($obj);
        $_assigned_vars = $ptr->tpl_vars;
        ksort($_assigned_vars);
        $_config_vars = $ptr->config_vars;
        ksort($_config_vars);
        $debugging = $smarty->debugging;

        $_template = new Smarty_Internal_Template($debObj->debug_tpl, $debObj);
        if ($obj->_objType == 2) {
            $_template->assign('template_name', $obj->source->type . ':' . $obj->source->name);
        }
        if ($obj->_objType == 1 || $full) {
            $_template->assign('template_data', $this->template_data[$this->index]);
        } else {
            $_template->assign('template_data', null);
        }
        $_template->assign('assigned_vars', $_assigned_vars);
        $_template->assign('config_vars', $_config_vars);
        $_template->assign('execution_time', microtime(true) - $smarty->start_time);
        $_template->assign('display_mode', $debugging == 2 || !$full);
        $_template->assign('offset', $this->offset * 50);
        echo $_template->fetch();
        if (isset($full)) {
            $this->index --;
        }
        if (!$full) {
            $this->index = $savedIndex;
        }
    }

    /**
     * Recursively gets variables from all template/data scopes
     *
     * @param  Smarty_Internal_Template|Smarty_Data $obj object to debug
     *
     * @return StdClass
     */
    public function get_debug_vars($obj)
    {
        $config_vars = array();
        foreach ($obj->config_vars as $key => $var) {
            $config_vars[$key]['value'] = $var;
            if ($obj->_objType == 2) {
                $config_vars[$key]['scope'] = $obj->source->type . ':' . $obj->source->name;
            } elseif ($obj->_objType == 4) {
                $tpl_vars[$key]['scope'] = $obj->dataObjectName;
            } else {
                $config_vars[$key]['scope'] = 'Smarty object';
            }
        }
        $tpl_vars = array();
        foreach ($obj->tpl_vars as $key => $var) {
            foreach ($var as $varkey => $varvalue) {
                if ($varkey == 'value') {
                    $tpl_vars[$key][$varkey] = $varvalue;
                } else {
                    if ($varkey == 'nocache') {
                        if ($varvalue == true) {
                            $tpl_vars[$key][$varkey] = $varvalue;
                        }
                    } else {
                        if ($varkey != 'scope' || $varvalue !== 0) {
                            $tpl_vars[$key]['attributes'][$varkey] = $varvalue;
                        }
                    }
                }
            }
            if ($obj->_objType == 2) {
                $tpl_vars[$key]['scope'] = $obj->source->type . ':' . $obj->source->name;
            } elseif ($obj->_objType == 4) {
                $tpl_vars[$key]['scope'] = $obj->dataObjectName;
            } else {
                $tpl_vars[$key]['scope'] = 'Smarty object';
            }
        }

        if (isset($obj->parent)) {
            $parent = $this->get_debug_vars($obj->parent);
            foreach ($parent->tpl_vars as $name => $pvar) {
                if (isset($tpl_vars[$name]) && $tpl_vars[$name]['value'] === $pvar['value']) {
                    $tpl_vars[$name]['scope'] = $pvar['scope'];
                }
            }
            $tpl_vars = array_merge($parent->tpl_vars, $tpl_vars);

            foreach ($parent->config_vars as $name => $pvar) {
                if (isset($config_vars[$name]) && $config_vars[$name]['value'] === $pvar['value']) {
                    $config_vars[$name]['scope'] = $pvar['scope'];
                }
            }
            $config_vars = array_merge($parent->config_vars, $config_vars);
        } else {
            foreach (Smarty::$global_tpl_vars as $key => $var) {
                if (!array_key_exists($key, $tpl_vars)) {
                    foreach ($var as $varkey => $varvalue) {
                        if ($varkey == 'value') {
                            $tpl_vars[$key][$varkey] = $varvalue;
                        } else {
                            if ($varkey == 'nocache') {
                                if ($varvalue == true) {
                                    $tpl_vars[$key][$varkey] = $varvalue;
                                }
                            } else {
                                if ($varkey != 'scope' || $varvalue !== 0) {
                                    $tpl_vars[$key]['attributes'][$varkey] = $varvalue;
                                }
                            }
                        }
                    }
                    $tpl_vars[$key]['scope'] = 'Global';
                }
            }
        }

        return (object) array('tpl_vars' => $tpl_vars, 'config_vars' => $config_vars);
    }

    /**
     * Return key into $template_data for template
     *
     * @param \Smarty_Internal_Template $template template object
     *
     * @return string key into $template_data
     */
    private function get_key(Smarty_Internal_Template $template)
    {
        static $_is_stringy = array('string' => true, 'eval' => true);
        // calculate Uid if not already done
        if ($template->source->uid == '') {
            $template->source->filepath;
        }
        $key = $template->source->uid;
        if (isset($this->template_data[$this->index][$key])) {
            return $key;
        } else {
            if (isset($_is_stringy[$template->source->type])) {
                $this->template_data[$this->index][$key]['name'] =
                    '\'' . substr($template->source->name, 0, 25) . '...\'';
            } else {
                $this->template_data[$this->index][$key]['name'] = $template->source->filepath;
            }
            $this->template_data[$this->index][$key]['compile_time'] = 0;
            $this->template_data[$this->index][$key]['render_time'] = 0;
            $this->template_data[$this->index][$key]['cache_time'] = 0;
            $this->template_data[$this->index][$key]['total_time'] = 0;

            return $key;
        }
    }

    /**
     * Ignore template
     *
     * @param \Smarty_Internal_Template $template
     */
    public function ignore(Smarty_Internal_Template $template)
    {
        // calculate Uid if not already done
        if ($template->source->uid == '') {
            $template->source->filepath;
        }
        $this->ignore_uid[$template->source->uid] = true;
    }

    /**
     * handle 'URL' debugging mode
     *
     * @param Smarty $smarty
     */
    public function debugUrl(Smarty $smarty)
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            $_query_string = $_SERVER['QUERY_STRING'];
        } else {
            $_query_string = '';
        }
        if (false !== strpos($_query_string, $smarty->smarty_debug_id)) {
            if (false !== strpos($_query_string, $smarty->smarty_debug_id . '=on')) {
                // enable debugging for this browser session
                setcookie('SMARTY_DEBUG', true);
                $smarty->debugging = true;
            } elseif (false !== strpos($_query_string, $smarty->smarty_debug_id . '=off')) {
                // disable debugging for this browser session
                setcookie('SMARTY_DEBUG', false);
                $smarty->debugging = false;
            } else {
                // enable debugging for this page
                $smarty->debugging = true;
            }
        } else {
            if (isset($_COOKIE['SMARTY_DEBUG'])) {
                $smarty->debugging = true;
            }
        }
    }
}
