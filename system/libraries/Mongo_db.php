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
 * @since		Version 3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter MongoDB Class
 *
 * A library to interface with the NoSQL database MongoDB. For more information see http://www.mongodb.org
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Nisheeth Barthwal
 * @link
 */

class Mongo_db {

	/**
	 * Name of default config file
	 * @var	string
	 */
	const CONFIG_FILE  = 'mongo_db';

	/**
	 * The last error thrown by the database
	 *
	 * @var	MongoException
	 */
	protected $_last_error			= FALSE;

	/**
	 * The cursor object to the last query
	 *
	 * @var	MongoCursor
	 */
	protected $_cursor				= FALSE;

	/**
	 * MongoId of the last inserted document
	 * @var int
	 */
	protected $_insert_id			= 0;

	/**
	 * Number of documents affected by last query
	 *
	 * @var int
	 */
	protected $_affected_documents	= 0;

	/**
	 * Whether the last query updated an existing document
	 * @var bool
	 */
	protected $_updated_existing	= FALSE;

	/**
	 * DSN for connecting to MongoDB
	 *
	 * @var string
	 */
	public $dsn				= '';

	/**
	 * Database Username
	 *
	 * @var string
	 */
	public $username		 = '';

	/**
	 * Database Password
	 *
	 * @var string
	 */
	public $password		= '';

	/**
	 * Database Hostname
	 *
	 * @var string
	 */
	public $hostname		 = '';

	/**
	 * Database Name
	 *
	 * @var string
	 */
	public $database		= '';

	/**
	 * Whether to automatically initialize the class
	 *
	 * @var string
	 */
	public $autoinit		= TRUE;

	/**
	 * Port for connecting to the database
	 *
	 * @var string
	 */
	public $port			= '';

	/**
	 * Default result format
	 *
	 * @var	string	'array' or 'object'
	 */
	public $result_set		= 'array';

	/**
	 * Debug mode
	 *
	 * @var bool
	 */
	public $db_debug		= FALSE;

	/**
	 * Attach the optional database flag to the DSN
	 *
	 * @var bool
	 */
	public $host_db_flag	= FALSE;

	/**
	 * Options for the connection
	 *
	 * @var array
	 */
	public $options			= array();

	/**
	 * Current connection object
	 *
	 * @var MongoClient
	 */
	public $conn_id			= FALSE;

	/**
	 * Connection object to current database
	 *
	 * @var string
	 */
	public $db				= FALSE;

	/**
	 * Constructor - Sets Email Preferences
	 *
	 * The constructor can be passed an array of config values
	 *
	 * @param array $config = array()
	 * @return Mongo_db
	 */
	public function __construct($config = array())
	{
		if ( ! class_exists('Mongo'))
		{
			$this->display_error("The MongoDB PECL extension has not been installed or enabled");
		}

		$this->initialize($config);
		log_message('debug', 'Database Driver Class Initialized');
	}

	/**
	 * Returns the current MongoClient version
	 *
	 * @return string
	 */
	public function get_driver_version()
	{
		return MongoClient::VERSION;
	}

	/**
	 * Initializes the class with the config
	 *
	 * @param array $config = array()
	 * @return Mongo_db
	 */
	public function initialize($config = array())
	{
		if (count($config) === 0)
		{
			$CI =& get_instance();
			$CI->config->load(self::CONFIG_FILE);
			$config = $CI->config->item($CI->config->item('active_group'));
		}

		$this->dsn = '';

		if (isset($config['active_group']))
		{
			$config = $config[$config['active_group']];
		}

		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		if ($this->autoinit)
		{
			if(empty($this->dsn))
			{
				$this->build_dsn();
			}
			$this->connect();
		}

		return $this;
	}

	/**
	 * Attempts to connect to the database. Builds the DSN if not already built.
	 *
	 * @return Mongo_db|FALSE
	 */
	public function connect()
	{
		try
		{
			if (empty($this->dsn))
				$this->build_dsn();
			$this->conn_id = new Mongo($this->dsn, array(
				'w' => $this->options['write_concern'],
				'wTimeout' => $this->options['timeout']
			));
			$this->db = $this->conn_id->{$this->database};
			return $this;
		}
		catch (MongoConnectionException $e)
		{
			$this->_last_error = $e;
			if ($this->db_debug)
			{
				$this->display_error($e->getMessage());
			}
			return FALSE;
		}
	}

	/**
	 * Builds the DSN for the connection
	 *
	 * @return void
	 */
	protected function build_dsn()
	{
		$this->dsn = "mongodb://";

		if (empty($this->hostname))
		{
			$this->display_error("No hostname specified");
		}

		if (empty($this->database))
		{
			$this->display_error("No database specified");
		}

		if ( ! empty($this->username) && ! empty($this->password))
		{
			$this->dsn .= "{$this->username}:{$this->password}@";
		}

		if (isset($this->port) && ! empty($this->port))
		{
			$this->dsn .= "{$this->hostname}:{$this->port}";
		}
		else
		{
			$this->dsn .= "{$this->hostname}";
		}

		if ($this->host_db_flag === TRUE)
		{
			$this->dsn = trim($this->dsn) . '/' . $this->database;
		}
		else
		{
			$this->dsn = trim($this->dsn);
		}
	}

	/**
	 * Displays a critical error
	 *
	 * @param string $msg = ''
	 */
	public function display_error($msg = '')
	{
		$error =& load_class('Exceptions', 'core');
		die($error->show_error('MongoDb Error', $msg, 'error_db'));
	}

	/**
	 * Returns the last error message
	 *
	 * @return string
	 */
	public function last_error_message()
	{
		return (is_object($this->_last_error))
			? $this->_last_error->getMessage()
			: "";
	}

	/**
	 * Returns the last error code
	 *
	 * @return int
	 */
	public function last_error_code()
	{
		return (is_object($this->_last_error))
			? $this->_last_error->getCode()
			: 0;
	}

	/**
	 * Select database
	 *
	 * @param string $database
	 * @return bool
	 */
	public function use_db($database)
	{
		if ( ! empty($database))
		{
			$this->database = $database;
			try
			{
				$this->db = $this->conn_id->{$this->database};
				return TRUE;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Drop database
	 *
	 * @param string $database
	 * @return bool|array
	 */
	public function drop_db($database)
	{
		if ( ! empty($database))
		{
			try
			{
				return $this->conn_id->{$this->database}->drop();
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Create a new collection
	 *
	 * @link http://www.php.net/manual/en/mongodb.createcollection.php
	 * @param string $collection
	 * @param bool $capped = FALSE
	 * @param int $size = 0
	 * @param int $max = 0
	 * @return bool
	 */
	public function create($collection, $capped = FALSE, $size = 0, $max = 0)
	{
		if ( ! empty($collection))
		{
			try
			{
				$this->db->createCollection($collection, $capped, $size, $max);
				return TRUE;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Drop collection
	 *
	 * @param string $collection
	 * @return bool|array
	 */
	public function drop($collection)
	{
		if ( ! empty($collection))
		{
			try
			{
				return $this->db->selectCollection($collection)->drop();
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Execute a command on the database
	 *
	 * @link http://www.php.net/manual/en/mongodb.command.php
	 * @param array $command
	 * @param string|bool $collection = FALSE
	 * @param array $options = array()
	 * @return bool|array
	 */
	public function command($command, $collection = FALSE, $options = array())
	{
		if ( ! empty($command))
		{
			try
			{
				$this->_update_option('timeout', $options);

				if ($collection !== FALSE)
					return $this->db->selectCollection($collection)->command($command, $options);
				else
					return $this->db->command($command, $options);
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Returns all indexes of the collection
	 *
	 * @link http://www.php.net/manual/en/mongocollection.getindexinfo.php
	 * @param string $collection
	 * @return bool|array
	 */
	public function get_indexes($collection)
	{
		if ( ! empty($collection))
		{
			try
			{
				return $this->db->selectCollection($collection)->getIndexInfo ();
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Create an index
	 *
	 * @link http://www.php.net/manual/en/mongocollection.ensureindex.php
	 * @param string $collection
	 * @param string|array $keys
	 * @param array $options = array()
	 * @return bool
	 */
	public function ensure_index($collection, $keys, $options = array())
	{
		if ( ! empty($collection))
		{
			try
			{
				return $this->db->selectCollection($collection)->ensureIndex($keys, $options);
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Delete index
	 *
	 * @link http://www.php.net/manual/en/mongocollection.deleteindex.php
	 * @param string $collection
	 * @param string|array $keys
	 * @return bool|array
	 */
	public function delete_index($collection, $keys)
	{
		if ( ! empty($collection))
		{
			try
			{
				return $this->db->selectCollection($collection)->deleteIndex($keys);
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Delete all indexes
	 *
	 * @link http://www.php.net/manual/en/mongocollection.deleteindexes.php
	 * @param string $collection
	 * @return bool|array
	 */
	public function delete_indexes($collection)
	{
		if ( ! empty($collection))
		{
			try
			{
				return $this->db->selectCollection($collection)->deleteIndexes();
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Return array of distinct values for a given key
	 *
	 * @link http://www.php.net/manual/en/mongocollection.distinct.php
	 * @param string $collection
	 * @param string $key
	 * @param array $query = array()
	 * @return bool|array
	 */
	public function distinct($collection, $key, $query = array())
	{
		if ( ! empty($collection))
		{
			try
			{
				return $this->db->selectCollection($collection)->distinct($key, $query);
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Count the records in the query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.count.php
	 * @param string $collection
	 * @param array $query = array()
	 * @param int $limit = 0
	 * @param int $skip = 0
	 * @return bool|int
	 */
	public function count($collection, $query = array(), $limit = 0, $skip = 0)
	{
		if ( ! empty($collection))
		{
			try
			{
				return $this->db->selectCollection($collection)->count($query, $limit, $skip);
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Perform an aggregation on the collection
	 *
	 * @link http://www.php.net/manual/en/mongocollection.aggregate.php
	 * @param string $collection
	 * @param array $pipeline
	 * @param array $op [, array $... ] (vararg)
	 * @return bool|array|object
	 */
	public function aggregate($collection, $pipeline, $op = array())
	{
		$this->_last_error = NULL;
		$params = func_get_args();
		array_shift($params);

		if ( ! empty($collection))
		{
			try
			{
				if ($r =  call_user_func_array(array($this->db->selectCollection($collection), 'aggregate'), $params))
				{
					if ($r['ok'])
						return ($doc = $r['result']) && $this->result_set === 'object'
							? (object)$doc
							: $doc;
				}
				return FALSE;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Perform a group query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.group.php
	 * @param string $collection
	 * @param mixed $keys
	 * @param array $initial
	 * @param MongoCode $reduce
	 * @param array $options = array()
	 * @return bool|array|object
	 */
	public function group($collection, $keys, $initial, $reduce, $options = array())
	{
		if ( ! empty($collection))
		{
			try
			{
				return ($doc = $this->db->selectCollection($collection)->group($keys, $initial, $reduce, $options)) && $this->result_set === 'object'
					? (object)$doc
					: $doc;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Performs a find query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.find.php
	 * @param string $collection
	 * @param array $query = array()
	 * @param array $fields = array()
	 * @return bool|Mongo_db
	 */
	public function find($collection, $query = array(), $fields = array())
	{
		if ( ! empty($collection))
		{
			try
			{
				$this->_cursor = $this->db->selectCollection($collection)->find($query, $fields);
				return $this;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Gets the current MongoCursor object
	 *
	 * @return bool|MongoCursor
	 */
	public function cursor()
	{
		return $this->_cursor;
	}

	/**
	 * Fetch the result pointed by the cursor
	 *
	 * @return bool|array|object
	 */
	public function result()
	{
		if ($this->_cursor !== FALSE)
		{
			$result = array();
			foreach ($this->_cursor as $doc)
			{
				$result[] = $this->result_set === 'object'? (object)$doc: $doc;
			}
			$this->_cursor = FALSE;
			return $result;
		}
		return FALSE;
	}

	/**
	 * Fetch the result pointed by the cursor indexed by a key
	 *
	 * @param string $field
	 * @return bool|array|object
	 */
	public function result_by($field)
	{
		if ($this->_cursor !== FALSE)
		{
			$result = array();
			foreach ($this->_cursor as $doc)
				$result[(string)$doc[$field]] = $this->result_set === 'object'? (object)$doc: $doc;
			$this->_cursor = FALSE;
			return $result;
		}
		return FALSE;
	}

	/**
	 * Fetch the result pointed by the cursor aggregated by a key
	 *
	 * @param string $field
	 * @return bool|array|object
	 */
	public function result_group($field)
	{
		if ($this->_cursor !== FALSE)
		{
			$result = array();
			foreach ($this->_cursor as $doc)
				$result[(string)$doc[$field]][] = $this->result_set === 'object'? (object)$doc: $doc;
			$this->_cursor = FALSE;
			return $result;
		}
		return FALSE;
	}

	/**
	 * Performs the findOne query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.findone.php
	 * @param string $collection
	 * @param array $query = array()
	 * @param array $fields = array()
	 * @return bool|array|object
	 */
	public function find_one($collection, $query = array(), $fields = array())
	{
		if ( ! empty($collection))
		{
			try
			{
				return ($doc = $this->db->selectCollection($collection)->findOne($query, $fields)) && $this->result_set === 'object'
					? (object)$doc
					: $doc;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Performs the findAndModify query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.findandmodify.php
	 * @param string $collection
	 * @param array $query
	 * @param array $update = array()
	 * @param array $fields = array()
	 * @param array $options = array()
	 * @return bool|array|object
	 */
	public function find_and_modify($collection, $query, $update = array(), $fields = array(), $options = array())
	{
		if ( ! empty($collection))
		{
			try
			{
				return ($doc = $this->db->selectCollection($collection)->findAndModify($query, $update, $fields, $options)) && $this->result_set === 'object'
					? (object)$doc
					: $doc;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Performs an inset query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.insert.php
	 * @param string $collection
	 * @param array $data
	 * @param array $options = array()
	 * @return bool|array
	 */
	public function insert($collection, $data, $options = array())
	{
		$this->_clear_updates();
		if ( ! empty($collection))
		{
			try
			{
				$this->_update_option('fsync', $options);

				$r = $this->db->selectCollection($collection)->insert($data, $options);
				if (is_array($r))
				{
					$this->_affected_documents = $r['ok'] ? 1 : 0;
					if (isset($data['_id']))
						$this->_insert_id = (string)$data['_id'];
				}
				return $r;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Peforms an update query
	 *
	 * @lnk http://www.php.net/manual/en/mongocollection.update.php
	 * @param string $collection
	 * @param array $criteria
	 * @param array $new_object
	 * @param array $options = array()
	 * @return bool|array
	 */
	public function update($collection, $criteria, $new_object, $options = array())
	{
		$this->_clear_updates();
		if ( ! empty($collection))
		{
			try
			{
				$this->_update_option('fsync', $options);

				$r = $this->db->selectCollection($collection)->update($criteria, $new_object, $options);
				if (is_array($r))
				{
					$this->_affected_documents = $r['n'];
					if (isset($r['upserted']))
						$this->_insert_id = $r['upserted'];

					if (isset($r['updatedExisting']))
						$this->_updated_existing = $r['updatedExisting'];
				}
				return $r;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Performs a save query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.save.php
	 * @param string $collection
	 * @param array $data
	 * @param array $options = array()
	 * @return bool|array
	 */
	public function save($collection, $data, $options = array())
	{
		$this->_clear_updates();
		if ( ! empty($collection))
		{
			try
			{
				$this->_update_option('fsync', $options);

				$r = $this->db->selectCollection($collection)->save($data, $options);
				if (is_array($r))
				{
					$this->_affected_documents = $r['n'];
					if (isset($r['upserted']))
						$this->_insert_id = $r['upserted'];

					if (isset($r['updatedExisting']))
						$this->_updated_existing = $r['updatedExisting'];
				}
				return $r;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Performs a remove query
	 *
	 * @link http://www.php.net/manual/en/mongocollection.remove.php
	 * @param string $collection
	 * @param array $criteria
	 * @param array $options = array()
	 * @return bool|array
	 */
	public function remove($collection, $criteria, $options = array())
	{
		$this->_clear_updates();
		if ( ! empty($collection))
		{
			try
			{
				$this->_update_option('fsync', $options);

				$r = $this->db->selectCollection($collection)->remove($criteria, $options);
				if (is_array($r))
				{
					$this->_affected_documents = $r['n'];
				}
				return $r;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Gets the number of documents affected by the last query
	 *
	 * @return int
	 */
	public function affected_documents()
	{
		return $this->_affected_documents;
	}

	/**
	 * Gets the MongoId of the document inserted by the last query
	 *
	 * @return MongoId
	 */
	public function insert_id()
	{
		return $this->_insert_id;
	}

	/**
	 * Whether the last query updated an existing document
	 *
	 * @return bool
	 */
	public function updated_existing()
	{
		return $this->_updated_existing;
	}


	/**
	 * Limit the number of documents for the current cursor
	 *
	 * @link http://www.php.net/manual/en/mongocursor.limit.php
	 * @param int $num
	 * @return bool|Mongo_db
	 */
	public function limit($num)
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				$this->_cursor = $this->_cursor->limit($num);
				return $this;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Number of documents to skip in cursor
	 *
	 * @link http://www.php.net/manual/en/mongocursor.skip.php
	 * @param $num
	 * @return bool|Mongo_db
	 */
	public function skip($num)
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				$this->_cursor = $this->_cursor->skip($num);
				return $this;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Sort the results in cursor
	 *
	 * @link http://www.php.net/manual/en/mongocursor.sort.php
	 * @param array $fields
	 * @return bool|Mongo_db
	 */
	public function sort($fields)
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				$this->_cursor = $this->_cursor->sort($fields);
				return $this;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Provide the database a hint about the query
	 *
	 * @link http://www.php.net/manual/en/mongocursor.hint.php
	 * @param array $key_pattern
	 * @return bool|Mongo_db
	 */
	public function hint($key_pattern)
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				$this->_cursor = $this->_cursor->hint($key_pattern);
				return $this;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Get the number of results for the current cursor
	 *
	 * @link http://www.php.net/manual/en/mongocursor.count.php
	 * @param bool $found_only = FALSE
	 * @return bool
	 */
	public function result_count($found_only = FALSE)
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				return $this->_cursor->count($found_only);
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Set a client-side timeout for the query
	 *
	 * @link http://www.php.net/manual/en/mongocursor.timeout.php
	 * @param int $ms
	 * @return bool|Mongo_db
	 */
	public function timeout($ms)
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				$this->_cursor = $this->_cursor->timeout($ms);
				return $this;
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Return an explanation of the query
	 *
	 * @link http://www.php.net/manual/en/mongocursor.explain.php
	 * @return bool|array
	 */
	public function explain()
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				return $this->_cursor->explain();
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Get the query, fields, limit, and skip for the current cursor
	 *
	 * @link http://www.php.net/manual/en/mongocursor.info.php
	 * @return bool|array
	 */
	public function info()
	{
		if ($this->_cursor !== FALSE)
		{
			try
			{
				return $this->_cursor->info();
			}
			catch (Exception $e)
			{
				$this->_last_error = $e;
				if ($this->db_debug)
				{
					$this->display_error($e->getMessage());
				}
			}
		}
		return FALSE;
	}

	/**
	 * Create a MongoId from UNIX timestamp
	 *
	 * @param int $timestamp
	 * @return MongoId
	 */
	public function mongoid_from_time($timestamp)
	{
		return new MongoId(str_pad(dechex($timestamp), 8, "0", STR_PAD_LEFT)."0000000000000000");
	}

	/**
	 * Update an array with default option if not already present
	 *
	 * @param string $key
	 * @param &array $array
	 */
	protected function _update_option($key, &$array)
	{
		if ( ! isset($array[$key]))
		{
			$array[$key] = $this->options[$key];
		}
	}

	/**
	 * Reset all query-specific parameters
	 */
	protected function _clear_updates()
	{
		$this->_insert_id = FALSE;
		$this->_affected_documents = FALSE;
		$this->_updated_existing = FALSE;
	}
}

/* End of file Mongo_db.php */
/* Location: ./core/libraries/Mongo_db.php */