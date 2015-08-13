<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Javascript Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Javascript
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/javascript.html
 * @deprecated	3.0.0	This was never a good idea in the first place.
 */
class CI_Javascript {

	/**
	 * JavaScript location
	 *
	 * @var	string
	 */
	protected $_javascript_location = 'js';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params = array())
	{
		$defaults = array('js_library_driver' => 'jquery', 'autoload' => TRUE);

		foreach ($defaults as $key => $val)
		{
			if (isset($params[$key]) && $params[$key] !== '')
			{
				$defaults[$key] = $params[$key];
			}
		}

		extract($defaults);

		$this->CI =& get_instance();

		// load the requested js library
		$this->CI->load->library('Javascript/'.$js_library_driver, array('autoload' => $autoload));
		// make js to refer to current library
		$this->js =& $this->CI->$js_library_driver;

		log_message('info', 'Javascript Class Initialized and loaded. Driver used: '.$js_library_driver);
	}

	// --------------------------------------------------------------------
	// Event Code
	// --------------------------------------------------------------------

	/**
	 * Blur
	 *
	 * Outputs a javascript library blur event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function blur($element = 'this', $js = '')
	{
		return $this->js->_blur($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Change
	 *
	 * Outputs a javascript library change event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function change($element = 'this', $js = '')
	{
		return $this->js->_change($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Click
	 *
	 * Outputs a javascript library click event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @param	bool	whether or not to return false
	 * @return	string
	 */
	public function click($element = 'this', $js = '', $ret_false = TRUE)
	{
		return $this->js->_click($element, $js, $ret_false);
	}

	// --------------------------------------------------------------------

	/**
	 * Double Click
	 *
	 * Outputs a javascript library dblclick event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function dblclick($element = 'this', $js = '')
	{
		return $this->js->_dblclick($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Error
	 *
	 * Outputs a javascript library error event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function error($element = 'this', $js = '')
	{
		return $this->js->_error($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Focus
	 *
	 * Outputs a javascript library focus event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function focus($element = 'this', $js = '')
	{
		return $this->js->_focus($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Hover
	 *
	 * Outputs a javascript library hover event
	 *
	 * @param	string	- element
	 * @param	string	- Javascript code for mouse over
	 * @param	string	- Javascript code for mouse out
	 * @return	string
	 */
	public function hover($element = 'this', $over = '', $out = '')
	{
		return $this->js->_hover($element, $over, $out);
	}

	// --------------------------------------------------------------------

	/**
	 * Keydown
	 *
	 * Outputs a javascript library keydown event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function keydown($element = 'this', $js = '')
	{
		return $this->js->_keydown($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Keyup
	 *
	 * Outputs a javascript library keydown event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function keyup($element = 'this', $js = '')
	{
		return $this->js->_keyup($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Load
	 *
	 * Outputs a javascript library load event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function load($element = 'this', $js = '')
	{
		return $this->js->_load($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mousedown
	 *
	 * Outputs a javascript library mousedown event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function mousedown($element = 'this', $js = '')
	{
		return $this->js->_mousedown($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mouse Out
	 *
	 * Outputs a javascript library mouseout event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function mouseout($element = 'this', $js = '')
	{
		return $this->js->_mouseout($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mouse Over
	 *
	 * Outputs a javascript library mouseover event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function mouseover($element = 'this', $js = '')
	{
		return $this->js->_mouseover($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mouseup
	 *
	 * Outputs a javascript library mouseup event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function mouseup($element = 'this', $js = '')
	{
		return $this->js->_mouseup($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Output
	 *
	 * Outputs the called javascript to the screen
	 *
	 * @param	string	The code to output
	 * @return	string
	 */
	public function output($js)
	{
		return $this->js->_output($js);
	}

	// --------------------------------------------------------------------

	/**
	 * Ready
	 *
	 * Outputs a javascript library mouseup event
	 *
	 * @param	string	$js	Code to execute
	 * @return	string
	 */
	public function ready($js)
	{
		return $this->js->_document_ready($js);
	}

	// --------------------------------------------------------------------

	/**
	 * Resize
	 *
	 * Outputs a javascript library resize event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function resize($element = 'this', $js = '')
	{
		return $this->js->_resize($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Scroll
	 *
	 * Outputs a javascript library scroll event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function scroll($element = 'this', $js = '')
	{
		return $this->js->_scroll($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Unload
	 *
	 * Outputs a javascript library unload event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	public function unload($element = 'this', $js = '')
	{
		return $this->js->_unload($element, $js);
	}

	// --------------------------------------------------------------------
	// Effects
	// --------------------------------------------------------------------

	/**
	 * Add Class
	 *
	 * Outputs a javascript library addClass event
	 *
	 * @param	string	- element
	 * @param	string	- Class to add
	 * @return	string
	 */
	public function addClass($element = 'this', $class = '')
	{
		return $this->js->_addClass($element, $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Animate
	 *
	 * Outputs a javascript library animate event
	 *
	 * @param	string	$element = 'this'
	 * @param	array	$params = array()
	 * @param	mixed	$speed			'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	$extra
	 * @return	string
	 */
	public function animate($element = 'this', $params = array(), $speed = '', $extra = '')
	{
		return $this->js->_animate($element, $params, $speed, $extra);
	}

	// --------------------------------------------------------------------

	/**
	 * Fade In
	 *
	 * Outputs a javascript library hide event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function fadeIn($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_fadeIn($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Fade Out
	 *
	 * Outputs a javascript library hide event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function fadeOut($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_fadeOut($element, $speed, $callback);
	}
	// --------------------------------------------------------------------

	/**
	 * Slide Up
	 *
	 * Outputs a javascript library slideUp event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function slideUp($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_slideUp($element, $speed, $callback);

	}

	// --------------------------------------------------------------------

	/**
	 * Remove Class
	 *
	 * Outputs a javascript library removeClass event
	 *
	 * @param	string	- element
	 * @param	string	- Class to add
	 * @return	string
	 */
	public function removeClass($element = 'this', $class = '')
	{
		return $this->js->_removeClass($element, $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Down
	 *
	 * Outputs a javascript library slideDown event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function slideDown($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_slideDown($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Toggle
	 *
	 * Outputs a javascript library slideToggle event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function slideToggle($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_slideToggle($element, $speed, $callback);

	}

	// --------------------------------------------------------------------

	/**
	 * Hide
	 *
	 * Outputs a javascript library hide action
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function hide($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_hide($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Toggle
	 *
	 * Outputs a javascript library toggle event
	 *
	 * @param	string	- element
	 * @return	string
	 */
	public function toggle($element = 'this')
	{
		return $this->js->_toggle($element);

	}

	// --------------------------------------------------------------------

	/**
	 * Toggle Class
	 *
	 * Outputs a javascript library toggle class event
	 *
	 * @param	string	$element = 'this'
	 * @param	string	$class = ''
	 * @return	string
	 */
	public function toggleClass($element = 'this', $class = '')
	{
		return $this->js->_toggleClass($element, $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Show
	 *
	 * Outputs a javascript library show event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function show($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_show($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile
	 *
	 * gather together all script needing to be output
	 *
	 * @param	string	$view_var
	 * @param	bool	$script_tags
	 * @return	string
	 */
	public function compile($view_var = 'script_foot', $script_tags = TRUE)
	{
		$this->js->_compile($view_var, $script_tags);
	}

	// --------------------------------------------------------------------

	/**
	 * Clear Compile
	 *
	 * Clears any previous javascript collected for output
	 *
	 * @return	void
	 */
	public function clear_compile()
	{
		$this->js->_clear_compile();
	}

	// --------------------------------------------------------------------

	/**
	 * External
	 *
	 * Outputs a <script> tag with the source as an external js file
	 *
	 * @param	string	$external_file
	 * @param	bool	$relative
	 * @return	string
	 */
	public function external($external_file = '', $relative = FALSE)
	{
		if ($external_file !== '')
		{
			$this->_javascript_location = $external_file;
		}
		elseif ($this->CI->config->item('javascript_location') !== '')
		{
			$this->_javascript_location = $this->CI->config->item('javascript_location');
		}

		if ($relative === TRUE OR strpos($external_file, 'http://') === 0 OR strpos($external_file, 'https://') === 0)
		{
			$str = $this->_open_script($external_file);
		}
		elseif (strpos($this->_javascript_location, 'http://') !== FALSE)
		{
			$str = $this->_open_script($this->_javascript_location.$external_file);
		}
		else
		{
			$str = $this->_open_script($this->CI->config->slash_item('base_url').$this->_javascript_location.$external_file);
		}

		return $str.$this->_close_script();
	}

	// --------------------------------------------------------------------

	/**
	 * Inline
	 *
	 * Outputs a <script> tag
	 *
	 * @param	string	The element to attach the event to
	 * @param	bool	If a CDATA section should be added
	 * @return	string
	 */
	public function inline($script, $cdata = TRUE)
	{
		return $this->_open_script()
			. ($cdata ? "\n// <![CDATA[\n".$script."\n// ]]>\n" : "\n".$script."\n")
			. $this->_close_script();
	}

	// --------------------------------------------------------------------

	/**
	 * Open Script
	 *
	 * Outputs an opening <script>
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _open_script($src = '')
	{
		return '<script type="text/javascript" charset="'.strtolower($this->CI->config->item('charset')).'"'
			.($src === '' ? '>' : ' src="'.$src.'">');
	}

	// --------------------------------------------------------------------

	/**
	 * Close Script
	 *
	 * Outputs an closing </script>
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _close_script($extra = "\n")
	{
		return '</script>'.$extra;
	}

	// --------------------------------------------------------------------
	// AJAX-Y STUFF - still a testbed
	// --------------------------------------------------------------------

	/**
	 * Update
	 *
	 * Outputs a javascript library slideDown event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	public function update($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_updater($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate JSON
	 *
	 * Can be passed a database result or associative array and returns a JSON formatted string
	 *
	 * @param	mixed	result set or array
	 * @param	bool	match array types (defaults to objects)
	 * @return	string	a json formatted string
	 */
	public function generate_json($result = NULL, $match_array_type = FALSE)
	{
		// JSON data can optionally be passed to this function
		// either as a database result object or an array, or a user supplied array
		if ($result !== NULL)
		{
			if (is_object($result))
			{
				$json_result = is_callable(array($result, 'result_array')) ? $result->result_array() : (array) $result;
			}
			elseif (is_array($result))
			{
				$json_result = $result;
			}
			else
			{
				return $this->_prep_args($result);
			}
		}
		else
		{
			return 'null';
		}

		$json = array();
		$_is_assoc = TRUE;

		if ( ! is_array($json_result) && empty($json_result))
		{
			show_error('Generate JSON Failed - Illegal key, value pair.');
		}
		elseif ($match_array_type)
		{
			$_is_assoc = $this->_is_associative_array($json_result);
		}

		foreach ($json_result as $k => $v)
		{
			if ($_is_assoc)
			{
				$json[] = $this->_prep_args($k, TRUE).':'.$this->generate_json($v, $match_array_type);
			}
			else
			{
				$json[] = $this->generate_json($v, $match_array_type);
			}
		}

		$json = implode(',', $json);

		return $_is_assoc ? '{'.$json.'}' : '['.$json.']';

	}

	// --------------------------------------------------------------------

	/**
	 * Is associative array
	 *
	 * Checks for an associative array
	 *
	 * @param	array
	 * @return	bool
	 */
	protected function _is_associative_array($arr)
	{
		foreach (array_keys($arr) as $key => $val)
		{
			if ($key !== $val)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Args
	 *
	 * Ensures a standard json value and escapes values
	 *
	 * @param	mixed	$result
	 * @param	bool	$is_key = FALSE
	 * @return	string
	 */
	protected function _prep_args($result, $is_key = FALSE)
	{
		if ($result === NULL)
		{
			return 'null';
		}
		elseif (is_bool($result))
		{
			return ($result === TRUE) ? 'true' : 'false';
		}
		elseif (is_string($result) OR $is_key)
		{
			return '"'.str_replace(array('\\', "\t", "\n", "\r", '"', '/'), array('\\\\', '\\t', '\\n', "\\r", '\"', '\/'), $result).'"';
		}
		elseif (is_scalar($result))
		{
			return $result;
		}
	}

}
