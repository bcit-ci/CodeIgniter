<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class CI_Pagination {

	protected $base_url				= ''; // The page we are linking to
	protected $prefix				= ''; // A custom prefix added to the path.
	protected $suffix				= ''; // A custom suffix added to the path.
	protected $total_rows			= 0; // Total number of items (database results)
	protected $per_page				= 10; // Max number of items you want shown per page
	protected $num_links			= 2; // Number of "digit" links to show before/after the currently viewed page
	protected $cur_page				= 0; // The current page being viewed
	protected $use_page_numbers		= FALSE; // Use page number for segment instead of offset
	protected $first_link			= '&lsaquo; First';
	protected $next_link			= '&gt;';
	protected $prev_link			= '&lt;';
	protected $last_link			= 'Last &rsaquo;';
	protected $uri_segment			= 3;
	protected $full_tag_open		= '';
	protected $full_tag_close		= '';
	protected $first_tag_open		= '';
	protected $first_tag_close		= '';
	protected $last_tag_open		= '';
	protected $last_tag_close		= '';
	protected $first_url			= ''; // Alternative URL for the First Page.
	protected $cur_tag_open			= '<strong>';
	protected $cur_tag_close		= '</strong>';
	protected $next_tag_open		= '';
	protected $next_tag_close		= '';
	protected $prev_tag_open		= '';
	protected $prev_tag_close		= '';
	protected $num_tag_open			= '';
	protected $num_tag_close		= '';
	protected $page_query_string	= FALSE;
	protected $query_string_segment = 'per_page';
	protected $display_pages		= TRUE;
	protected $_attributes			= '';
	protected $_link_types			= array();
	protected $reuse_query_string   = FALSE;
	protected $data_page_attr		= 'data-ci-pagination-page';

	/**
	 * Constructor
	 *
	 * @param	array	initialization parameters
	 * @return	void
	 */
	public function __construct($params = array())
	{
		$this->initialize($params);
		log_message('debug', 'Pagination Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @param	array	initialization parameters
	 * @return	void
	 */
	public function initialize($params = array())
	{
		$attributes = array();

		if (isset($params['attributes']) && is_array($params['attributes']))
		{
			$attributes = $params['attributes'];
			unset($params['attributes']);
		}

		// Deprecated legacy support for the anchor_class option
		// Should be removed in CI 3.1+
		if (isset($params['anchor_class']))
		{
			empty($params['anchor_class']) OR $attributes['class'] = $params['anchor_class'];
			unset($params['anchor_class']);
		}

		$this->_parse_attributes($attributes);

		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 *
	 * @return	string
	 */
	public function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows === 0 OR $this->per_page === 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = (int) ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages === 1)
		{
			return '';
		}

		// Set the base page index for starting page number
		$base_page = ($this->use_page_numbers) ? 1 : 0;

		// Determine the current page number.
		$CI =& get_instance();

		// See if we are using a prefix or suffix on links
		if ($this->prefix !== '' OR $this->suffix !== '')
		{
			$this->cur_page = (int) str_replace(array($this->prefix, $this->suffix), '', $CI->uri->segment($this->uri_segment));
		}

		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != $base_page)
			{
				$this->cur_page = (int) $CI->input->get($this->query_string_segment);
			}
		}
		elseif ( ! $this->cur_page && $CI->uri->segment($this->uri_segment) !== $base_page)
		{
			$this->cur_page = (int) $CI->uri->segment($this->uri_segment);
		}

		// Set current page to 1 if it's not valid or if using page numbers instead of offset
		if ( ! is_numeric($this->cur_page) OR ($this->use_page_numbers && $this->cur_page === 0))
		{
			$this->cur_page = $base_page;
		}

		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}

		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		elseif ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;

		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = (int) floor(($this->cur_page/$this->per_page) + 1);
		}

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start	= (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end	= (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Is pagination being used over GET or POST? If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			$this->base_url = rtrim($this->base_url).'&amp;'.$this->query_string_segment.'=';
		}
		else
		{
			$this->base_url = rtrim($this->base_url, '/') .'/';
		}

		// And here we go...
		$output = '';
		$query_string = '';

		// Add anything in the query string back to the links
		// Note: Nothing to do with query_string_segment or any other query string options
		if ($this->reuse_query_string === TRUE)
		{
			$get = $CI->input->get();

			// Unset the controll, method, old-school routing options
			unset($get['c'], $get['m'], $get[$this->query_string_segment]);

			if ( ! $get) $get = array();

			// Put everything else onto the end
			$query_string = (strpos($this->base_url, '&amp;') !== FALSE ? '&amp;' : '?') . http_build_query($get, '', '&amp;');

			// Add this after the suffix to put it into more links easily
			$this->suffix .= $query_string;
		}

		// Render the "First" link
		if ($this->first_link !== FALSE && $this->cur_page > ($this->num_links + 1))
		{
			$first_url = ($this->first_url === '') ? $this->base_url : $this->first_url;

			// Take the general parameters, and squeeze this pagination-page attr in there for JS fw's
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, 1);

			$output .= $this->first_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('start').'>'
				.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if ($this->prev_link !== FALSE && $this->cur_page !== 1)
		{
			$i = ($this->use_page_numbers) ? $uri_page_number - 1 : $uri_page_number - $this->per_page;

			// Take the general parameters, and squeeze this pagination-page attr in there for JS fw's
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

			if ($i === $base_page && $this->first_url !== '')
			{
				$output .= $this->prev_tag_open.'<a href="'.$this->first_url.$query_string.'"'.$attributes.$this->_attr_rel('prev').'>'
					.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else
			{
				$append = ($i === $base_page) ? $query_string : $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a href="'.$this->base_url.$append.'"'.$attributes.$this->_attr_rel('prev').'>'
					.$this->prev_link.'</a>'.$this->prev_tag_close;
			}

		}

		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++)
			{
				$i = ($this->use_page_numbers) ? $loop : ($loop * $this->per_page) - $this->per_page;

				// Take the general parameters, and squeeze this pagination-page attr in there for JS fw's
				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

				if ($i >= $base_page)
				{
					if ($this->cur_page === $loop)
					{
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
					}
					else
					{
						$n = ($i === $base_page) ? '' : $i;
						if ($n === '' && ! empty($this->first_url))
						{
							$output .= $this->num_tag_open.'<a href="'.$this->first_url.$query_string.'"'.$attributes.$this->_attr_rel('start').'>'
								.$loop.'</a>'.$this->num_tag_close;
						}
						else
						{
							$append = ($n === '') ? $query_string : $this->prefix.$n.$this->suffix;
							$output .= $this->num_tag_open.'<a href="'.$this->base_url.$append.'"'.$attributes.$this->_attr_rel('start').'>'
								.$loop.'</a>'.$this->num_tag_close;
						}
					}
				}
			}
		}

		// Render the "next" link
		if ($this->next_link !== FALSE && $this->cur_page < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $this->cur_page + 1 : $this->cur_page * $this->per_page;

			// Take the general parameters, and squeeze this pagination-page attr in there for JS fw's
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

			$output .= $this->next_tag_open.'<a href="'.$this->base_url.$this->prefix.$i.$this->suffix.'"'.$attributes
				.$this->_attr_rel('next').'>'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;

			// Take the general parameters, and squeeze this pagination-page attr in there for JS fw's
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, (int) $i);

			$output .= $this->last_tag_open.'<a href="'.$this->base_url.$this->prefix.$i.$this->suffix.'"'.$attributes.'>'
				.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes. Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace('#([^:])//+#', '\\1/', $output);

		// Add the wrapper HTML if exists
		return $this->full_tag_open.$output.$this->full_tag_close;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse attributes
	 *
	 * @param	array
	 * @return	void
	 */
	protected function _parse_attributes($attributes)
	{
		isset($attributes['rel']) OR $attributes['rel'] = TRUE;
		$this->_link_types = ($attributes['rel'])
					? array('start' => 'start', 'prev' => 'prev', 'next' => 'next')
					: array();
		unset($attributes['rel']);

		$this->_attributes = '';
		foreach ($attributes as $key => $value)
		{
			$this->_attributes .= ' '.$key.'="'.$value.'"';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add "rel" attribute
	 *
	 * @link	http://www.w3.org/TR/html5/links.html#linkTypes
	 * @param	string
	 * @return	string
	 */
	protected function _attr_rel($type)
	{
		if (isset($this->_link_types[$type]))
		{
			unset($this->_link_types[$type]);
			return ' rel="'.$type.'"';
		}

		return '';
	}

}

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */