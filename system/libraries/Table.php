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
 * @since		Version 1.3.1
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * HTML Table Generating Class
 *
 * Lets you create tables manually or from database result objects, or arrays.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	HTML Tables
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/table.html
 */
class CI_Table {

	/**
	 * Data for table rows
	 *
	 * @var array
	 */
	public $rows		= array();

	/**
	 * Data for table heading
	 *
	 * @var array
	 */
	public $heading		= array();

	/**
	 * Whether or not to automatically create the table header
	 *
	 * @var bool
	 */
	public $auto_heading	= TRUE;

	/**
	 * Table caption
	 *
	 * @var string
	 */
	public $caption		= NULL;

	/**
	 * Table layout template
	 *
	 * @var array
	 */
	public $template	= NULL;

	/**
	 * Newline setting
	 *
	 * @var string
	 */
	public $newline		= "\n";

	/**
	 * Contents of empty cells
	 *
	 * @var string
	 */
	public $empty_cells	= '';

	/**
	 * Callback for custom table layout
	 *
	 * @var function
	 */
	public $function	= FALSE;

	/**
	 * Set the template from the table config file if it exists
	 *
	 * @param	array	$config	(default: array())
	 * @return	void
	 */
	public function __construct($config = array())
	{
		// initialize config
		foreach ($config as $key => $val)
		{
			$this->template[$key] = $val;
		}

		log_message('debug', 'Table Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Set the template
	 *
	 * @param	array
	 * @return	bool
	 */
	public function set_template($template)
	{
		if ( ! is_array($template))
		{
			return FALSE;
		}

		$this->template = $template;
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the table heading
	 *
	 * Can be passed as an array or discreet params
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function set_heading($args = array())
	{
		$args = func_get_args();
		$this->heading = $this->_prep_args($args);
	}

	// --------------------------------------------------------------------

	/**
	 * Set columns. Takes a one-dimensional array as input and creates
	 * a multi-dimensional array with a depth equal to the number of
	 * columns. This allows a single array with many elements to be
	 * displayed in a table that has a fixed column count.
	 *
	 * @param	array
	 * @param	int
	 * @return	void
	 */
	public function make_columns($array = array(), $col_limit = 0)
	{
		if ( ! is_array($array) OR count($array) === 0 OR ! is_int($col_limit))
		{
			return FALSE;
		}

		// Turn off the auto-heading feature since it's doubtful we
		// will want headings from a one-dimensional array
		$this->auto_heading = FALSE;

		if ($col_limit === 0)
		{
			return $array;
		}

		$new = array();
		do
		{
			$temp = array_splice($array, 0, $col_limit);

			if (count($temp) < $col_limit)
			{
				for ($i = count($temp); $i < $col_limit; $i++)
				{
					$temp[] = '&nbsp;';
				}
			}

			$new[] = $temp;
		}
		while (count($array) > 0);

		return $new;
	}

	// --------------------------------------------------------------------

	/**
	 * Set "empty" cells
	 *
	 * Can be passed as an array or discreet params
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function set_empty($value)
	{
		$this->empty_cells = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a table row
	 *
	 * Can be passed as an array or discreet params
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function add_row($args = array())
	{
		$args = func_get_args();
		$this->rows[] = $this->_prep_args($args);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Args
	 *
	 * Ensures a standard associative array format for all cell data
	 *
	 * @param	array
	 * @return	array
	 */
	protected function _prep_args($args)
	{
		// If there is no $args[0], skip this and treat as an associative array
		// This can happen if there is only a single key, for example this is passed to table->generate
		// array(array('foo'=>'bar'))
		if (isset($args[0]) && count($args) === 1 && is_array($args[0]))
		{
			// args sent as indexed array
			if ( ! isset($args[0]['data']))
			{
				foreach ($args[0] as $key => $val)
				{
					$args[$key] = (is_array($val) && isset($val['data'])) ? $val : array('data' => $val);
				}
			}
		}
		else
		{
			foreach ($args as $key => $val)
			{
				if ( ! is_array($val))
				{
					$args[$key] = array('data' => $val);
				}
			}
		}

		return $args;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a table caption
	 *
	 * @param	string
	 * @return	void
	 */
	public function set_caption($caption)
	{
		$this->caption = $caption;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the table
	 *
	 * @param	mixed
	 * @return	string
	 */
	public function generate($table_data = NULL)
	{
		// The table data can optionally be passed to this function
		// either as a database result object or an array
		if ($table_data !== NULL)
		{
			if (is_object($table_data))
			{
				$this->_set_from_object($table_data);
			}
			elseif (is_array($table_data))
			{
				$set_heading = (count($this->heading) !== 0 OR $this->auto_heading !== FALSE);
				$this->_set_from_array($table_data, $set_heading);
			}
		}

		// Is there anything to display? No? Smite them!
		if (count($this->heading) === 0 && count($this->rows) === 0)
		{
			return 'Undefined table data';
		}

		// Compile and validate the template date
		$this->_compile_template();

		// set a custom cell manipulation function to a locally scoped variable so its callable
		$function = $this->function;

		// Build the table!

		$out = $this->template['table_open'].$this->newline;

		// Add any caption here
		if ($this->caption)
		{
			$out .= $this->newline.'<caption>'.$this->caption.'</caption>'.$this->newline;
		}

		// Is there a table heading to display?
		if (count($this->heading) > 0)
		{
			$out .= $this->template['thead_open'].$this->newline.$this->template['heading_row_start'].$this->newline;

			foreach ($this->heading as $heading)
			{
				$temp = $this->template['heading_cell_start'];

				foreach ($heading as $key => $val)
				{
					if ($key !== 'data')
					{
						$temp = str_replace('<th', '<th '.$key.'="'.$val.'"', $temp);
					}
				}

				$out .= $temp.(isset($heading['data']) ? $heading['data'] : '').$this->template['heading_cell_end'];
			}

			$out .= $this->template['heading_row_end'].$this->newline.$this->template['thead_close'].$this->newline;
		}

		// Build the table rows
		if (count($this->rows) > 0)
		{
			$out .= $this->template['tbody_open'].$this->newline;

			$i = 1;
			foreach ($this->rows as $row)
			{
				if ( ! is_array($row))
				{
					break;
				}

				// We use modulus to alternate the row colors
				$name = fmod($i++, 2) ? '' : 'alt_';

				$out .= $this->template['row_'.$name.'start'].$this->newline;

				foreach ($row as $cell)
				{
					$temp = $this->template['cell_'.$name.'start'];

					foreach ($cell as $key => $val)
					{
						if ($key !== 'data')
						{
							$temp = str_replace('<td', '<td '.$key.'="'.$val.'"', $temp);
						}
					}

					$cell = isset($cell['data']) ? $cell['data'] : '';
					$out .= $temp;

					if ($cell === '' OR $cell === NULL)
					{
						$out .= $this->empty_cells;
					}
					elseif ($function !== FALSE && is_callable($function))
					{
						$out .= call_user_func($function, $cell);
					}
					else
					{
						$out .= $cell;
					}

					$out .= $this->template['cell_'.$name.'end'];
				}

				$out .= $this->template['row_'.$name.'end'].$this->newline;
			}

			$out .= $this->template['tbody_close'].$this->newline;
		}

		$out .= $this->template['table_close'];

		// Clear table class properties before generating the table
		$this->clear();

		return $out;
	}

	// --------------------------------------------------------------------

	/**
	 * Clears the table arrays.  Useful if multiple tables are being generated
	 *
	 * @return	void
	 */
	public function clear()
	{
		$this->rows		= array();
		$this->heading		= array();
		$this->auto_heading	= TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set table data from a database result object
	 *
	 * @param	object
	 * @return	void
	 */
	protected function _set_from_object($query)
	{
		if ( ! is_object($query))
		{
			return;
		}

		// First generate the headings from the table column names
		if (count($this->heading) === 0)
		{
			if ( ! is_callable(array($query, 'list_fields')))
			{
				return;
			}

			$this->heading = $this->_prep_args($query->list_fields());
		}

		// Next blast through the result array and build out the rows
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$this->rows[] = $this->_prep_args($row);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set table data from an array
	 *
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	protected function _set_from_array($data, $set_heading = TRUE)
	{
		if ( ! is_array($data) OR count($data) === 0)
		{
			return FALSE;
		}

		$i = 0;
		foreach ($data as $row)
		{
			// If a heading hasn't already been set we'll use the first row of the array as the heading
			if ($i++ === 0 && count($data) > 1 && count($this->heading) === 0 && $set_heading === TRUE)
			{
				$this->heading = $this->_prep_args($row);
			}
			else
			{
				$this->rows[] = $this->_prep_args($row);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Compile Template
	 *
	 * @return	void
	 */
	protected function _compile_template()
	{
		if ($this->template === NULL)
		{
			$this->template = $this->_default_template();
			return;
		}

		$this->temp = $this->_default_template();
		foreach (array('table_open', 'thead_open', 'thead_close', 'heading_row_start', 'heading_row_end', 'heading_cell_start', 'heading_cell_end', 'tbody_open', 'tbody_close', 'row_start', 'row_end', 'cell_start', 'cell_end', 'row_alt_start', 'row_alt_end', 'cell_alt_start', 'cell_alt_end', 'table_close') as $val)
		{
			if ( ! isset($this->template[$val]))
			{
				$this->template[$val] = $this->temp[$val];
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Default Template
	 *
	 * @return	void
	 */
	protected function _default_template()
	{
		return array(
				'table_open'		=> '<table border="0" cellpadding="4" cellspacing="0">',

				'thead_open'		=> '<thead>',
				'thead_close'		=> '</thead>',

				'heading_row_start'	=> '<tr>',
				'heading_row_end'	=> '</tr>',
				'heading_cell_start'	=> '<th>',
				'heading_cell_end'	=> '</th>',

				'tbody_open'		=> '<tbody>',
				'tbody_close'		=> '</tbody>',

				'row_start'		=> '<tr>',
				'row_end'		=> '</tr>',
				'cell_start'		=> '<td>',
				'cell_end'		=> '</td>',

				'row_alt_start'		=> '<tr>',
				'row_alt_end'		=> '</tr>',
				'cell_alt_start'	=> '<td>',
				'cell_alt_end'		=> '</td>',

				'table_close'		=> '</table>'
			);
	}

}

/* End of file Table.php */
/* Location: ./system/libraries/Table.php */