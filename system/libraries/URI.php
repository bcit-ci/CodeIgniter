<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * URI Class
 * 
 * Parses URIs and determines routing
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	URI
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/uri.html
 */
class CI_URI {

	var $router;
	var	$keyval	= array();

	/**
	 * Constructor
	 *
	 * Simply globalizes the $RTR object.  The front
	 * loads the Router class early on so it's not available
	 * normally as other classes are. 
	 *
	 * @access	public
	 */		
	function CI_URI()
	{
		$this->router =& _load_class('CI_Router');	
		log_message('debug', "URI Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a URI Segment
	 *
	 * This function returns the URI segment based on the number provided.  
	 *
	 * @access	public
	 * @param	integer
	 * @param	bool
	 * @return	string
	 */
	function segment($n, $no_result = FALSE)
	{
		return ( ! isset($this->router->segments[$n])) ? $no_result : $this->router->segments[$n];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Generate a key value pair from the URI string
	 *
	 * This function generates and associative array of URI data starting 
	 * at the supplied segment. For example, if this is your URI:
	 *
	 *	www.your-site.com/user/search/name/joe/location/UK/gender/male
	 *
	 * You can use this function to generate an array with this prototype:
	 *
	 * array (
	 *			name => joe
	 *			location => UK
	 *			gender => male
	 *		 )
	 * 
	 * @access	public
	 * @param	integer	the starting segment number
	 * @param	array	an array of default values
	 * @return	array
	 */
	function uri_to_assoc($n = 3, $default = array())
	{
		if ( ! is_numeric($n))
		{
			return $default;
		}
	
		if (isset($this->keyval[$n]))
		{
			return $this->keyval[$n];
		}
	
		if ($this->total_segments() < $n)
		{
			if (count($default) == 0)
			{
				return array();
			}
			
			$retval = array();
			foreach ($default as $val)
			{
				$retval[$val] = FALSE;
			}		
			return $retval;
		}

		$segments = array_slice($this->segment_array(), ($n - 1));

		$i = 0;
		$lastval = '';
		$retval  = array();
		foreach ($segments as $seg)
		{
			if ($i % 2)
			{
				$retval[$lastval] = $seg;
			}
			else
			{
				$retval[$seg] = FALSE;
				$lastval = $seg;
			}
		
			$i++;
		}

		if (count($default) > 0)
		{
			foreach ($default as $val)
			{
				if ( ! array_key_exists($val, $retval))
				{
					$retval[$val] = FALSE;
				}
			}
		}

		// Cache the array for reuse
		$this->keyval[$n] = $retval;
		return $retval;
	}

	/**
	 * Generate a URI string from an associative array
	 *
	 * 
	 * @access	public
	 * @param	array	an associative array of key/values
	 * @return	array
	 */	function assoc_to_uri($array)
	{	
		$temp = array();
		foreach ((array)$array as $key => $val)
		{
			$temp[] = $key;
			$temp[] = $val;
		}
		
		return implode('/', $temp);
	}

	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a URI Segment and add a trailing slash
	 *
	 * @access	public
	 * @param	integer
	 * @param	string
	 * @return	string
	 */
	function slash_segment($n, $where = 'trailing')
	{	
		if ($where == 'trailing')
		{
			$trailing	= '/';
			$leading	= '';
		}
		elseif ($where == 'leading')
		{
			$leading	= '/';
			$trailing	= '';
		}
		else
		{
			$leading	= '/';
			$trailing	= '/';
		}
		return ( ! isset($this->router->segments[$n])) ? '' : $leading.$this->router->segments[$n].$trailing;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Segment Array
	 *
	 * @access	public
	 * @return	array
	 */
	function segment_array()
	{
		return $this->router->segments;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Total number of segments
	 *
	 * @access	public
	 * @return	integer
	 */
	function total_segments()
	{
		return count($this->router->segments);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch the entire URI string
	 *
	 * @access	public
	 * @return	string
	 */
	function uri_string()
	{
		return $this->router->uri_string;
	}

}
// END URI Class
?>