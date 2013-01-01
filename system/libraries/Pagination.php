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
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class CI_Pagination {

	/**
	 * Base URL
	 *
	 * The page that we're linking to
	 *
	 * @var	string
	 */
	protected $base_url		= '';

	/**
	 * Prefix
	 *
	 * @var	string
	 */
	protected $prefix		= '';

	/**
	 * Suffix
	 *
	 * @var	string
	 */
	protected $suffix		= '';

	/**
	 * Total number of items
	 *
	 * @var	int
	 */
	protected $total_rows		= 0;

	/**
	 * Items per page
	 *
	 * @var	int
	 */
	protected $per_page		= 10;

	/**
	 * Number of links to show
	 *
	 * Relates to "digit" type links shown before/after
	 * the currently viewed page.
	 *
	 * @var	int
	 */
	protected $num_links		= 2;

	/**
	 * Current page
	 *
	 * @var	int
	 */
	protected $cur_page		= 0;

	/**
	 * Use page numbers flag
	 *
	 * Whether to use actual page numbers instead of an offset
	 *
	 * @var	bool
	 */
	protected $use_page_numbers	= FALSE;

	/**
	 * First link
	 *
	 * @var	string
	 */
	protected $first_link		= '&lsaquo; First';

	/**
	 * Next link
	 *
	 * @var	string
	 */
	protected $next_link		= '&gt;';

	/**
	 * Previous link
	 *
	 * @var	string
	 */
	protected $prev_link		= '&lt;';

	/**
	 * Last link
	 *
	 * @var	string
	 */
	protected $last_link		= 'Last &rsaquo;';

	/**
	 * URI Segment
	 *
	 * @var	int
	 */
	protected $uri_segment		= 3;

	/**
	 * Full tag open
	 *
	 * @var	string
	 */
	protected $full_tag_open	= '';

	/**
	 * Full tag close
	 *
	 * @var	string
	 */
	protected $full_tag_close	= '';

	/**
	 * First tag open
	 *
	 * @var	string
	 */
	protected $first_tag_open	= '';

	/**
	 * First tag close
	 *
	 * @var	string
	 */
	protected $first_tag_close	= '';

	/**
	 * Last tag open
	 *
	 * @var	string
	 */
	protected $last_tag_open	= '';

	/**
	 * Last tag close
	 *
	 * @var	string
	 */
	protected $last_tag_close	= '';

	/**
	 * First URL
	 *
	 * An alternative URL for the first page
	 *
	 * @var	string
	 */
	protected $first_url		= '';

	/**
	 * Current tag open
	 *
	 * @var	string
	 */
	protected $cur_tag_open		= '<strong>';

	/**
	 * Current tag close
	 *
	 * @var	string
	 */
	protected $cur_tag_close	= '</strong>';

	/**
	 * Next tag open
	 *
	 * @var	string
	 */
	protected $next_tag_open	= '';

	/**
	 * Next tag close
	 *
	 * @var	string
	 */
	protected $next_tag_close	= '';

	/**
	 * Previous tag open
	 *
	 * @var	string
	 */
	protected $prev_tag_open	= '';

	/**
	 * Previous tag close
	 *
	 * @var	string
	 */
	protected $prev_tag_close	= '';

	/**
	 * Number tag open
	 *
	 * @var	string
	 */
	protected $num_tag_open		= '';

	/**
	 * Number tag close
	 *
	 * @var	string
	 */
	protected $num_tag_close	= '';

	/**
	 * Page query string flag
	 *
	 * @var	bool
	 */
	protected $page_query_string	= FALSE;

	/**
	 * Query string segment
	 *
	 * @var	string
	 */
	protected $query_string_segment = 'per_page';

	/**
	 * Display pages flag
	 *
	 * @var	bool
	 */
	protected $display_pages	= TRUE;

	/**
	 * Attributes
	 *
	 * @var	string
	 */
	protected $_attributes		= '';

	/**
	 * Link types
	 *
	 * "rel" attribute
	 *
	 * @see	CI_Pagination::_attr_rel()
	 * @var	array
	 */
	protected $_link_types		= array();

	/**
	 * Reuse query string flag
	 *
	 * @var	bool
	 */
	protected $reuse_query_string   = FALSE;

	/**
	 * Data page attribute
	 *
	 * @var	string
	 */
	protected $data_page_attr	= 'data-ci-pagination-page';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param	array	$params	Initialization parameters
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
	 * @param	array	$params	Initialization parameters
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
			$this->cur_page = (int) str_replace(array($this->prefix, $this->suffix), '', $CI->uri->rsegment($this->uri_segment));
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
			$this->cur_page = (int) $CI->uri->rsegment($this->uri_segment);
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
			$segment = (strpos($this->base_url, '?')) ? '&amp;' : '?';
			$this->base_url = rtrim($this->base_url).$segment.$this->query_string_segment.'=';
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

			if ( ! empty($get))
			{
				// Put everything else onto the end
				$query_string = (strpos($this->base_url, '?') !== FALSE ? '&amp;' : '?')
						.http_build_query($get, '', '&amp;');

				// Add this after the suffix to put it into more links easily
				$this->suffix .= $query_string;
			}
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
	 * @param	array	$attributes
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
	 * @param	string	$type
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