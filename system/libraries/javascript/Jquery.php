<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * NOTICE OF LICENSE
 * 
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Jquery Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Loader
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/javascript.html
 */
 
class CI_Jquery extends CI_Javascript {

	var $_javascript_folder = 'js';
	var $jquery_code_for_load = array();
	var $jquery_code_for_compile = array();
	var $jquery_corner_active = FALSE;
	var $jquery_table_sorter_active = FALSE;
	var $jquery_table_sorter_pager_active = FALSE;
	var $jquery_ajax_img = '';

	public function __construct($params)
	{
		$this->CI =& get_instance();	
		extract($params);

		if ($autoload === TRUE)
		{
			$this->script();			
		}
		
		log_message('debug', "Jquery Class Initialized");
	}
	
	// --------------------------------------------------------------------	 
	// Event Code
	// --------------------------------------------------------------------	

	/**
	 * Blur
	 *
	 * Outputs a jQuery blur event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _blur($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'blur');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Change
	 *
	 * Outputs a jQuery change event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _change($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'change');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Click
	 *
	 * Outputs a jQuery click event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @param	boolean	whether or not to return false
	 * @return	string
	 */
	function _click($element = 'this', $js = '', $ret_false = TRUE)
	{
		if ( ! is_array($js))
		{
			$js = array($js);
		}

		if ($ret_false)
		{
			$js[] = "return false;";
		}

		return $this->_add_event($element, $js, 'click');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Double Click
	 *
	 * Outputs a jQuery dblclick event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _dblclick($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'dblclick');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Error
	 *
	 * Outputs a jQuery error event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _error($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'error');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Focus
	 *
	 * Outputs a jQuery focus event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _focus($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'focus');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Hover
	 *
	 * Outputs a jQuery hover event
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- Javascript code for mouse over
	 * @param	string	- Javascript code for mouse out
	 * @return	string
	 */
	function _hover($element = 'this', $over, $out)
	{
		$event = "\n\t$(" . $this->_prep_element($element) . ").hover(\n\t\tfunction()\n\t\t{\n\t\t\t{$over}\n\t\t}, \n\t\tfunction()\n\t\t{\n\t\t\t{$out}\n\t\t});\n";

		$this->jquery_code_for_compile[] = $event;

		return $event;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Keydown
	 *
	 * Outputs a jQuery keydown event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _keydown($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'keydown');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Keyup
	 *
	 * Outputs a jQuery keydown event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _keyup($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'keyup');
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Load
	 *
	 * Outputs a jQuery load event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _load($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'load');
	}	
	
	// --------------------------------------------------------------------
	
	/**
	 * Mousedown
	 *
	 * Outputs a jQuery mousedown event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _mousedown($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mousedown');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Mouse Out
	 *
	 * Outputs a jQuery mouseout event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _mouseout($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mouseout');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Mouse Over
	 *
	 * Outputs a jQuery mouseover event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _mouseover($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mouseover');
	}

	// --------------------------------------------------------------------

	/**
	 * Mouseup
	 *
	 * Outputs a jQuery mouseup event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _mouseup($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mouseup');
	}

	// --------------------------------------------------------------------

	/**
	 * Output
	 *
	 * Outputs script directly
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _output($array_js = '')
	{
		if ( ! is_array($array_js))
		{
			$array_js = array($array_js);
		}
		
		foreach ($array_js as $js)
		{
			$this->jquery_code_for_compile[] = "\t$js\n";
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Resize
	 *
	 * Outputs a jQuery resize event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _resize($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'resize');
	}

	// --------------------------------------------------------------------

	/**
	 * Scroll
	 *
	 * Outputs a jQuery scroll event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _scroll($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'scroll');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Unload
	 *
	 * Outputs a jQuery unload event
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @return	string
	 */
	function _unload($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'unload');
	}

	// --------------------------------------------------------------------	 
	// Effects
	// --------------------------------------------------------------------	
	
	/**
	 * Add Class
	 *
	 * Outputs a jQuery addClass event
	 *
	 * @access	private
	 * @param	string	- element
	 * @return	string
	 */
	function _addClass($element = 'this', $class='')
	{
		$element = $this->_prep_element($element);
		$str  = "$({$element}).addClass(\"$class\");";
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Animate
	 *
	 * Outputs a jQuery animate event
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _animate($element = 'this', $params = array(), $speed = '', $extra = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);
		
		$animations = "\t\t\t";
		
		foreach ($params as $param=>$value)
		{
			$animations .= $param.': \''.$value.'\', ';
		}

		$animations = substr($animations, 0, -2); // remove the last ", "

		if ($speed != '')
		{
			$speed = ', '.$speed;
		}
		
		if ($extra != '')
		{
			$extra = ', '.$extra;
		}
		
		$str  = "$({$element}).animate({\n$animations\n\t\t}".$speed.$extra.");";
		
		return $str;
	}

	// --------------------------------------------------------------------
		
	/**
	 * Fade In
	 *
	 * Outputs a jQuery hide event
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _fadeIn($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);	
		$speed = $this->_validate_speed($speed);
		
		if ($callback != '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}
		
		$str  = "$({$element}).fadeIn({$speed}{$callback});";
		
		return $str;
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Fade Out
	 *
	 * Outputs a jQuery hide event
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _fadeOut($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);
		
		if ($callback != '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}
		
		$str  = "$({$element}).fadeOut({$speed}{$callback});";
		
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Hide
	 *
	 * Outputs a jQuery hide action
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _hide($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);	
		$speed = $this->_validate_speed($speed);
		
		if ($callback != '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}
		
		$str  = "$({$element}).hide({$speed}{$callback});";

		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Remove Class
	 *
	 * Outputs a jQuery remove class event
	 *
	 * @access	private
	 * @param	string	- element
	 * @return	string
	 */
	function _removeClass($element = 'this', $class='')
	{
		$element = $this->_prep_element($element);
		$str  = "$({$element}).removeClass(\"$class\");";
		return $str;
	}

	// --------------------------------------------------------------------
			
	/**
	 * Slide Up
	 *
	 * Outputs a jQuery slideUp event
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _slideUp($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);	
		$speed = $this->_validate_speed($speed);
		
		if ($callback != '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}
		
		$str  = "$({$element}).slideUp({$speed}{$callback});";
		
		return $str;
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Slide Down
	 *
	 * Outputs a jQuery slideDown event
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _slideDown($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);
		
		if ($callback != '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}
		
		$str  = "$({$element}).slideDown({$speed}{$callback});";
		
		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Slide Toggle
	 *
	 * Outputs a jQuery slideToggle event
	 *
	 * @access	public
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _slideToggle($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);
		
		if ($callback != '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}
		
		$str  = "$({$element}).slideToggle({$speed}{$callback});";
		
		return $str;
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Toggle
	 *
	 * Outputs a jQuery toggle event
	 *
	 * @access	private
	 * @param	string	- element
	 * @return	string
	 */
	function _toggle($element = 'this')
	{
		$element = $this->_prep_element($element);
		$str  = "$({$element}).toggle();";
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Toggle Class
	 *
	 * Outputs a jQuery toggle class event
	 *
	 * @access	private
	 * @param	string	- element
	 * @return	string
	 */
	function _toggleClass($element = 'this', $class='')
	{
		$element = $this->_prep_element($element);
		$str  = "$({$element}).toggleClass(\"$class\");";
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Show
	 *
	 * Outputs a jQuery show event
	 *
	 * @access	private
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	function _show($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);	
		$speed = $this->_validate_speed($speed);
		
		if ($callback != '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}
		
		$str  = "$({$element}).show({$speed}{$callback});";
		
		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Updater
	 *
	 * An Ajax call that populates the designated DOM node with 
	 * returned content
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	the controller to run the call against
	 * @param	string	optional parameters
	 * @return	string
	 */
	
	function _updater($container = 'this', $controller, $options = '')
	{	
		$container = $this->_prep_element($container);
		
		$controller = (strpos('://', $controller) === FALSE) ? $controller : $this->CI->config->site_url($controller);
		
		// ajaxStart and ajaxStop are better choices here... but this is a stop gap
		if ($this->CI->config->item('javascript_ajax_img') == '')
		{
			$loading_notifier = "Loading...";
		}
		else
		{
			$loading_notifier = '<img src=\'' . $this->CI->config->slash_item('base_url') . $this->CI->config->item('javascript_ajax_img') . '\' alt=\'Loading\' />';
		}
		
		$updater = "$($container).empty();\n"; // anything that was in... get it out
		$updater .= "\t\t$($container).prepend(\"$loading_notifier\");\n"; // to replace with an image

		$request_options = '';
		if ($options != '')
		{
			$request_options .= ", {";
			$request_options .= (is_array($options)) ? "'".implode("', '", $options)."'" : "'".str_replace(":", "':'", $options)."'";
			$request_options .= "}";
		}

		$updater .= "\t\t$($container).load('$controller'$request_options);";
		return $updater;
	}


	// --------------------------------------------------------------------
	// Pre-written handy stuff
	// --------------------------------------------------------------------
	 
	/**
	 * Zebra tables
	 *
	 * @access	private
	 * @param	string	table name
	 * @param	string	plugin location
	 * @return	string
	 */
	function _zebraTables($class = '', $odd = 'odd', $hover = '')
	{
		$class = ($class != '') ? '.'.$class : '';
		
		$zebra  = "\t\$(\"table{$class} tbody tr:nth-child(even)\").addClass(\"{$odd}\");";

		$this->jquery_code_for_compile[] = $zebra;

		if ($hover != '')
		{
			$hover = $this->hover("table{$class} tbody tr", "$(this).addClass('hover');", "$(this).removeClass('hover');");
		}

		return $zebra;
	}



	// --------------------------------------------------------------------
	// Plugins
	// --------------------------------------------------------------------
	
	/**
	 * Corner Plugin
	 *
	 * http://www.malsup.com/jquery/corner/
	 *
	 * @access	public
	 * @param	string	target
	 * @return	string
	 */
	function corner($element = '', $corner_style = '')
	{
		// may want to make this configurable down the road
		$corner_location = '/plugins/jquery.corner.js';

		if ($corner_style != '')
		{
			$corner_style = '"'.$corner_style.'"';
		}

		return "$(" . $this->_prep_element($element) . ").corner(".$corner_style.");";
	}
	
	// --------------------------------------------------------------------

	/**
	 * modal window
	 *
	 * Load a thickbox modal window
	 *
	 * @access	public
	 * @return	void
	 */
	function modal($src, $relative = FALSE)
	{	
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Effect
	 *
	 * Load an Effect library
	 *
	 * @access	public
	 * @return	void
	 */
	function effect($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Plugin
	 *
	 * Load a plugin library
	 *
	 * @access	public
	 * @return	void
	 */
	function plugin($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * UI
	 *
	 * Load a user interface library
	 *
	 * @access	public
	 * @return	void
	 */
	function ui($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}
	// --------------------------------------------------------------------

	/**
	 * Sortable
	 *
	 * Creates a jQuery sortable
	 *
	 * @access	public
	 * @return	void
	 */
	function sortable($element, $options = array())
	{

		if (count($options) > 0)
		{
			$sort_options = array();
			foreach ($options as $k=>$v)
			{
				$sort_options[] = "\n\t\t".$k.': '.$v."";
			}
			$sort_options = implode(",", $sort_options);
		}
		else
		{
			$sort_options = '';
		}

		return "$(" . $this->_prep_element($element) . ").sortable({".$sort_options."\n\t});";
	}

	// --------------------------------------------------------------------

	/**
	 * Table Sorter Plugin
	 *
	 * @access	public
	 * @param	string	table name
	 * @param	string	plugin location
	 * @return	string
	 */
	function tablesorter($table = '', $options = '')
	{
		$this->jquery_code_for_compile[] = "\t$(" . $this->_prep_element($table) . ").tablesorter($options);\n";
	}
	
	// --------------------------------------------------------------------
	// Class functions
	// --------------------------------------------------------------------

	/**
	 * Add Event
	 *
	 * Constructs the syntax for an event, and adds to into the array for compilation
	 *
	 * @access	private
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @param	string	The event to pass
	 * @return	string
	 */	
	function _add_event($element, $js, $event)
	{
		if (is_array($js))
		{
			$js = implode("\n\t\t", $js);

		}

		$event = "\n\t$(" . $this->_prep_element($element) . ").{$event}(function(){\n\t\t{$js}\n\t});\n";
		$this->jquery_code_for_compile[] = $event;
		return $event;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile
	 *
	 * As events are specified, they are stored in an array
	 * This funciton compiles them all for output on a page
	 *
	 * @access	private
	 * @return	string
	 */
	function _compile($view_var = 'script_foot', $script_tags = TRUE)
	{
		// External references
		$external_scripts = implode('', $this->jquery_code_for_load);
		$this->CI->load->vars(array('library_src' => $external_scripts));

		if (count($this->jquery_code_for_compile) == 0 )
		{
			// no inline references, let's just return
			return;
		}

		// Inline references
		$script = '$(document).ready(function() {' . "\n";
		$script .= implode('', $this->jquery_code_for_compile);
		$script .= '});';
		
		$output = ($script_tags === FALSE) ? $script : $this->inline($script);

		$this->CI->load->vars(array($view_var => $output));

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clear Compile
	 *
	 * Clears the array of script events collected for output
	 *
	 * @access	public
	 * @return	void
	 */
	function _clear_compile()
	{
		$this->jquery_code_for_compile = array();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Document Ready
	 *
	 * A wrapper for writing document.ready()
	 *
	 * @access	private
	 * @return	string
	 */
	function _document_ready($js)
	{
		if ( ! is_array($js))
		{
			$js = array ($js);

		}
		
		foreach ($js as $script)
		{
			$this->jquery_code_for_compile[] = $script;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Script Tag
	 *
	 * Outputs the script tag that loads the jquery.js file into an HTML document
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function script($library_src = '', $relative = FALSE)
	{
		$library_src = $this->external($library_src, $relative);
		$this->jquery_code_for_load[] = $library_src;
		return $library_src;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Prep Element
	 *
	 * Puts HTML element in quotes for use in jQuery code
	 * unless the supplied element is the Javascript 'this'
	 * object, in which case no quotes are added
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function _prep_element($element)
	{
		if ($element != 'this')
		{
			$element = '"'.$element.'"';
		}
		
		return $element;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Validate Speed
	 *
	 * Ensures the speed parameter is valid for jQuery
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */	
	function _validate_speed($speed)
	{
		if (in_array($speed, array('slow', 'normal', 'fast')))
		{
			$speed = '"'.$speed.'"';
		}
		elseif (preg_match("/[^0-9]/", $speed))
		{
			$speed = '';
		}
	
		return $speed;
	}

}

/* End of file Jquery.php */
/* Location: ./system/libraries/Jquery.php */