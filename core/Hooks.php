<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/hooks.html
 */
class CI_Hooks
{

    /**
     * Determines whether hooks are enabled
     *
     * @var	bool
     */
    public $enabled = false;

    /**
     * List of all hooks set in config/hooks.php
     *
     * @var	array
     */
    public $hooks =	array();

    /**
     * Array with class objects to use hooks methods
     *
     * @var array
     */
    protected $_objects = array();

    /**
     * In progress flag
     *
     * Determines whether hook is in progress, used to prevent infinte loops
     *
     * @var	bool
     */
    protected $_in_progress = false;

    /**
     * Class constructor
     *
     * @param	CI_Config	$config
     * @return	void
     */
    public function __construct(CI_Config $config)
    {
        log_message('info', 'Hooks Class Initialized');

        // If hooks are not enabled in the config file
        // there is nothing else to do
        if ($config->item('enable_hooks') === false) {
            return;
        }

        // Grab the "hooks" definition file.
        if (file_exists(APPPATH.'config/hooks.php')) {
            include(APPPATH.'config/hooks.php');
        }

        if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/hooks.php')) {
            include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
        }

        // If there are no hooks, we're done.
        if (! isset($hook) or ! is_array($hook)) {
            return;
        }

        $this->hooks =& $hook;
        $this->enabled = true;
    }

    // --------------------------------------------------------------------

    /**
     * Call Hook
     *
     * Calls a particular hook. Called by CodeIgniter.php.
     *
     * @uses	CI_Hooks::_run_hook()
     *
     * @param	string	$which	Hook name
     * @return	bool	TRUE on success or FALSE on failure
     */
    public function call_hook($which = '')
    {
        if (! $this->enabled or ! isset($this->hooks[$which])) {
            return false;
        }

        if (is_array($this->hooks[$which]) && ! isset($this->hooks[$which]['function'])) {
            foreach ($this->hooks[$which] as $val) {
                $this->_run_hook($val);
            }
        } else {
            $this->_run_hook($this->hooks[$which]);
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Run Hook
     *
     * Runs a particular hook
     *
     * @param	array	$data	Hook details
     * @return	bool	TRUE on success or FALSE on failure
     */
    protected function _run_hook($data)
    {
        // Closures/lambda functions and array($object, 'method') callables
        if (is_callable($data)) {
            is_array($data)
                ? $data[0]->{$data[1]}()
                : $data();

            return true;
        } elseif (! is_array($data)) {
            return false;
        }

        // -----------------------------------
        // Safety - Prevents run-away loops
        // -----------------------------------

        // If the script being called happens to have the same
        // hook call within it a loop can happen
        if ($this->_in_progress === true) {
            return;
        }

        // -----------------------------------
        // Set file path
        // -----------------------------------

        if (! isset($data['filepath'], $data['filename'])) {
            return false;
        }

        $filepath = APPPATH.$data['filepath'].'/'.$data['filename'];

        if (! file_exists($filepath)) {
            return false;
        }

        // Determine and class and/or function names
        $class		= empty($data['class']) ? false : $data['class'];
        $function	= empty($data['function']) ? false : $data['function'];
        $params		= isset($data['params']) ? $data['params'] : '';

        if (empty($function)) {
            return false;
        }

        // Set the _in_progress flag
        $this->_in_progress = true;

        // Call the requested class and/or function
        if ($class !== false) {
            // The object is stored?
            if (isset($this->_objects[$class])) {
                if (method_exists($this->_objects[$class], $function)) {
                    $this->_objects[$class]->$function($params);
                } else {
                    return $this->_in_progress = false;
                }
            } else {
                class_exists($class, false) or require_once($filepath);

                if (! class_exists($class, false) or ! method_exists($class, $function)) {
                    return $this->_in_progress = false;
                }

                // Store the object and execute the method
                $this->_objects[$class] = new $class();
                $this->_objects[$class]->$function($params);
            }
        } else {
            function_exists($function) or require_once($filepath);

            if (! function_exists($function)) {
                return $this->_in_progress = false;
            }

            $function($params);
        }

        $this->_in_progress = false;
        return true;
    }
}
