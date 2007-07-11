<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Form Declaration
 *
 * Creates the opening portion of the form.
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */	
function form_open($action = '', $attributes = array(), $hidden = array())
{
	$CI =& get_instance();

	$form = '<form action="'.$CI->config->site_url($action).'"';
	
	if ( ! isset($attributes['method']))
	{
		$form .= ' method="post"';
	}
	
	if (is_array($attributes) AND count($attributes) > 0)
	{
		foreach ($attributes as $key => $val)
		{
			$form .= ' '.$key.'="'.$val.'"';
		}
	}
	
	$form .= '>';

	if (is_array($hidden) AND count($hidden > 0))
	{
		$form .= form_hidden($hidden);
	}
	
	return $form;
}
	
// ------------------------------------------------------------------------

/**
 * Form Declaration - Multipart type
 *
 * Creates the opening portion of the form, but with "multipart/form-data".
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */	
function form_open_multipart($action, $attributes = array(), $hidden = array())
{
	$attributes['enctype'] = 'multipart/form-data';
	return form_open($action, $attributes, $hidden);
}
	
// ------------------------------------------------------------------------

/**
 * Hidden Input Field
 *
 * Generates hidden fields.  You can pass a simple key/value string or an associative
 * array with multiple values.
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @return	string
 */	
function form_hidden($name, $value = '')
{
	if ( ! is_array($name))
	{
		return '<input type="hidden" name="'.$name.'" value="'.form_prep($value).'" />';
	}

	$form = '';
	foreach ($name as $name => $value)
	{
		$form .= '<input type="hidden" name="'.$name.'" value="'.form_prep($value).'" />';
	}
	
	return $form;
}
	
// ------------------------------------------------------------------------

/**
 * Text Input Field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
function form_input($data = '', $value = '', $extra = '')
{
	$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value, 'maxlength' => '500', 'size' => '50');

	return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
}
	
// ------------------------------------------------------------------------

/**
 * Password Field
 *
 * Identical to the input function but adds the "password" type
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
function form_password($data = '', $value = '', $extra = '')
{
	if ( ! is_array($data))
	{
		$data = array('name' => $data);
	}

	$data['type'] = 'password';
	return form_input($data, $value, $extra);
}
	
// ------------------------------------------------------------------------

/**
 * Upload Field
 *
 * Identical to the input function but adds the "file" type
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
function form_upload($data = '', $value = '', $extra = '')
{
	if ( ! is_array($data))
	{
		$data = array('name' => $data);
	}

	$data['type'] = 'file';
	return form_input($data, $value, $extra);
}
	
// ------------------------------------------------------------------------

/**
 * Textarea field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
function form_textarea($data = '', $value = '', $extra = '')
{
	$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'cols' => '90', 'rows' => '12');
	
    if ( ! is_array($data) OR ! isset($data['value']))
	{
		$val = $value;
	}
    else
	{
		$val = $data['value']; 
		unset($data['value']); // textareas don't use the value attribute
	}
		
	return "<textarea ".parse_form_attributes($data, $defaults).$extra.">".$val."</textarea>\n";
}
	
// ------------------------------------------------------------------------

/**
 * Drop-down Menu
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	string
 * @param	string
 * @return	string
 */	
function form_dropdown($name = '', $options = array(), $selected = '', $extra = '')
{
	if ($extra != '') $extra = ' '.$extra;
		
	$form = '<select name="'.$name.'"'.$extra.">\n";
	
	foreach ($options as $key => $val)
	{
		$key = (string) $key;
		$val = (string) $val;
		
		$sel = ($selected != $key) ? '' : ' selected="selected"';
		
		$form .= '<option value="'.$key.'"'.$sel.'>'.$val."</option>\n";
	}

	$form .= '</select>';
	
	return $form;
}
	
// ------------------------------------------------------------------------

/**
 * Checkbox Field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	bool
 * @param	string
 * @return	string
 */	
function form_checkbox($data = '', $value = '', $checked = TRUE, $extra = '')
{
	$defaults = array('type' => 'checkbox', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);
	
	if (is_array($data) AND array_key_exists('checked', $data))
	{
		$checked = $data['checked'];
		
		if ($checked == FALSE)
		{
			unset($data['checked']);
		}
		else
		{
			$data['checked'] = 'checked';
		}
	}
	
	if ($checked == TRUE)
		$defaults['checked'] = 'checked';
	else
		unset($defaults['checked']);

	return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
}
	
// ------------------------------------------------------------------------

/**
 * Radio Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	bool
 * @param	string
 * @return	string
 */	
function form_radio($data = '', $value = '', $checked = TRUE, $extra = '')
{
	if ( ! is_array($data))
	{	
		$data = array('name' => $data);
	}

	$data['type'] = 'radio';
	return form_checkbox($data, $value, $checked, $extra);
}
	
// ------------------------------------------------------------------------

/**
 * Submit Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
function form_submit($data = '', $value = '', $extra = '')
{
	$defaults = array('type' => 'submit', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

	return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
}
	
// ------------------------------------------------------------------------

/**
 * Form Close Tag
 *
 * @access	public
 * @param	string
 * @return	string
 */	
function form_close($extra = '')
{
	return "</form>\n".$extra;
}
	
// ------------------------------------------------------------------------

/**
 * Form Prep
 *
 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
 *
 * @access	public
 * @param	string
 * @return	string
 */	
function form_prep($str = '')
{
	if ($str === '')
	{
		return '';
	}

	$temp = '__TEMP_AMPERSANDS__';
	
	// Replace entities to temporary markers so that 
	// htmlspecialchars won't mess them up
	$str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);
	$str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);

	$str = htmlspecialchars($str);

	// In case htmlspecialchars misses these.
	$str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);	
	
	// Decode the temp markers back to entities
	$str = preg_replace("/$temp(\d+);/","&#\\1;",$str);
	$str = preg_replace("/$temp(\w+);/","&\\1;",$str);	
	
	return $str;	
}
	
// ------------------------------------------------------------------------

/**
 * Parse the form attributes
 *
 * Helper function used by some of the form helpers
 *
 * @access	private
 * @param	array
 * @param	array
 * @return	string
 */	
function parse_form_attributes($attributes, $default)
{
	if (is_array($attributes))
	{
		foreach ($default as $key => $val)
		{
			if (isset($attributes[$key]))
			{
				$default[$key] = $attributes[$key];
				unset($attributes[$key]);
			}
		}
		
		if (count($attributes) > 0)
		{	
			$default = array_merge($default, $attributes);
		}
	}
	
	$att = '';
	foreach ($default as $key => $val)
	{
		if ($key == 'value')
		{
			$val = form_prep($val);
		}
	
		$att .= $key . '="' . $val . '" ';
	}

	return $att;
}

?>