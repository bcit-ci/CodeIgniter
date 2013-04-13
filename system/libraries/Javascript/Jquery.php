<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
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
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Jquery Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Javascript
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/javascript.html
 */
class CI_Jquery extends CI_Javascript {

	/**
	 * JavaScript directory location
	 *
	 * @var	string
	 */
	protected $_javascript_folder = 'js';

	/**
	 * JQuery code for load
	 *
	 * @var	array
	 */
	public $jquery_code_for_load = array();

	/**
	 * JQuery code for compile
	 *
	 * @var	array
	 */
	public $jquery_code_for_compile = array();

	/**
	 * JQuery corner active flag
	 *
	 * @var	bool
	 */
	public $jquery_corner_active = FALSE;

	/**
	 * JQuery table sorter active flag
	 *
	 * @var	bool
	 */
	public $jquery_table_sorter_active = FALSE;

	/**
	 * JQuery table sorder pager active
	 *
	 * @var	bool
	 */
	public $jquery_table_sorter_pager_active = FALSE;

	/**
	 * JQuery AJAX image
	 *
	 * @var	string
	 */
	public $jquery_ajax_img = '';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		$this->CI =& get_instance();
		extract($params);

		if ($autoload === TRUE)
		{
			$this->script();
		}

		log_message('debug', 'Jquery Class Initialized');
	}

	// --------------------------------------------------------------------
	// Event Code
	// --------------------------------------------------------------------

	/**
	 * Error
	 *
	 * Outputs a jQuery error event
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @param	bool	Whether to trigger the event immediately after declaring it
	 * @return	string
	 */
	protected function _error($element = 'this', $js = '', $trigger = FALSE)
	{
		return $this->_add_event($element, $js, 'error', $trigger);
	}

	// --------------------------------------------------------------------

	/**
	 * Hover
	 *
	 * Outputs a jQuery hover event
	 *
	 * @param	string	- element
	 * @param	string	- Javascript code for mouse over
	 * @param	string	- Javascript code for mouse out
	 * @return	string
	 */
	protected function _hover($element = 'this', $over, $out)
	{
		$event = "\n\t".$this->_prep_element($element).".hover(\n\t\tfunction()\n\t\t{\n\t\t\t{$over}\n\t\t}, \n\t\tfunction()\n\t\t{\n\t\t\t{$out}\n\t\t});\n";

		$this->jquery_code_for_compile[] = $event;

		return $event;
	}

	// --------------------------------------------------------------------

	/**
	 * Output
	 *
	 * Outputs script directly
	 *
	 * @param	array	$array_js = array()
	 * @return	void
	 */
	protected function _output($array_js = array())
	{
		if ( ! is_array($array_js))
		{
			$array_js = array($array_js);
		}

		foreach ($array_js as $js)
		{
			$this->jquery_code_for_compile[] = "\t".$js."\n";
		}
	}

	// --------------------------------------------------------------------
	// Effects
	// --------------------------------------------------------------------

	/**
	 * Add Class
	 *
	 * Outputs a jQuery addClass event
	 *
	 * @param	string	$element
	 * @param	string	$class
	 * @return	string
	 */
	protected function _addClass($element = 'this', $class = '')
	{
		$element = $this->_prep_element($element);
		return $element.'.addClass("'.$class.'");';
	}

	// --------------------------------------------------------------------

	/**
	 * Animate
	 *
	 * Outputs a jQuery animate event
	 *
	 * @param	string	$element
	 * @param	array	$params
	 * @param	string	$speed	'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	$extra
	 * @return	string
	 */
	protected function _animate($element = 'this', $params = array(), $speed = '', $extra = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		$animations = "\t\t\t";

		foreach ($params as $param => $value)
		{
			$animations .= $param.": '".$value."', ";
		}

		$animations = substr($animations, 0, -2); // remove the last ", "

		if ($speed !== '')
		{
			$speed = ', '.$speed;
		}

		if ($extra !== '')
		{
			$extra = ', '.$extra;
		}

		return "{$element}.animate({\n$animations\n\t\t}".$speed.$extra.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Fade In
	 *
	 * Outputs a jQuery hide event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	protected function _fadeIn($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return "{$element}.fadeIn({$speed}{$callback});";
	}

	// --------------------------------------------------------------------

	/**
	 * Fade Out
	 *
	 * Outputs a jQuery hide event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	protected function _fadeOut($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return $element.'.fadeOut('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Hide
	 *
	 * Outputs a jQuery hide action
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	protected function _hide($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return "{$element}.hide({$speed}{$callback});";
	}

	// --------------------------------------------------------------------

	/**
	 * Remove Class
	 *
	 * Outputs a jQuery remove class event
	 *
	 * @param	string	$element
	 * @param	string	$class
	 * @return	string
	 */
	protected function _removeClass($element = 'this', $class = '')
	{
		$element = $this->_prep_element($element);
		return $element.'.removeClass("'.$class.'");';
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Up
	 *
	 * Outputs a jQuery slideUp event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	protected function _slideUp($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return $element.'.slideUp('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Down
	 *
	 * Outputs a jQuery slideDown event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	protected function _slideDown($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return $element.'.slideDown('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Toggle
	 *
	 * Outputs a jQuery slideToggle event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	protected function _slideToggle($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return $element.'.slideToggle('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Toggle
	 *
	 * Outputs a jQuery toggle event
	 *
	 * @param	string	- element
	 * @param	mixed - blank to toggle, true or false to force toggle state,
	 *                  string for custom jquery JS
	 * @return	string
	 */
	protected function _toggle($element = 'this', $show_or_hide = '')
	{
		if (is_bool($show_or_hide))
		{
			$show_or_hide = $show_or_hide ? 'true' : 'false';
		}
		else if (!empty($show_or_hide) && is_string($show_or_hide))
		{
			$show_or_hide = $show_or_hide;
		}

		$element = $this->_prep_element($element);
		return $element.'.toggle('.$show_or_hide.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Toggle Class
	 *
	 * Outputs a jQuery toggle class event
	 *
	 * @param	string	$element
	 * @param	string	$class
	 * @param	mixed - blank to toggle, true or false to force toggle state,
	 *                  string for custom jquery JS
	 * @return	string
	 */
	protected function _toggleClass($element = 'this', $class = '', $switch = '')
	{
		if (is_bool($switch))
		{
			$switch = ', '.($switch ? 'true' : 'false');
		}
		else if (!empty($switch) && is_string($switch))
		{
			$switch = ', '.$switch;
		}

		$element = $this->_prep_element($element);
		return $element.'.toggleClass("'.$class.'"'.$switch.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Show
	 *
	 * Outputs a jQuery show event
	 *
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds
	 * @param	string	- Javascript callback function
	 * @return	string
	 */
	protected function _show($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return $element.'.show('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Updater
	 *
	 * An Ajax call that populates the designated DOM node with
	 * returned content
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	the controller to run the call against
	 * @param	string	optional parameters
	 * @return	string
	 */

	protected function _updater($container = 'this', $controller, $options = '')
	{
		$container = $this->_prep_element($container);
		$controller = (strpos('://', $controller) === FALSE) ? $controller : $this->CI->config->site_url($controller);

		// ajaxStart and ajaxStop are better choices here... but this is a stop gap
		if ($this->CI->config->item('javascript_ajax_img') === '')
		{
			$loading_notifier = 'Loading...';
		}
		else
		{
			$loading_notifier = '<img src="'.$this->CI->config->slash_item('base_url').$this->CI->config->item('javascript_ajax_img').'" alt="Loading" />';
		}

		$updater = $container.".empty();\n" // anything that was in... get it out
			."\t\t".$container.'.prepend("'.$loading_notifier."\");\n"; // to replace with an image

		$request_options = '';
		if ($options !== '')
		{
			$request_options .= ', {'
					.(is_array($options) ? "'".implode("', '", $options)."'" : "'".str_replace(':', "':'", $options)."'")
					.'}';
		}

		return $updater."\t\t{$container}.load('$controller'$request_options);";
	}

	// --------------------------------------------------------------------
	// Pre-written handy stuff
	// --------------------------------------------------------------------

	/**
	 * Zebra tables
	 *
	 * @param	string	$class
	 * @param	string	$odd
	 * @param	string	$hover
	 * @return	string
	 */
	protected function _zebraTables($class = '', $odd = 'odd', $hover = '')
	{
		$class = ($class !== '') ? '.'.$class : '';
		$zebra = "\t\$(\"table{$class} tbody tr:nth-child(even)\").addClass(\"{$odd}\");";

		$this->jquery_code_for_compile[] = $zebra;

		if ($hover !== '')
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
	 * @link	http://www.malsup.com/jquery/corner/
	 * @param	string	$element
	 * @param	string	$corner_style
	 * @return	string
	 */
	public function corner($element = '', $corner_style = '')
	{
		// may want to make this configurable down the road
		$corner_location = '/plugins/jquery.corner.js';

		if ($corner_style !== '')
		{
			$corner_style = '"'.$corner_style.'"';
		}

		return $this->_prep_element($element).'.corner('.$corner_style.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Modal window
	 *
	 * Load a thickbox modal window
	 *
	 * @param	string	$src
	 * @param	bool	$relative
	 * @return	void
	 */
	public function modal($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Effect
	 *
	 * Load an Effect library
	 *
	 * @param	string	$src
	 * @param	bool	$relative
	 * @return	void
	 */
	public function effect($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Plugin
	 *
	 * Load a plugin library
	 *
	 * @param	string	$src
	 * @param	bool	$relative
	 * @return	void
	 */
	public function plugin($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * UI
	 *
	 * Load a user interface library
	 *
	 * @param	string	$src
	 * @param	bool	$relative
	 * @return	void
	 */
	public function ui($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Sortable
	 *
	 * Creates a jQuery sortable
	 *
	 * @param	string	$element
	 * @param	array	$options
	 * @return	string
	 */
	public function sortable($element, $options = array())
	{
		if (count($options) > 0)
		{
			$sort_options = array();
			foreach ($options as $k=>$v)
			{
				$sort_options[] = "\n\t\t".$k.': '.$v;
			}
			$sort_options = implode(',', $sort_options);
		}
		else
		{
			$sort_options = '';
		}

		return $this->_prep_element($element).'.sortable({'.$sort_options."\n\t});";
	}

	// --------------------------------------------------------------------

	/**
	 * Table Sorter Plugin
	 *
	 * @param	string	table name
	 * @param	string	plugin location
	 * @return	string
	 */
	public function tablesorter($table = '', $options = '')
	{
		$this->jquery_code_for_compile[] = "\t".$this->_prep_element($table).'.tablesorter('.$options.");\n";
	}

	// --------------------------------------------------------------------
	// Class functions
	// --------------------------------------------------------------------

	/**
	 * Add Event
	 *
	 * Constructs the syntax for an event, and adds to into the array for compilation
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The code to execute
	 * @param	string	The event to pass
	 * @param	bool	Whether to trigger the event immediately after declaring it
	 * @return	string
	 */
	protected function _add_event($element, $js, $event, $trigger = FALSE, $prevent_default = FALSE)
	{
		is_array($js) OR $js = array($js);

		if ($prevent_default)
		{
			$js[] = 'event.preventDefault();';
		}

		$js = implode("\n\t\t", $js);

		$event = '.on("'.$event . '"'.", (function(event){\n\t\t{$js}\n\t}))".($trigger ? $this->_trigger_event(FALSE, $event) : '');

		if ( $element )
		{
			$event = "\n\t".$this->_prep_element($element).$event.";\n";
			$this->jquery_code_for_compile[] = $event;
		}

		return $event;
	}

	/**
	 * Trigger Event
	 *
	 * Constructs the syntax for triggering an event, and adds to into the array for compilation if applicable
	 *
	 * @param	string	The element to attach the event to
	 * @param	string	The event to trigger
	 * @return	string
	 */
	protected function _trigger_event($element = 'this', $event)
	{
		$event = '.trigger("'.$event.'")';

		if ($element)
		{
			$event = "\n\t".$this->_prep_element($element).$event.";\n";
			$this->jquery_code_for_compile[] = $event;
		}

		return $event;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile
	 *
	 * As events are specified, they are stored in an array
	 * This funciton compiles them all for output on a page
	 *
	 * @param	string	$view_var
	 * @param	bool	$script_tags
	 * @return	void
	 */
	protected function _compile($view_var = 'script_foot', $script_tags = TRUE)
	{
		// External references
		$external_scripts = implode('', $this->jquery_code_for_load);
		$this->CI->load->vars(array('library_src' => $external_scripts));

		if (count($this->jquery_code_for_compile) === 0)
		{
			// no inline references, let's just return
			return;
		}

		// Inline references
		$script = '$(document).ready(function() {'."\n"
			.implode('', $this->jquery_code_for_compile)
			.'});';

		$output = ($script_tags === FALSE) ? $script : $this->inline($script);

		$this->CI->load->vars(array($view_var => $output));
	}

	// --------------------------------------------------------------------

	/**
	 * Clear Compile
	 *
	 * Clears the array of script events collected for output
	 *
	 * @return	void
	 */
	protected function _clear_compile()
	{
		$this->jquery_code_for_compile = array();
	}

	// --------------------------------------------------------------------

	/**
	 * Document Ready
	 *
	 * A wrapper for writing document.ready()
	 *
	 * @param	array	$js
	 * @return	void
	 */
	protected function _document_ready($js)
	{
		is_array($js) OR $js = array($js);

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
	 * @param	string	$library_src
	 * @param	bool	$relative
	 * @return	string
	 */
	public function script($library_src = '', $relative = FALSE)
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
	 * @param	string
	 * @return	string
	 */
	protected function _prep_element($element = 'this')
	{
		if (is_string($element))
		{
			if ($element === 'this')
			{
				return '$('.$element.')';
			}

			if (isset($element[0]) && $element[0] == '$')
			{
				return $element;
			} else {
				return '$(\'' . $element . '\')';
			}
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Speed
	 *
	 * Ensures the speed parameter is valid for jQuery
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _validate_speed($speed)
	{
		if (in_array($speed, array('slow', 'normal', 'fast')))
		{
			return '"'.$speed.'"';
		}
		elseif (preg_match('/[^0-9]/', $speed))
		{
			return '';
		}

		return $speed;
	}

}

/* End of file Jquery.php */
/* Location: ./system/libraries/Jquery.php */