<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

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
class CI_Hooks {

	/**
	 * Determines whether hooks are enabled
	 *
	 * @var	bool
	 */
	// 是否开启hooks
	public $enabled = FALSE;

	/**
	 * List of all hooks set in config/hooks.php
	 *
	 * @var	array
	 */
	// hooks数组
	public $hooks =	array();

	/**
	 * Array with class objects to use hooks methods
	 *
	 * @var array
	 */
	//hooks调用到的对象
	protected $_objects = array();

	/**
	 * In progress flag
	 *
	 * Determines whether hook is in progress, used to prevent infinte loops
	 *
	 * @var	bool
	 */
	//hooks是否在进程中，反正死循环
	protected $_in_progress = FALSE;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		//加载配置文件
		$CFG =& load_class('Config', 'core');
		log_message('info', 'Hooks Class Initialized');

		// If hooks are not enabled in the config file
		// there is nothing else to do
		//如果配置文件为开启hooks，直接返回
		if ($CFG->item('enable_hooks') === FALSE)
		{
			return;
		}

		//加载hooks的配置文件
		// Grab the "hooks" definition file.
		if (file_exists(APPPATH.'config/hooks.php'))
		{
			include(APPPATH.'config/hooks.php');
		}

		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/hooks.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
		}
		
		//检查配置文件中定义的hook数组是否正确
		// If there are no hooks, we're done.
		if ( ! isset($hook) OR ! is_array($hook))
		{
			return;
		}
		
		//将配置hook数组赋值给对象hooks属性
		$this->hooks =& $hook;
		//更新enabled，允许使用hooks
		$this->enabled = TRUE;
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
		//判断是否开启hooks或者改hooks是否存在
		if ( ! $this->enabled OR ! isset($this->hooks[$which]))
		{
			return FALSE;
		}
	
		//如果配置是数组，则判断是否设置该hooks函数 $hook['pre_system'] = array('class'=>'','function'=>''...)
		if (is_array($this->hooks[$which]) && ! isset($this->hooks[$which]['function']))
		{
			//遍历改hooks下面每个函数
			foreach ($this->hooks[$which] as $val)
			{
				//调用
				$this->_run_hook($val);
			}
		}
		else
		{
			//如果是直接配置函数 $hook['pre_system'] = hook_function
			$this->_run_hook($this->hooks[$which]);
		}

		return TRUE;
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
		//是否可调用,包括lamdda 函数表达式和 array($object,'method')
		if (is_callable($data))
		{
			//如果是是数组，$object->method，否则，直接通过函数名调用函数
			is_array($data)
				? $data[0]->{$data[1]}()
				: $data();

			return TRUE;
		}
		elseif ( ! is_array($data))	//数据不是数组，返回
		{
			return FALSE;
		}

		// -----------------------------------
		// Safety - Prevents run-away loops
		// -----------------------------------
		//防止死循环

		// If the script being called happens to have the same
		// hook call within it a loop can happen
		if ($this->_in_progress === TRUE)
		{
			return;
		}

		// -----------------------------------
		// Set file path
		// -----------------------------------
		//array（'class','method','filename','filepath'）这种形式hook配置参数检查
		if ( ! isset($data['filepath'], $data['filename']))
		{
			return FALSE;
		}
		
		//拼接文件路径
		$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];
		
		//判断文件是否存在
		if ( ! file_exists($filepath))
		{
			return FALSE;
		}

		// Determine and class and/or function names
		$class		= empty($data['class']) ? FALSE : $data['class'];	//类
		$function	= empty($data['function']) ? FALSE : $data['function'];	//方法
		$params		= isset($data['params']) ? $data['params'] : '';	//参数
		
		//方法是否为空
		if (empty($function))
		{
			return FALSE;
		}

		// Set the _in_progress flag
		//设置 进程中 。。。
		$this->_in_progress = TRUE;

		// Call the requested class and/or function
		//类调用
		if ($class !== FALSE)
		{
			// The object is stored?
			//该类是否已经初始化
			if (isset($this->_objects[$class]))
			{
				//是否存在该方法
				if (method_exists($this->_objects[$class], $function))
				{
					//调用方法
					$this->_objects[$class]->$function($params);
				}
				else
				{
					return $this->_in_progress = FALSE;
				}
			}
			else
			{
				//加载该类
				class_exists($class, FALSE) OR require_once($filepath);
				//检查类和方法是否有效
				if ( ! class_exists($class, FALSE) OR ! method_exists($class, $function))
				{
					return $this->_in_progress = FALSE;
				}

				// Store the object and execute the method
				//缓存类和调用方法
				$this->_objects[$class] = new $class();
				$this->_objects[$class]->$function($params);
			}
		}
		else //方法调用
		{
			//载入函数文件
			function_exists($function) OR require_once($filepath);
			//检查函数是否存在
			if ( ! function_exists($function))
			{
				return $this->_in_progress = FALSE;
			}
			//调用函数
			$function($params);
		}
		//结束
		$this->_in_progress = FALSE;
		return TRUE;
	}

}
