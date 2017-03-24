<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Query Builder Class
 *
 * This is the platform-independent base Query Builder implementation class.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */

abstract class CI_DB_query_builder extends CI_DB_driver {

	/**
	 * Return DELETE SQL flag
	 *
	 * @var	bool
	 */
	protected $return_delete_sql		= FALSE;

	/**
	 * Reset DELETE data flag
	 *
	 * @var	bool
	 */
	protected $reset_delete_data		= FALSE;

	/**
	 * QB SELECT data
	 *
	 * @var	array
	 */
	protected $qb_select			= array();

	/**
	 * QB DISTINCT flag
	 *
	 * @var	bool
	 */
	protected $qb_distinct			= FALSE;

	/**
	 * QB FROM data
	 *
	 * @var	array
	 */
	protected $qb_from			= array();

	/**
	 * QB JOIN data
	 *
	 * @var	array
	 */
	protected $qb_join			= array();

	/**
	 * QB WHERE data
	 *
	 * @var	array
	 */
	protected $qb_where			= array();

	/**
	 * QB GROUP BY data
	 *
	 * @var	array
	 */
	protected $qb_groupby			= array();

	/**
	 * QB HAVING data
	 *
	 * @var	array
	 */
	protected $qb_having			= array();

	/**
	 * QB keys
	 *
	 * @var	array
	 */
	protected $qb_keys			= array();

	/**
	 * QB LIMIT data
	 *
	 * @var	int
	 */
	protected $qb_limit			= FALSE;

	/**
	 * QB OFFSET data
	 *
	 * @var	int
	 */
	protected $qb_offset			= FALSE;

	/**
	 * QB ORDER BY data
	 *
	 * @var	array
	 */
	protected $qb_orderby			= array();

	/**
	 * QB data sets
	 *
	 * @var	array
	 */
	protected $qb_set			= array();

	/**
	 * QB data set for update_batch()
	 *
	 * @var	array
	 */
	protected $qb_set_ub			= array();

	/**
	 * QB aliased tables list
	 *
	 * @var	array
	 */
	protected $qb_aliased_tables		= array();

	/**
	 * QB WHERE group started flag
	 *
	 * @var	bool
	 */
	protected $qb_where_group_started	= FALSE;

	/**
	 * QB WHERE group count
	 *
	 * @var	int
	 */
	protected $qb_where_group_count		= 0;

	// Query Builder Caching variables

	/**
	 * QB Caching flag
	 *
	 * @var	bool
	 */
	protected $qb_caching				= FALSE;

	/**
	 * QB Cache exists list
	 *
	 * @var	array
	 */
	protected $qb_cache_exists			= array();

	/**
	 * QB Cache SELECT data
	 *
	 * @var	array
	 */
	protected $qb_cache_select			= array();

	/**
	 * QB Cache FROM data
	 *
	 * @var	array
	 */
	protected $qb_cache_from			= array();

	/**
	 * QB Cache JOIN data
	 *
	 * @var	array
	 */
	protected $qb_cache_join			= array();

	/**
	 * QB Cache aliased tables list
	 *
	 * @var	array
	 */
	protected $qb_cache_aliased_tables			= array();

	/**
	 * QB Cache WHERE data
	 *
	 * @var	array
	 */
	protected $qb_cache_where			= array();

	/**
	 * QB Cache GROUP BY data
	 *
	 * @var	array
	 */
	protected $qb_cache_groupby			= array();

	/**
	 * QB Cache HAVING data
	 *
	 * @var	array
	 */
	protected $qb_cache_having			= array();

	/**
	 * QB Cache ORDER BY data
	 *
	 * @var	array
	 */
	protected $qb_cache_orderby			= array();

	/**
	 * QB Cache data sets
	 *
	 * @var	array
	 */
	protected $qb_cache_set				= array();

	/**
	 * QB No Escape data
	 *
	 * @var	array
	 */
	protected $qb_no_escape 			= array();

	/**
	 * QB Cache No Escape data
	 *
	 * @var	array
	 */
	protected $qb_cache_no_escape			= array();

	// --------------------------------------------------------------------

	/**
	 * Select
	 *
	 * Generates the SELECT portion of the query
	 *
	 * @param	string
	 * @param	mixed
	 * @return	CI_DB_query_builder
	 */
	public function select($select = '*', $escape = NULL)
	{
		if (is_string($select))
		{
			$select = explode(',', $select);
		}

		// If the escape value was not set, we will base it on the global setting
		is_bool($escape) OR $escape = $this->_protect_identifiers;

		foreach ($select as $val)
		{
			$val = trim($val);

			if ($val !== '')
			{
				$this->qb_select[] = $val;
				$this->qb_no_escape[] = $escape;

				if ($this->qb_caching === TRUE)
				{
					$this->qb_cache_select[] = $val;
					$this->qb_cache_exists[] = 'select';
					$this->qb_cache_no_escape[] = $escape;
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Select Max
	 *
	 * Generates a SELECT MAX(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_max($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'MAX');
	}

	// --------------------------------------------------------------------

	/**
	 * Select Min
	 *
	 * Generates a SELECT MIN(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_min($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'MIN');
	}

	// --------------------------------------------------------------------

	/**
	 * Select Average
	 *
	 * Generates a SELECT AVG(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_avg($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'AVG');
	}

	// --------------------------------------------------------------------

	/**
	 * Select Sum
	 *
	 * Generates a SELECT SUM(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_sum($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'SUM');
	}

	// --------------------------------------------------------------------

	/**
	 * SELECT [MAX|MIN|AVG|SUM]()
	 *
	 * @used-by	select_max()
	 * @used-by	select_min()
	 * @used-by	select_avg()
	 * @used-by	select_sum()
	 *
	 * @param	string	$select	Field name
	 * @param	string	$alias
	 * @param	string	$type
	 * @return	CI_DB_query_builder
	 */
	protected function _max_min_avg_sum($select = '', $alias = '', $type = 'MAX')
	{
		if ( ! is_string($select) OR $select === '')
		{
			$this->display_error('db_invalid_query');
		}

		$type = strtoupper($type);

		if ( ! in_array($type, array('MAX', 'MIN', 'AVG', 'SUM')))
		{
			show_error('Invalid function type: '.$type);
		}

		if ($alias === '')
		{
			$alias = $this->_create_alias_from_table(trim($select));
		}

		$sql = $type.'('.$this->protect_identifiers(trim($select)).') AS '.$this->escape_identifiers(trim($alias));

		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Determines the alias name based on the table
	 *
	 * @param	string	$item
	 * @return	string
	 */
	protected function _create_alias_from_table($item)
	{
		if (strpos($item, '.') !== FALSE)
		{
			$item = explode('.', $item);
			return end($item);
		}

		return $item;
	}

	// --------------------------------------------------------------------

	/**
	 * DISTINCT
	 *
	 * Sets a flag which tells the query string compiler to add DISTINCT
	 *
	 * @param	bool	$val
	 * @return	CI_DB_query_builder
	 */
	public function distinct($val = TRUE)
	{
		$this->qb_distinct = is_bool($val) ? $val : TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * From
	 *
	 * Generates the FROM portion of the query
	 *
	 * @param	mixed	$from	can be a string or array
	 * @return	CI_DB_query_builder
	 */
	public function from($from)
	{
		foreach ((array) $from as $val)
		{
			if (strpos($val, ',') !== FALSE)
			{
				foreach (explode(',', $val) as $v)
				{
					$v = trim($v);
					$this->_track_aliases($v);

					$this->qb_from[] = $v = $this->protect_identifiers($v, TRUE, NULL, FALSE);

					if ($this->qb_caching === TRUE)
					{
						$this->qb_cache_from[] = $v;
						$this->qb_cache_exists[] = 'from';
					}
				}
			}
			else
			{
				$val = trim($val);

				// Extract any aliases that might exist. We use this information
				// in the protect_identifiers to know whether to add a table prefix
				$this->_track_aliases($val);

				$this->qb_from[] = $val = $this->protect_identifiers($val, TRUE, NULL, FALSE);

				if ($this->qb_caching === TRUE)
				{
					$this->qb_cache_from[] = $val;
					$this->qb_cache_exists[] = 'from';
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * JOIN
	 *
	 * Generates the JOIN portion of the query
	 *
	 * @param	string
	 * @param	string	the join condition
	 * @param	string	the type of join
	 * @param	string	whether not to try to escape identifiers
	 * @return	CI_DB_query_builder
	 */
	public function join($table, $cond, $type = '', $escape = NULL)
	{
		if ($type !== '')
		{
			$type = strtoupper(trim($type));

			if ( ! in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE))
			{
				$type = '';
			}
			else
			{
				$type .= ' ';
			}
		}

		// Extract any aliases that might exist. We use this information
		// in the protect_identifiers to know whether to add a table prefix
		$this->_track_aliases($table);

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		if ( ! $this->_has_operator($cond))
		{
			$cond = ' USING ('.($escape ? $this->escape_identifiers($cond) : $cond).')';
		}
		elseif ($escape === FALSE)
		{
			$cond = ' ON '.$cond;
		}
		else
		{
			// Split multiple conditions
			if (preg_match_all('/\sAND\s|\sOR\s/i', $cond, $joints, PREG_OFFSET_CAPTURE))
			{
				$conditions = array();
				$joints = $joints[0];
				array_unshift($joints, array('', 0));

				for ($i = count($joints) - 1, $pos = strlen($cond); $i >= 0; $i--)
				{
					$joints[$i][1] += strlen($joints[$i][0]); // offset
					$conditions[$i] = substr($cond, $joints[$i][1], $pos - $joints[$i][1]);
					$pos = $joints[$i][1] - strlen($joints[$i][0]);
					$joints[$i] = $joints[$i][0];
				}
			}
			else
			{
				$conditions = array($cond);
				$joints = array('');
			}

			$cond = ' ON ';
			for ($i = 0, $c = count($conditions); $i < $c; $i++)
			{
				$operator = $this->_get_operator($conditions[$i]);
				$cond .= $joints[$i];
				$cond .= preg_match("/(\(*)?([\[\]\w\.'-]+)".preg_quote($operator)."(.*)/i", $conditions[$i], $match)
					? $match[1].$this->protect_identifiers($match[2]).$operator.$this->protect_identifiers($match[3])
					: $conditions[$i];
			}
		}

		// Do we want to escape the table name?
		if ($escape === TRUE)
		{
			$table = $this->protect_identifiers($table, TRUE, NULL, FALSE);
		}

		// Assemble the JOIN statement
		$this->qb_join[] = $join = $type.'JOIN '.$table.$cond;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_join[] = $join;
			$this->qb_cache_exists[] = 'join';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE
	 *
	 * Generates the WHERE portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed
	 * @param	mixed
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function where($key, $value = NULL, $escape = NULL)
	{
		return $this->_wh('qb_where', $key, $value, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR WHERE
	 *
	 * Generates the WHERE portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed
	 * @param	mixed
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function or_where($key, $value = NULL, $escape = NULL)
	{
		return $this->_wh('qb_where', $key, $value, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE, HAVING
	 *
	 * @used-by	where()
	 * @used-by	or_where()
	 * @used-by	having()
	 * @used-by	or_having()
	 *
	 * @param	string	$qb_key	'qb_where' or 'qb_having'
	 * @param	mixed	$key
	 * @param	mixed	$value
	 * @param	string	$type
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	protected function _wh($qb_key, $key, $value = NULL, $type = 'AND ', $escape = NULL)
	{
		$qb_cache_key = ($qb_key === 'qb_having') ? 'qb_cache_having' : 'qb_cache_where';

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		// If the escape value was not set will base it on the global setting
		is_bool($escape) OR $escape = $this->_protect_identifiers;

		foreach ($key as $k => $v)
		{
			$prefix = (count($this->$qb_key) === 0 && count($this->$qb_cache_key) === 0)
				? $this->_group_get_type('')
				: $this->_group_get_type($type);

			if ($v !== NULL)
			{
				if ($escape === TRUE)
				{
					$v = ' '.$this->escape($v);
				}

				if ( ! $this->_has_operator($k))
				{
					$k .= ' = ';
				}
			}
			elseif ( ! $this->_has_operator($k))
			{
				// value appears not to have been set, assign the test to IS NULL
				$k .= ' IS NULL';
			}
			elseif (preg_match('/\s*(!?=|<>|\sIS(?:\s+NOT)?\s)\s*$/i', $k, $match, PREG_OFFSET_CAPTURE))
			{
				$k = substr($k, 0, $match[0][1]).($match[1][0] === '=' ? ' IS NULL' : ' IS NOT NULL');
			}

			$this->{$qb_key}[] = array('condition' => $prefix.$k.$v, 'escape' => $escape);
			if ($this->qb_caching === TRUE)
			{
				$this->{$qb_cache_key}[] = array('condition' => $prefix.$k.$v, 'escape' => $escape);
				$this->qb_cache_exists[] = substr($qb_key, 3);
			}

		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE IN
	 *
	 * Generates a WHERE field IN('item', 'item') SQL query,
	 * joined with 'AND' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function where_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_where', $key, $values, FALSE, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR WHERE IN
	 *
	 * Generates a WHERE field IN('item', 'item') SQL query,
	 * joined with 'OR' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_where_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_where', $key, $values, FALSE, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE NOT IN
	 *
	 * Generates a WHERE field NOT IN('item', 'item') SQL query,
	 * joined with 'AND' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function where_not_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_where', $key, $values, TRUE, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR WHERE NOT IN
	 *
	 * Generates a WHERE field NOT IN('item', 'item') SQL query,
	 * joined with 'OR' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_where_not_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_where', $key, $values, TRUE, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * HAVING IN
	 *
	 * Generates a HAVING field IN('item', 'item') SQL query,
	 * joined with 'AND' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function having_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_having', $key, $values, FALSE, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR HAVING IN
	 *
	 * Generates a HAVING field IN('item', 'item') SQL query,
	 * joined with 'OR' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_having_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_having', $key, $values, FALSE, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * HAVING NOT IN
	 *
	 * Generates a HAVING field NOT IN('item', 'item') SQL query,
	 * joined with 'AND' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function having_not_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_having', $key, $values, TRUE, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR HAVING NOT IN
	 *
	 * Generates a HAVING field NOT IN('item', 'item') SQL query,
	 * joined with 'OR' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_having_not_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_wh_in('qb_having', $key, $values, TRUE, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal WHERE/HAVING IN
	 *
	 * @used-by	where_in()
	 * @used-by	or_where_in()
	 * @used-by	where_not_in()
	 * @used-by	or_where_not_in()
	 * @used-by	having_in()
	 * @used-by	or_having_in()
	 * @used-by	having_not_in()
	 * @used-by	or_having_not_in()
	 *
	 * @param	string	$qb_key	'qb_where' or 'qb_having'
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$not	If the statement would be IN or NOT IN
	 * @param	string	$type
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	protected function _wh_in($qb_key, $key = NULL, $values = NULL, $not = FALSE, $type = 'AND ', $escape = NULL)
	{
		$qb_cache_key = ($qb_key === 'qb_having') ? 'qb_cache_having' : 'qb_cache_where';

		if ($key === NULL OR $values === NULL)
		{
			return $this;
		}

		if ( ! is_array($values))
		{
			$values = array($values);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		$not = ($not) ? ' NOT' : '';

		if ($escape === TRUE)
		{
			$wh_in = array();
			foreach ($values as $value)
			{
				$wh_in[] = $this->escape($value);
			}
		}
		else
		{
			$wh_in = array_values($values);
		}

		$prefix = (count($this->$qb_key) === 0 && count($this->$qb_cache_key) === 0)
			? $this->_group_get_type('')
			: $this->_group_get_type($type);

		$wh_in = array(
			'condition' => $prefix.$key.$not.' IN('.implode(', ', $wh_in).')',
			'escape' => $escape
		);

		$this->{$qb_key}[] = $wh_in;
		if ($this->qb_caching === TRUE)
		{
			$this->{$qb_cache_key}[] = $wh_in;
			$this->qb_cache_exists[] = substr($qb_key, 3);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * LIKE
	 *
	 * Generates a %LIKE% portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function like($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'AND ', $side, '', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * NOT LIKE
	 *
	 * Generates a NOT LIKE portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function not_like($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'AND ', $side, 'NOT', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR LIKE
	 *
	 * Generates a %LIKE% portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_like($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'OR ', $side, '', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR NOT LIKE
	 *
	 * Generates a NOT LIKE portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_not_like($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'OR ', $side, 'NOT', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal LIKE
	 *
	 * @used-by	like()
	 * @used-by	or_like()
	 * @used-by	not_like()
	 * @used-by	or_not_like()
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$type
	 * @param	string	$side
	 * @param	string	$not
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	protected function _like($field, $match = '', $type = 'AND ', $side = 'both', $not = '', $escape = NULL)
	{
		if ( ! is_array($field))
		{
			$field = array($field => $match);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;
		// lowercase $side in case somebody writes e.g. 'BEFORE' instead of 'before' (doh)
		$side = strtolower($side);

		foreach ($field as $k => $v)
		{
			$prefix = (count($this->qb_where) === 0 && count($this->qb_cache_where) === 0)
				? $this->_group_get_type('') : $this->_group_get_type($type);

			if ($escape === TRUE)
			{
				$v = $this->escape_like_str($v);
			}

			if ($side === 'none')
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '{$v}'";
			}
			elseif ($side === 'before')
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '%{$v}'";
			}
			elseif ($side === 'after')
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '{$v}%'";
			}
			else
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '%{$v}%'";
			}

			// some platforms require an escape sequence definition for LIKE wildcards
			if ($escape === TRUE && $this->_like_escape_str !== '')
			{
				$like_statement .= sprintf($this->_like_escape_str, $this->_like_escape_chr);
			}

			$this->qb_where[] = array('condition' => $like_statement, 'escape' => $escape);
			if ($this->qb_caching === TRUE)
			{
				$this->qb_cache_where[] = array('condition' => $like_statement, 'escape' => $escape);
				$this->qb_cache_exists[] = 'where';
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group.
	 *
	 * @param	string	$not	(Internal use only)
	 * @param	string	$type	(Internal use only)
	 * @return	CI_DB_query_builder
	 */
	public function group_start($not = '', $type = 'AND ')
	{
		$type = $this->_group_get_type($type);

		$this->qb_where_group_started = TRUE;
		$prefix = (count($this->qb_where) === 0 && count($this->qb_cache_where) === 0) ? '' : $type;
		$where = array(
			'condition' => $prefix.$not.str_repeat(' ', ++$this->qb_where_group_count).' (',
			'escape' => FALSE
		);

		$this->qb_where[] = $where;
		if ($this->qb_caching)
		{
			$this->qb_cache_where[] = $where;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group, but ORs the group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function or_group_start()
	{
		return $this->group_start('', 'OR ');
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group, but NOTs the group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function not_group_start()
	{
		return $this->group_start('NOT ', 'AND ');
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group, but OR NOTs the group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function or_not_group_start()
	{
		return $this->group_start('NOT ', 'OR ');
	}

	// --------------------------------------------------------------------

	/**
	 * Ends a query group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function group_end()
	{
		$this->qb_where_group_started = FALSE;
		$where = array(
			'condition' => str_repeat(' ', $this->qb_where_group_count--).')',
			'escape' => FALSE
		);

		$this->qb_where[] = $where;
		if ($this->qb_caching)
		{
			$this->qb_cache_where[] = $where;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Group_get_type
	 *
	 * @used-by	group_start()
	 * @used-by	_like()
	 * @used-by	_wh()
	 * @used-by	_where_in()
	 *
	 * @param	string	$type
	 * @return	string
	 */
	protected function _group_get_type($type)
	{
		if ($this->qb_where_group_started)
		{
			$type = '';
			$this->qb_where_group_started = FALSE;
		}

		return $type;
	}

	// --------------------------------------------------------------------

	/**
	 * GROUP BY
	 *
	 * @param	string	$by
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function group_by($by, $escape = NULL)
	{
		is_bool($escape) OR $escape = $this->_protect_identifiers;

		if (is_string($by))
		{
			$by = ($escape === TRUE)
				? explode(',', $by)
				: array($by);
		}

		foreach ($by as $val)
		{
			$val = trim($val);

			if ($val !== '')
			{
				$val = array('field' => $val, 'escape' => $escape);

				$this->qb_groupby[] = $val;
				if ($this->qb_caching === TRUE)
				{
					$this->qb_cache_groupby[] = $val;
					$this->qb_cache_exists[] = 'groupby';
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * HAVING
	 *
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	string	$key
	 * @param	string	$value
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function having($key, $value = NULL, $escape = NULL)
	{
		return $this->_wh('qb_having', $key, $value, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR HAVING
	 *
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	string	$key
	 * @param	string	$value
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_having($key, $value = NULL, $escape = NULL)
	{
		return $this->_wh('qb_having', $key, $value, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * ORDER BY
	 *
	 * @param	string	$orderby
	 * @param	string	$direction	ASC, DESC or RANDOM
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function order_by($orderby, $direction = '', $escape = NULL)
	{
		$direction = strtoupper(trim($direction));

		if ($direction === 'RANDOM')
		{
			$direction = '';

			// Do we have a seed value?
			$orderby = ctype_digit((string) $orderby)
				? sprintf($this->_random_keyword[1], $orderby)
				: $this->_random_keyword[0];
		}
		elseif (empty($orderby))
		{
			return $this;
		}
		elseif ($direction !== '')
		{
			$direction = in_array($direction, array('ASC', 'DESC'), TRUE) ? ' '.$direction : '';
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		if ($escape === FALSE)
		{
			$qb_orderby[] = array('field' => $orderby, 'direction' => $direction, 'escape' => FALSE);
		}
		else
		{
			$qb_orderby = array();
			foreach (explode(',', $orderby) as $field)
			{
				$qb_orderby[] = ($direction === '' && preg_match('/\s+(ASC|DESC)$/i', rtrim($field), $match, PREG_OFFSET_CAPTURE))
					? array('field' => ltrim(substr($field, 0, $match[0][1])), 'direction' => ' '.$match[1][0], 'escape' => TRUE)
					: array('field' => trim($field), 'direction' => $direction, 'escape' => TRUE);
			}
		}

		$this->qb_orderby = array_merge($this->qb_orderby, $qb_orderby);
		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_orderby = array_merge($this->qb_cache_orderby, $qb_orderby);
			$this->qb_cache_exists[] = 'orderby';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * LIMIT
	 *
	 * @param	int	$value	LIMIT value
	 * @param	int	$offset	OFFSET value
	 * @return	CI_DB_query_builder
	 */
	public function limit($value, $offset = 0)
	{
		is_null($value) OR $this->qb_limit = (int) $value;
		empty($offset) OR $this->qb_offset = (int) $offset;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the OFFSET value
	 *
	 * @param	int	$offset	OFFSET value
	 * @return	CI_DB_query_builder
	 */
	public function offset($offset)
	{
		empty($offset) OR $this->qb_offset = (int) $offset;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * LIMIT string
	 *
	 * Generates a platform-specific LIMIT clause.
	 *
	 * @param	string	$sql	SQL Query
	 * @return	string
	 */
	protected function _limit($sql)
	{
		return $sql.' LIMIT '.($this->qb_offset ? $this->qb_offset.', ' : '').(int) $this->qb_limit;
	}

	// --------------------------------------------------------------------

	/**
	 * The "set" function.
	 *
	 * Allows key/value pairs to be set for inserting or updating
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function set($key, $value = '', $escape = NULL)
	{
		$key = $this->_object_to_array($key);

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		foreach ($key as $k => $v)
		{
			$this->qb_set[$this->protect_identifiers($k, FALSE, $escape)] = ($escape)
				? $this->escape($v) : $v;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get SELECT query string
	 *
	 * Compiles a SELECT query string and returns the sql.
	 *
	 * @param	string	the table name to select from (optional)
	 * @param	bool	TRUE: resets QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function get_compiled_select($table = '', $reset = TRUE)
	{
		if ($table !== '')
		{
			$this->_track_aliases($table);
			$this->from($table);
		}

		$select = $this->_compile_select();

		if ($reset === TRUE)
		{
			$this->_reset_select();
		}

		return $select;
	}

	// --------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Compiles the select statement based on the other functions called
	 * and runs the query
	 *
	 * @param	string	the table
	 * @param	string	the limit clause
	 * @param	string	the offset clause
	 * @return	CI_DB_result
	 */
	public function get($table = '', $limit = NULL, $offset = NULL)
	{
		if ($table !== '')
		{
			$this->_track_aliases($table);
			$this->from($table);
		}

		if ( ! empty($limit))
		{
			$this->limit($limit, $offset);
		}

		$result = $this->query($this->_compile_select());
		$this->_reset_select();
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All Results" query
	 *
	 * Generates a platform-specific query string that counts all records
	 * returned by an Query Builder query.
	 *
	 * @param	string
	 * @param	bool	the reset clause
	 * @return	int
	 */
	public function count_all_results($table = '', $reset = TRUE)
	{
		if ($table !== '')
		{
			$this->_track_aliases($table);
			$this->from($table);
		}

		// ORDER BY usage is often problematic here (most notably
		// on Microsoft SQL Server) and ultimately unnecessary
		// for selecting COUNT(*) ...
		if ( ! empty($this->qb_orderby))
		{
			$orderby = $this->qb_orderby;
			$this->qb_orderby = NULL;
		}

		$result = ($this->qb_distinct === TRUE OR ! empty($this->qb_groupby) OR ! empty($this->qb_cache_groupby))
			? $this->query($this->_count_string.$this->protect_identifiers('numrows')."\nFROM (\n".$this->_compile_select()."\n) CI_count_all_results")
			: $this->query($this->_compile_select($this->_count_string.$this->protect_identifiers('numrows')));

		if ($reset === TRUE)
		{
			$this->_reset_select();
		}
		// If we've previously reset the qb_orderby values, get them back
		elseif ( ! isset($this->qb_orderby))
		{
			$this->qb_orderby = $orderby;
		}

		if ($result->num_rows() === 0)
		{
			return 0;
		}

		$row = $result->row();
		return (int) $row->numrows;
	}

	// --------------------------------------------------------------------

	/**
	 * Get_Where
	 *
	 * Allows the where clause, limit and offset to be added directly
	 *
	 * @param	string	$table
	 * @param	string	$where
	 * @param	int	$limit
	 * @param	int	$offset
	 * @return	CI_DB_result
	 */
	public function get_where($table = '', $where = NULL, $limit = NULL, $offset = NULL)
	{
		if ($table !== '')
		{
			$this->from($table);
		}

		if ($where !== NULL)
		{
			$this->where($where);
		}

		if ( ! empty($limit))
		{
			$this->limit($limit, $offset);
		}

		$result = $this->query($this->_compile_select());
		$this->_reset_select();
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert_Batch
	 *
	 * Compiles batch insert strings and runs the queries
	 *
	 * @param	string	$table	Table to insert into
	 * @param	array	$set 	An associative array of insert values
	 * @param	bool	$escape	Whether to escape values and identifiers
	 * @return	int	Number of rows inserted or FALSE on failure
	 */
	public function insert_batch($table, $set = NULL, $escape = NULL, $batch_size = 100)
	{
		if ($set === NULL)
		{
			if (empty($this->qb_set))
			{
				return ($this->db_debug) ? $this->display_error('db_must_use_set') : FALSE;
			}
		}
		else
		{
			if (empty($set))
			{
				return ($this->db_debug) ? $this->display_error('insert_batch() called with no data') : FALSE;
			}

			$this->set_insert_batch($set, '', $escape);
		}

		if (strlen($table) === 0)
		{
			if ( ! isset($this->qb_from[0]))
			{
				return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
			}

			$table = $this->qb_from[0];
		}

		// Batch this baby
		$affected_rows = 0;
		for ($i = 0, $total = count($this->qb_set); $i < $total; $i += $batch_size)
		{
			if ($this->query($this->_insert_batch($this->protect_identifiers($table, TRUE, $escape, FALSE), $this->qb_keys, array_slice($this->qb_set, $i, $batch_size))))
			{
				$affected_rows += $this->affected_rows();
			}
		}

		$this->_reset_write();
		return $affected_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data.
	 *
	 * @param	string	$table	Table name
	 * @param	array	$keys	INSERT keys
	 * @param	array	$values	INSERT values
	 * @return	string
	 */
	protected function _insert_batch($table, $keys, $values)
	{
		return 'INSERT INTO '.$table.' ('.implode(', ', $keys).') VALUES '.implode(', ', $values);
	}

	// --------------------------------------------------------------------

	/**
	 * The "set_insert_batch" function.  Allows key/value pairs to be set for batch inserts
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function set_insert_batch($key, $value = '', $escape = NULL)
	{
		$key = $this->_object_to_array_batch($key);

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		$keys = array_keys($this->_object_to_array(reset($key)));
		sort($keys);

		foreach ($key as $row)
		{
			$row = $this->_object_to_array($row);
			if (count(array_diff($keys, array_keys($row))) > 0 OR count(array_diff(array_keys($row), $keys)) > 0)
			{
				// batch function above returns an error on an empty array
				$this->qb_set[] = array();
				return;
			}

			ksort($row); // puts $row in the same order as our keys

			if ($escape !== FALSE)
			{
				$clean = array();
				foreach ($row as $value)
				{
					$clean[] = $this->escape($value);
				}

				$row = $clean;
			}

			$this->qb_set[] = '('.implode(',', $row).')';
		}

		foreach ($keys as $k)
		{
			$this->qb_keys[] = $this->protect_identifiers($k, FALSE, $escape);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get INSERT query string
	 *
	 * Compiles an insert query and returns the sql
	 *
	 * @param	string	the table to insert into
	 * @param	bool	TRUE: reset QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function get_compiled_insert($table = '', $reset = TRUE)
	{
		if ($this->_validate_insert($table) === FALSE)
		{
			return FALSE;
		}

		$sql = $this->_insert(
			$this->protect_identifiers(
				$this->qb_from[0], TRUE, NULL, FALSE
			),
			array_keys($this->qb_set),
			array_values($this->qb_set)
		);

		if ($reset === TRUE)
		{
			$this->_reset_write();
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert
	 *
	 * Compiles an insert string and runs the query
	 *
	 * @param	string	the table to insert data into
	 * @param	array	an associative array of insert values
	 * @param	bool	$escape	Whether to escape values and identifiers
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function insert($table = '', $set = NULL, $escape = NULL)
	{
		if ($set !== NULL)
		{
			$this->set($set, '', $escape);
		}

		if ($this->_validate_insert($table) === FALSE)
		{
			return FALSE;
		}

		$sql = $this->_insert(
			$this->protect_identifiers(
				$this->qb_from[0], TRUE, $escape, FALSE
			),
			array_keys($this->qb_set),
			array_values($this->qb_set)
		);

		$this->_reset_write();
		return $this->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Insert
	 *
	 * This method is used by both insert() and get_compiled_insert() to
	 * validate that the there data is actually being set and that table
	 * has been chosen to be inserted into.
	 *
	 * @param	string	the table to insert data into
	 * @return	string
	 */
	protected function _validate_insert($table = '')
	{
		if (count($this->qb_set) === 0)
		{
			return ($this->db_debug) ? $this->display_error('db_must_use_set') : FALSE;
		}

		if ($table !== '')
		{
			$this->qb_from[0] = $table;
		}
		elseif ( ! isset($this->qb_from[0]))
		{
			return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Replace
	 *
	 * Compiles an replace into string and runs the query
	 *
	 * @param	string	the table to replace data into
	 * @param	array	an associative array of insert values
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function replace($table = '', $set = NULL)
	{
		if ($set !== NULL)
		{
			$this->set($set);
		}

		if (count($this->qb_set) === 0)
		{
			return ($this->db_debug) ? $this->display_error('db_must_use_set') : FALSE;
		}

		if ($table === '')
		{
			if ( ! isset($this->qb_from[0]))
			{
				return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
			}

			$table = $this->qb_from[0];
		}

		$sql = $this->_replace($this->protect_identifiers($table, TRUE, NULL, FALSE), array_keys($this->qb_set), array_values($this->qb_set));

		$this->_reset_write();
		return $this->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Replace statement
	 *
	 * Generates a platform-specific replace string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	protected function _replace($table, $keys, $values)
	{
		return 'REPLACE INTO '.$table.' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------

	/**
	 * FROM tables
	 *
	 * Groups tables in FROM clauses if needed, so there is no confusion
	 * about operator precedence.
	 *
	 * Note: This is only used (and overridden) by MySQL and CUBRID.
	 *
	 * @return	string
	 */
	protected function _from_tables()
	{
		return implode(', ', $this->qb_from);
	}

	// --------------------------------------------------------------------

	/**
	 * Get UPDATE query string
	 *
	 * Compiles an update query and returns the sql
	 *
	 * @param	string	the table to update
	 * @param	bool	TRUE: reset QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function get_compiled_update($table = '', $reset = TRUE)
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		if ($this->_validate_update($table) === FALSE)
		{
			return FALSE;
		}

		$sql = $this->_update($this->qb_from[0], $this->qb_set);

		if ($reset === TRUE)
		{
			$this->_reset_write();
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * UPDATE
	 *
	 * Compiles an update string and runs the query.
	 *
	 * @param	string	$table
	 * @param	array	$set	An associative array of update values
	 * @param	mixed	$where
	 * @param	int	$limit
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function update($table = '', $set = NULL, $where = NULL, $limit = NULL)
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		if ($set !== NULL)
		{
			$this->set($set);
		}

		if ($this->_validate_update($table) === FALSE)
		{
			return FALSE;
		}

		if ($where !== NULL)
		{
			$this->where($where);
		}

		if ( ! empty($limit))
		{
			$this->limit($limit);
		}

		$sql = $this->_update($this->qb_from[0], $this->qb_set);
		$this->_reset_write();
		return $this->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Update
	 *
	 * This method is used by both update() and get_compiled_update() to
	 * validate that data is actually being set and that a table has been
	 * chosen to be update.
	 *
	 * @param	string	the table to update data on
	 * @return	bool
	 */
	protected function _validate_update($table)
	{
		if (count($this->qb_set) === 0)
		{
			return ($this->db_debug) ? $this->display_error('db_must_use_set') : FALSE;
		}

		if ($table !== '')
		{
			$this->qb_from = array($this->protect_identifiers($table, TRUE, NULL, FALSE));
		}
		elseif ( ! isset($this->qb_from[0]))
		{
			return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update_Batch
	 *
	 * Compiles an update string and runs the query
	 *
	 * @param	string	the table to retrieve the results from
	 * @param	array	an associative array of update values
	 * @param	string	the where key
	 * @return	int	number of rows affected or FALSE on failure
	 */
	public function update_batch($table, $set = NULL, $index = NULL, $batch_size = 100)
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		if ($index === NULL)
		{
			return ($this->db_debug) ? $this->display_error('db_must_use_index') : FALSE;
		}

		if ($set === NULL)
		{
			if (empty($this->qb_set_ub))
			{
				return ($this->db_debug) ? $this->display_error('db_must_use_set') : FALSE;
			}
		}
		else
		{
			if (empty($set))
			{
				return ($this->db_debug) ? $this->display_error('update_batch() called with no data') : FALSE;
			}

			$this->set_update_batch($set, $index);
		}

		if (strlen($table) === 0)
		{
			if ( ! isset($this->qb_from[0]))
			{
				return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
			}

			$table = $this->qb_from[0];
		}

		// Batch this baby
		$affected_rows = 0;
		for ($i = 0, $total = count($this->qb_set_ub); $i < $total; $i += $batch_size)
		{
			if ($this->query($this->_update_batch($this->protect_identifiers($table, TRUE, NULL, FALSE), array_slice($this->qb_set_ub, $i, $batch_size), $index)))
			{
				$affected_rows += $this->affected_rows();
			}

			$this->qb_where = array();
		}

		$this->_reset_write();
		return $affected_rows;
	}

	// --------------------------------------------------------------------

	/**
	 * Update_Batch statement
	 *
	 * Generates a platform-specific batch update string from the supplied data
	 *
	 * @param	string	$table	Table name
	 * @param	array	$values	Update data
	 * @param	string	$index	WHERE key
	 * @return	string
	 */
	protected function _update_batch($table, $values, $index)
	{
		$ids = array();
		foreach ($values as $key => $val)
		{
			$ids[] = $val[$index]['value'];

			foreach (array_keys($val) as $field)
			{
				if ($field !== $index)
				{
					$final[$val[$field]['field']][] = 'WHEN '.$val[$index]['field'].' = '.$val[$index]['value'].' THEN '.$val[$field]['value'];
				}
			}
		}

		$cases = '';
		foreach ($final as $k => $v)
		{
			$cases .= $k." = CASE \n"
				.implode("\n", $v)."\n"
				.'ELSE '.$k.' END, ';
		}

		$this->where($val[$index]['field'].' IN('.implode(',', $ids).')', NULL, FALSE);

		return 'UPDATE '.$table.' SET '.substr($cases, 0, -2).$this->_compile_wh('qb_where');
	}

	// --------------------------------------------------------------------

	/**
	 * The "set_update_batch" function.  Allows key/value pairs to be set for batch updating
	 *
	 * @param	array
	 * @param	string
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function set_update_batch($key, $index = '', $escape = NULL)
	{
		$key = $this->_object_to_array_batch($key);

		if ( ! is_array($key))
		{
			// @todo error
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		foreach ($key as $k => $v)
		{
			$index_set = FALSE;
			$clean = array();
			foreach ($v as $k2 => $v2)
			{
				if ($k2 === $index)
				{
					$index_set = TRUE;
				}

				$clean[$k2] = array(
					'field'  => $this->protect_identifiers($k2, FALSE, $escape),
					'value'  => ($escape === FALSE ? $v2 : $this->escape($v2))
				);
			}

			if ($index_set === FALSE)
			{
				return $this->display_error('db_batch_missing_index');
			}

			$this->qb_set_ub[] = $clean;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Empty Table
	 *
	 * Compiles a delete string and runs "DELETE FROM table"
	 *
	 * @param	string	the table to empty
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function empty_table($table = '')
	{
		if ($table === '')
		{
			if ( ! isset($this->qb_from[0]))
			{
				return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
			}

			$table = $this->qb_from[0];
		}
		else
		{
			$table = $this->protect_identifiers($table, TRUE, NULL, FALSE);
		}

		$sql = $this->_delete($table);
		$this->_reset_write();
		return $this->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate
	 *
	 * Compiles a truncate string and runs the query
	 * If the database does not support the truncate() command
	 * This function maps to "DELETE FROM table"
	 *
	 * @param	string	the table to truncate
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function truncate($table = '')
	{
		if ($table === '')
		{
			if ( ! isset($this->qb_from[0]))
			{
				return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
			}

			$table = $this->qb_from[0];
		}
		else
		{
			$table = $this->protect_identifiers($table, TRUE, NULL, FALSE);
		}

		$sql = $this->_truncate($table);
		$this->_reset_write();
		return $this->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the truncate() command,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _truncate($table)
	{
		return 'TRUNCATE '.$table;
	}

	// --------------------------------------------------------------------

	/**
	 * Get DELETE query string
	 *
	 * Compiles a delete query string and returns the sql
	 *
	 * @param	string	the table to delete from
	 * @param	bool	TRUE: reset QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function get_compiled_delete($table = '', $reset = TRUE)
	{
		$this->return_delete_sql = TRUE;
		$sql = $this->delete($table, '', NULL, $reset);
		$this->return_delete_sql = FALSE;
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete
	 *
	 * Compiles a delete string and runs the query
	 *
	 * @param	mixed	the table(s) to delete from. String or array
	 * @param	mixed	the where clause
	 * @param	mixed	the limit clause
	 * @param	bool
	 * @return	mixed
	 */
	public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE)
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		if ($table === '')
		{
			if ( ! isset($this->qb_from[0]))
			{
				return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
			}

			$table = $this->qb_from[0];
		}
		elseif (is_array($table))
		{
			empty($where) && $reset_data = FALSE;

			foreach ($table as $single_table)
			{
				$this->delete($single_table, $where, $limit, $reset_data);
			}

			return;
		}
		else
		{
			$table = $this->protect_identifiers($table, TRUE, NULL, FALSE);
		}

		if ($where !== '')
		{
			$this->where($where);
		}

		if ( ! empty($limit))
		{
			$this->limit($limit);
		}

		if (count($this->qb_where) === 0)
		{
			return ($this->db_debug) ? $this->display_error('db_del_must_use_where') : FALSE;
		}

		$sql = $this->_delete($table);
		if ($reset_data)
		{
			$this->_reset_write();
		}

		return ($this->return_delete_sql === TRUE) ? $sql : $this->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _delete($table)
	{
		return 'DELETE FROM '.$table.$this->_compile_wh('qb_where')
			.($this->qb_limit ? ' LIMIT '.$this->qb_limit : '');
	}

	// --------------------------------------------------------------------

	/**
	 * DB Prefix
	 *
	 * Prepends a database prefix if one exists in configuration
	 *
	 * @param	string	the table
	 * @return	string
	 */
	public function dbprefix($table = '')
	{
		if ($table === '')
		{
			$this->display_error('db_table_name_required');
		}

		return $this->dbprefix.$table;
	}

	// --------------------------------------------------------------------

	/**
	 * Set DB Prefix
	 *
	 * Set's the DB Prefix to something new without needing to reconnect
	 *
	 * @param	string	the prefix
	 * @return	string
	 */
	public function set_dbprefix($prefix = '')
	{
		return $this->dbprefix = $prefix;
	}

	// --------------------------------------------------------------------

	/**
	 * Track Aliases
	 *
	 * Used to track SQL statements written with aliased tables.
	 *
	 * @param	string	The table to inspect
	 * @return	string
	 */
	protected function _track_aliases($table)
	{
		if (is_array($table))
		{
			foreach ($table as $t)
			{
				$this->_track_aliases($t);
			}
			return;
		}

		// Does the string contain a comma?  If so, we need to separate
		// the string into discreet statements
		if (strpos($table, ',') !== FALSE)
		{
			return $this->_track_aliases(explode(',', $table));
		}

		// if a table alias is used we can recognize it by a space
		if (strpos($table, ' ') !== FALSE)
		{
			// if the alias is written with the AS keyword, remove it
			$table = preg_replace('/\s+AS\s+/i', ' ', $table);

			// Grab the alias
			$table = trim(strrchr($table, ' '));

			// Store the alias, if it doesn't already exist
			if ( ! in_array($table, $this->qb_aliased_tables, TRUE))
			{
				$this->qb_aliased_tables[] = $table;
				if ($this->qb_caching === TRUE && ! in_array($table, $this->qb_cache_aliased_tables, TRUE))
				{
					$this->qb_cache_aliased_tables[] = $table;
					$this->qb_cache_exists[] = 'aliased_tables';
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Compile the SELECT statement
	 *
	 * Generates a query string based on which functions were used.
	 * Should not be called directly.
	 *
	 * @param	bool	$select_override
	 * @return	string
	 */
	protected function _compile_select($select_override = FALSE)
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		// Write the "select" portion of the query
		if ($select_override !== FALSE)
		{
			$sql = $select_override;
		}
		else
		{
			$sql = ( ! $this->qb_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

			if (count($this->qb_select) === 0)
			{
				$sql .= '*';
			}
			else
			{
				// Cycle through the "select" portion of the query and prep each column name.
				// The reason we protect identifiers here rather than in the select() function
				// is because until the user calls the from() function we don't know if there are aliases
				foreach ($this->qb_select as $key => $val)
				{
					$no_escape = isset($this->qb_no_escape[$key]) ? $this->qb_no_escape[$key] : NULL;
					$this->qb_select[$key] = $this->protect_identifiers($val, FALSE, $no_escape);
				}

				$sql .= implode(', ', $this->qb_select);
			}
		}

		// Write the "FROM" portion of the query
		if (count($this->qb_from) > 0)
		{
			$sql .= "\nFROM ".$this->_from_tables();
		}

		// Write the "JOIN" portion of the query
		if (count($this->qb_join) > 0)
		{
			$sql .= "\n".implode("\n", $this->qb_join);
		}

		$sql .= $this->_compile_wh('qb_where')
			.$this->_compile_group_by()
			.$this->_compile_wh('qb_having')
			.$this->_compile_order_by(); // ORDER BY

		// LIMIT
		if ($this->qb_limit OR $this->qb_offset)
		{
			return $this->_limit($sql."\n");
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile WHERE, HAVING statements
	 *
	 * Escapes identifiers in WHERE and HAVING statements at execution time.
	 *
	 * Required so that aliases are tracked properly, regardless of whether
	 * where(), or_where(), having(), or_having are called prior to from(),
	 * join() and dbprefix is added only if needed.
	 *
	 * @param	string	$qb_key	'qb_where' or 'qb_having'
	 * @return	string	SQL statement
	 */
	protected function _compile_wh($qb_key)
	{
		if (count($this->$qb_key) > 0)
		{
			for ($i = 0, $c = count($this->$qb_key); $i < $c; $i++)
			{
				// Is this condition already compiled?
				if (is_string($this->{$qb_key}[$i]))
				{
					continue;
				}
				elseif ($this->{$qb_key}[$i]['escape'] === FALSE)
				{
					$this->{$qb_key}[$i] = $this->{$qb_key}[$i]['condition'];
					continue;
				}

				// Split multiple conditions
				$conditions = preg_split(
					'/((?:^|\s+)AND\s+|(?:^|\s+)OR\s+)/i',
					$this->{$qb_key}[$i]['condition'],
					-1,
					PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
				);

				for ($ci = 0, $cc = count($conditions); $ci < $cc; $ci++)
				{
					if (($op = $this->_get_operator($conditions[$ci])) === FALSE
						OR ! preg_match('/^(\(?)(.*)('.preg_quote($op, '/').')\s*(.*(?<!\)))?(\)?)$/i', $conditions[$ci], $matches))
					{
						continue;
					}

					// $matches = array(
					//	0 => '(test <= foo)',	/* the whole thing */
					//	1 => '(',		/* optional */
					//	2 => 'test',		/* the field name */
					//	3 => ' <= ',		/* $op */
					//	4 => 'foo',		/* optional, if $op is e.g. 'IS NULL' */
					//	5 => ')'		/* optional */
					// );

					if ( ! empty($matches[4]))
					{
						$this->_is_literal($matches[4]) OR $matches[4] = $this->protect_identifiers(trim($matches[4]));
						$matches[4] = ' '.$matches[4];
					}

					$conditions[$ci] = $matches[1].$this->protect_identifiers(trim($matches[2]))
						.' '.trim($matches[3]).$matches[4].$matches[5];
				}

				$this->{$qb_key}[$i] = implode('', $conditions);
			}

			return ($qb_key === 'qb_having' ? "\nHAVING " : "\nWHERE ")
				.implode("\n", $this->$qb_key);
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Compile GROUP BY
	 *
	 * Escapes identifiers in GROUP BY statements at execution time.
	 *
	 * Required so that aliases are tracked properly, regardless of whether
	 * group_by() is called prior to from(), join() and dbprefix is added
	 * only if needed.
	 *
	 * @return	string	SQL statement
	 */
	protected function _compile_group_by()
	{
		if (count($this->qb_groupby) > 0)
		{
			for ($i = 0, $c = count($this->qb_groupby); $i < $c; $i++)
			{
				// Is it already compiled?
				if (is_string($this->qb_groupby[$i]))
				{
					continue;
				}

				$this->qb_groupby[$i] = ($this->qb_groupby[$i]['escape'] === FALSE OR $this->_is_literal($this->qb_groupby[$i]['field']))
					? $this->qb_groupby[$i]['field']
					: $this->protect_identifiers($this->qb_groupby[$i]['field']);
			}

			return "\nGROUP BY ".implode(', ', $this->qb_groupby);
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Compile ORDER BY
	 *
	 * Escapes identifiers in ORDER BY statements at execution time.
	 *
	 * Required so that aliases are tracked properly, regardless of whether
	 * order_by() is called prior to from(), join() and dbprefix is added
	 * only if needed.
	 *
	 * @return	string	SQL statement
	 */
	protected function _compile_order_by()
	{
		if (empty($this->qb_orderby))
		{
			return '';
		}

		for ($i = 0, $c = count($this->qb_orderby); $i < $c; $i++)
		{
			if (is_string($this->qb_orderby[$i]))
			{
				continue;
			}

			if ($this->qb_orderby[$i]['escape'] !== FALSE && ! $this->_is_literal($this->qb_orderby[$i]['field']))
			{
				$this->qb_orderby[$i]['field'] = $this->protect_identifiers($this->qb_orderby[$i]['field']);
			}

			$this->qb_orderby[$i] = $this->qb_orderby[$i]['field'].$this->qb_orderby[$i]['direction'];
		}

		return "\nORDER BY ".implode(', ', $this->qb_orderby);
	}

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	protected function _object_to_array($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		foreach (get_object_vars($object) as $key => $val)
		{
			// There are some built in keys we need to ignore for this conversion
			if ( ! is_object($val) && ! is_array($val) && $key !== '_parent_name')
			{
				$array[$key] = $val;
			}
		}

		return $array;
	}

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	protected function _object_to_array_batch($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		$out = get_object_vars($object);
		$fields = array_keys($out);

		foreach ($fields as $val)
		{
			// There are some built in keys we need to ignore for this conversion
			if ($val !== '_parent_name')
			{
				$i = 0;
				foreach ($out[$val] as $data)
				{
					$array[$i++][$val] = $data;
				}
			}
		}

		return $array;
	}

	// --------------------------------------------------------------------

	/**
	 * Start Cache
	 *
	 * Starts QB caching
	 *
	 * @return	CI_DB_query_builder
	 */
	public function start_cache()
	{
		$this->qb_caching = TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Stop Cache
	 *
	 * Stops QB caching
	 *
	 * @return	CI_DB_query_builder
	 */
	public function stop_cache()
	{
		$this->qb_caching = FALSE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Flush Cache
	 *
	 * Empties the QB cache
	 *
	 * @return	CI_DB_query_builder
	 */
	public function flush_cache()
	{
		$this->_reset_run(array(
			'qb_cache_select'		=> array(),
			'qb_cache_from'			=> array(),
			'qb_cache_join'			=> array(),
			'qb_cache_where'		=> array(),
			'qb_cache_groupby'		=> array(),
			'qb_cache_having'		=> array(),
			'qb_cache_orderby'		=> array(),
			'qb_cache_set'			=> array(),
			'qb_cache_exists'		=> array(),
			'qb_cache_no_escape'	=> array(),
			'qb_cache_aliased_tables'	=> array()
		));

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Merge Cache
	 *
	 * When called, this function merges any cached QB arrays with
	 * locally called ones.
	 *
	 * @return	void
	 */
	protected function _merge_cache()
	{
		if (count($this->qb_cache_exists) === 0)
		{
			return;
		}
		elseif (in_array('select', $this->qb_cache_exists, TRUE))
		{
			$qb_no_escape = $this->qb_cache_no_escape;
		}

		foreach (array_unique($this->qb_cache_exists) as $val) // select, from, etc.
		{
			$qb_variable	= 'qb_'.$val;
			$qb_cache_var	= 'qb_cache_'.$val;
			$qb_new 	= $this->$qb_cache_var;

			for ($i = 0, $c = count($this->$qb_variable); $i < $c; $i++)
			{
				if ( ! in_array($this->{$qb_variable}[$i], $qb_new, TRUE))
				{
					$qb_new[] = $this->{$qb_variable}[$i];
					if ($val === 'select')
					{
						$qb_no_escape[] = $this->qb_no_escape[$i];
					}
				}
			}

			$this->$qb_variable = $qb_new;
			if ($val === 'select')
			{
				$this->qb_no_escape = $qb_no_escape;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Is literal
	 *
	 * Determines if a string represents a literal value or a field name
	 *
	 * @param	string	$str
	 * @return	bool
	 */
	protected function _is_literal($str)
	{
		$str = trim($str);

		if (empty($str) OR ctype_digit($str) OR (string) (float) $str === $str OR in_array(strtoupper($str), array('TRUE', 'FALSE'), TRUE))
		{
			return TRUE;
		}

		static $_str;

		if (empty($_str))
		{
			$_str = ($this->_escape_char !== '"')
				? array('"', "'") : array("'");
		}

		return in_array($str[0], $_str, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Reset Query Builder values.
	 *
	 * Publicly-visible method to reset the QB values.
	 *
	 * @return	CI_DB_query_builder
	 */
	public function reset_query()
	{
		$this->_reset_select();
		$this->_reset_write();
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the query builder values.  Called by the get() function
	 *
	 * @param	array	An array of fields to reset
	 * @return	void
	 */
	protected function _reset_run($qb_reset_items)
	{
		foreach ($qb_reset_items as $item => $default_value)
		{
			$this->$item = $default_value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the query builder values.  Called by the get() function
	 *
	 * @return	void
	 */
	protected function _reset_select()
	{
		$this->_reset_run(array(
			'qb_select'		=> array(),
			'qb_from'		=> array(),
			'qb_join'		=> array(),
			'qb_where'		=> array(),
			'qb_groupby'		=> array(),
			'qb_having'		=> array(),
			'qb_orderby'		=> array(),
			'qb_aliased_tables'	=> array(),
			'qb_no_escape'		=> array(),
			'qb_distinct'		=> FALSE,
			'qb_limit'		=> FALSE,
			'qb_offset'		=> FALSE
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the query builder "write" values.
	 *
	 * Called by the insert() update() insert_batch() update_batch() and delete() functions
	 *
	 * @return	void
	 */
	protected function _reset_write()
	{
		$this->_reset_run(array(
			'qb_set'	=> array(),
			'qb_set_ub'	=> array(),
			'qb_from'	=> array(),
			'qb_join'	=> array(),
			'qb_where'	=> array(),
			'qb_orderby'	=> array(),
			'qb_keys'	=> array(),
			'qb_limit'	=> FALSE
		));
	}

}
