<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Language Checker Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Roberto Martinez
 * @link		http://codeigniter.com/user_guide/helpers/form_helper.html
 *
 Sample:
 		$this->load->helper('language_checker');
		$this->load->helper('directory');
		$struct = $map = directory_map('./application/language');		
		echo compare_language_files ( $struct );
 /

// ------------------------------------------------------------------------

/**
 * Compare Language Files
 *
 * Returns html string with all the language strings, ready to compare.
 *
 * @access	public
 * @param	array	array containing language directory contents
 * @return	string	tables containing all the language strings
 */
if ( ! function_exists('compare_language_files'))
{
	function compare_language_files ( $struct )
	{
		$files = get_language_files ( $struct );
		$languages = get_languages ( $struct );
		$map = map_languages ( $struct );
		$string = "";

		//get keys by file
		foreach ( $files as $file )
		{
			$keys = get_language_lines ( $map, $file );
			
			$string .= $file;
			foreach ( $files as $file )
			{
				//create table header
				$string .= "<TABLE BORDER CELLPADDING=1><tr><td>Key</td>";
				foreach ( $languages as $language)
				{
					$string .=  "<td>".$language."</td>";
				}
				$string .= "</tr>";
				
				//create rows
				foreach ( $keys as $key )
				{
					$string .= "<tr><td>".$key."</td>";
					foreach ( $languages as $language )
					{
						if ( isset ( $map [ $language ] [ $file ] [ $key ] ) )
						{
							$string .=  "<td>".$map [ $language ] [ $file ] [ $key ]."</td>";
						}
						else
						{
							$string .=  "<td>----</td>";
						}
					}
					$string .=  "</tr>";
				}
				//end table
				$string .=  "</table>";
			}			
		}
		
		return $string;
	}
}

/**
 * Map Languages
 *
 * Returns an array with all the languages strings: map[language][file][key].
 *
 * @access	public
 * @param	array	array containing language directory contents
 * @return	array
 */
if ( ! function_exists('map_languages'))
{
	function map_languages ( $struct )
	{
		$languages = get_languages ( $struct );
		$files = get_language_files ( $struct );
		$map = array ();

		foreach ( $files as $file)
		{
			foreach ( $languages as $language )
			{
				$path = 'application/language/'.$language.'/'.$file;
				if ( file_exists ( $path ) )
				{
					include_once ( $path );					
					$map [ $language ] [ $file ] = $lang; 
					unset ( $lang );
				}				
			}
		}
		
		return  $map;
	}
}

/**
 * Get Languages
 *
 * Returns an array with all the languages
 *
 * @access	public
 * @param	array	array containing language directory contents
 * @return	array
 */
 if ( ! function_exists('get_languages'))
{
	function get_languages ( $struct )

	{
		$languages = array ();
		foreach ( $struct as $key => $language )
		{			
			array_push ( $languages, $key);
		}
		return $languages ;
	}	
}
	
/**
 * Get Language Files
 *
 * Returns an array with all the languages files
 *
 * @access	public
 * @param	array	array containing language directory contents
 * @return	array
 */
 if ( ! function_exists('get_language_files'))
{
	function get_language_files ( $struct )
	//----------------------------------
	{
		$files = array ();
		foreach ( $struct as $key => $language )
		{			
			foreach ( $language as $file)
			{
				if ( !in_array ( $file, $files ) AND strpos ( $file, '_lang.php' ) )
				{	array_push ( $files, $file);}
			}
		}
		return $files ;
	}
}
	
/**
 * Get Language Lines
 *
 * Returns an array with all the languages keys 
 *
 * @access	public
 * @param	array	array containing language directory contents
 * @param	string	contains name of a language file that will be accesed in different languages
 * @return	array
 */
if ( ! function_exists('get_language_lines'))
{
	function get_language_lines ( $map, $the_file )
	//----------------------------------
	{
		$keys = array ();
		foreach ( $map as $key => $language )
		{			
			foreach ( $language as $file => $lines )
			{
				if ( $file == $the_file )
				{
					foreach ( $lines as $key2 => $line )
					{
						if ( !in_array ( $key2, $keys ) )
						{	array_push ( $keys, $key2 );}
					}
				}
			}
		}
		return $keys ;
	}	
}