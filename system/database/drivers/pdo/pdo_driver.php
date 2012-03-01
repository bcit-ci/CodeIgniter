<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 2.1.0
 * @filesource
 */

/**
 * PDO Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_driver extends CI_DB {

	public $dbdriver = 'pdo';
	public $trans_enabled = FALSE;

	// the character used to escape
	protected $_escape_char = '`';

	// clause and character used for LIKE escape sequences
	protected $_like_escape_str = ' ESCAPE \'%s\' ';
	protected $_like_escape_chr = '!';

	/**
	 * The syntax to count rows is slightly different across different
	 * database engines, so this string appears in each driver and is
	 * used for the count_all() and count_all_results() functions.
	 */
	protected $_count_string = 'SELECT COUNT(*) AS ';
	protected $_random_keyword;

	// PDO-specific properties
	public $subdriver;
	public $options = array();

	public function __construct($params)
	{
		parent::__construct($params);

		if (preg_match('/([^;]+):/', $this->dsn, $match) && count($match) === 2)
		{
			// If there is a valid DSN string found - we're done.
			// This is for general PDO users, who tend to have a full DSN string.
			$this->subdriver = end($match);
		}
		elseif ($this->_connect_string() === FALSE)
		{
			// Unable to create/validate DSN string
			// TODO: Possibly a nicer way to do this?
			show_error('Invalid DB Connection String for PDO');
		}

		// clause and character used for LIKE escape sequences
		// this one depends on the driver being used
		if ($this->subdriver === 'mysql')
		{
			$this->_like_escape_str = $this->_like_escape_chr = '';
		}
		elseif (in_array($this->subdriver, array('sqlite', 'sqlite2', 'pgsql')))
		{
			$this->_escape_char = '"';
		}
		elseif ($this->subdriver === 'odbc')
		{
			$this->_like_escape_str = ' {escape \'%s\'} ';
		}

		$this->_random_keyword = ' RND('.time().')';
	}

	// --------------------------------------------------------------------

	/**
	 * Connection String
	 *
	 * A PDO-specific method that tries to build a DSN string from
	 * configuration parameters.
	 *
	 * @return	bool
	 */
	protected function _connect_string()
	{
		// Legacy support for DSN strings supplied in the hostname field
		if (preg_match('/^([a-z0-9]+)\:.+$/i', $this->hostname, $matches))
		{
			// hostname generally would have this prototype
			// $db['hostname'] = 'subdriver:host(/Server(/DSN))=hostname(/DSN);';
			// We need to get the prefix (subdriver used by PDO).
			$this->dsn = rtrim($this->hostname);
			$this->subdriver = strtolower($matches[1]);
		}
		else
		{
			/* No driver prefix was found in the hostname ...
			 *
			 * Although this is not a documented configuration setting,
			 * we'll check if it isn't already set in $db['subdriver'].
			 *
			 * This could also be set by DB() if the configuration was
			 * provided via string in the following format:
			 *
			 * pdo://username:password@hostname:port/database?subdriver=pgsql
			 *
			 * If it's not - fail.
			 */
			if (empty($this->subdriver))
			{
				return FALSE;
			}

			$this->subdriver = strtolower($this->subdriver);
			$this->dsn = $this->subdriver.':';
		}

		// This might work in lower case, but on php.net it's always '4D'
		if ($this->subdriver === '4d' && substr($this->dsn, 0, 2) === '4d')
		{
			$this->dsn = '4D:'.substr($this->dsn, 3);
			$this->subdriver = '4D';
		}

		// OK ... now create and/or valudate the DSN
		if ($this->subdriver === 'oci')
		{
			/* Oracle has a slightly different PDO DSN format (Easy Connect).
			 * It also supports pre-defined DSNs.
			 */
			if (preg_match('/^oci:dbname=([^;\s\/]+)(;charset=(.+))?$/', $this->dsn, $matches))
			{
				// We have a predefined DSN already set - just check for the charset
				if (empty($matches[4]) && ! empty($this->char_set))
				{
					$this->dsn = 'oci:dbname='.$matches[1].';charset='.$this->char_set;
				}
			}
			elseif (preg_match('/^oci:dbname=\/\/([^:\/]+)(:[0-9]+)?\/([^;]+)?(;charset=.+)?$/', $this->dsn, $matches))
			{
				$this->dsn = 'oci:dbname=//'.$matches[1]
						.((empty($matches[2]) && ! empty($this->port) && ctype_digit($this->port)) ? ':'.$this->port : $matches[2])
						.'/'.($matches[3] === '' && $this->database !== '' ? $this->database : '')
						.(empty($matches[4])
							? ( ! empty($this->char_set) ? ';charset='.$this->char_set : '')
							: $matches[4]);
			}
			else
			{
				// No valid DSN found
				$this->dsn = 'oci:dbname=\/\/'.($this->hostname === '' ? 'localhost' : $this->hostname)
						.( ! empty($this->port) ? ':'.$this->port : '')
						.'/'.($this->database !== '' ? $this->database : '')
						.(empty($this->char_set) ? '' : ';charset='.$this->char_set);
			}

			return TRUE;
		}
		elseif (in_array($this->subdriver, array('sqlite', 'sqlite2')))
		{
			// SQLite only needs a filename path or ':memory:'
			if ( ! preg_match('/^'.$this->subdriver.':.{1,}$/', $this->dsn))
			{
				if ($this->hostname === '' && $this->database === '')
				{
					return FALSE;
				}

				$this->dsn = $this->subdriver.':'.(empty($this->hostname) ? $this->database : $this->hostname);
			}

			return TRUE;
		}
		elseif ($this->subdriver === 'odbc')
		{
			/* With ODBC being a kind of database abstraction layer itself,
			 * if we can't detect a system-wide DSN to be used, an ODBC subdriver
			 * must me provided.
			 * The problem is - if there's no such subdriver already set, then
			 * there's no way the DSN string is valid. And because parameters
			 * in it depend on the ODBC subdriver in use, even if we try to get
			 * it from a non-standart config variable - we can't know exactly what
			 * to append to it.
			 */
			return (bool) preg_match('/^odbc:([^=]{1,})|(driver=.+)$/', $this->dsn);
		}
		elseif ($this->subdriver === 'ibm')
		{
			// IBM supports pre-defined DSNs
			if (preg_match('/^ibm:DSN=.{1,}$/', $this->dsn))
			{
				return TRUE;
			}
			elseif ( ! preg_match('/DRIVER=\{([^}]+)\}/', $this->dsn, $matches)
				&& ( ! isset($this->ibmdriver) OR ! preg_match('/^\{?([^}]+)\}?$/', trim($this->ibmdriver), $matches)))
			{
				/* IBM requires a driver directive when a full DSN string is used
				 *
				 * If it's not already there - it can only be supplied by an
				 * additional configuration setting that's not documented, but
				 * still ... we should try.
				 *
				 * If we can't find one - try to use the hostname or database config
				 * settings as a pre-defined DSN. If we can't do that as well - fail.
				 */
				if ($this->hostname === '' && $this->database === '')
				{
					return FALSE;
				}

				$this->dsn = 'ibm:DSN='.($this->hostname === '' ? $this->database : $this->hostname);
				return TRUE;
			}

			// Get the values ...
			$this->ibmdriver = trim($matches[1]);
			$_protocol = preg_match('/PROTOCOL=([A-Z0-9]+)/', $this->dsn, $matches) ? $matches[1] : 'TCPIP';

			if (preg_match('/DATABASE=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->database = $matches[1];
			}

			if (preg_match('/HOSTNAME=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->hostname = $matches[1];
			}
			elseif ($this->hostname === '')
			{
				// Default to localhost
				$this->hostname = '127.0.0.1';
			}

			if (preg_match('/PORT=([1-9][0-9]{0,4})/', $this->dsn, $matches))
			{
				$this->port = $matches[1];
			}

			if ($this->database === '' && (empty($this->port) OR ! ctype_digit($this->port)))
			{
				// All of these parameters are required, so ...
				return FALSE;
			}

			/* Passing the username and password in the DSN is supported here.
			 * We won't do that, but if they are empty in our config, but are
			 * found in the DSN - we can get them.
			 */
			if ($this->username === '' && preg_match('/UID=[^;\s]/', $this->dsn, $matches))
			{
				$this->username = $matches[1];
			}

			if ($this->password === '' && preg_match('/PWD=[^;\s]/', $this->dsn, $matches))
			{
				$this->password = $matches[1];
			}

			$this->dsn = 'ibm:DRIVER={'.$this->ibmdriver.'};DATABASE='.$this->database
					.';HOSTNAME='.$this->hostname.';PORT='.$this->port.';PROTOCOL='.$_protocol.';';

			return TRUE;
		}
		elseif ($this->subdriver === 'sqlsrv')
		{
			if (preg_match('/Server=([^,;\s]+)(,([1-9][0-9]{0,4}))?/', $this->dsn, $matches))
			{
				$this->hostname = $matches[1];
				empty($matches[3]) OR $this->port = $matches[3];
			}
			elseif ($this->hostname === '')
			{
				// Default to localhost
				$this->hostname = 'localhost';
			}

			if (preg_match('/Database=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->database = $matches[1];
			}
			elseif ($this->database === '')
			{
				return FALSE;
			}

			// Some SQLSRV-specific optional parameters
			$_options = '';
			foreach (array(
					'APP' => '[^;\s]+',
					'ConnectionPooling' => '0|1',
					'Encrypt' => '0|1',
					'Failover_Partner' => '[^;\s]+',
					'LoginTimeout' => '[0-9]+',
					'MultipleActiveResultSets' => '[^;\s]+',
					'QuotedId' => '0|1',
					'TraceFile' => '[^;\s]+',
					'TraceOn' => '0|1',
					'TransactionIsolation' => '[0-9]+',
					'TrustServerCertificate' => '0|1',
					'WSID' => '[^;\s]+'
				) as $key => $value)
			{
				if (preg_match('/'.$key.'=('.$value.')/', $this->dsn, $matches))
				{
					$_options .= ';'.$key.'='.$matches[1];
				}
			}

			$this->dsn = 'sqlsrv:Server='.$this->hostname.';Database='.$this->database.$_options;
			return TRUE;
		}
		elseif ($this->subdriver === 'informix')
		{
			// Pre-defined DSNs are supported
		 	if (preg_match('/^informix:DSN=.+$/', $this->dsn))
			{
				return TRUE;
			}

			if (preg_match('/host=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->hostname = $matches[1];
			}

			if (preg_match('/service=([1-9][0-9]{0,4})/', $this->dsn, $matches))
			{
				$this->port = $matches[1];
			}

			if (preg_match('/database=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->database = $matches[1];
			}

			/* If this is not set - try to use the hostname or database name
			 * as a pre-defined DSN.
			 */
			if (preg_match('/server=([^;\s]+)/', $this->dsn, $matches))
			{
				$_server = $matches[1];
			}
			elseif ($this->hostname === '' && $this->database === '')
			{
				return FALSE;
			}
			else
			{
				$this->dsn = 'informix:DSN='.($this->hostname === '' ? $this->database : $this->hostname);
				return TRUE;
			}

			if ($this->database === '' OR (empty($this->port) OR ! ctype_digit($this->port)))
			{
				return FALSE;
			}

			// Default to localhost
			$this->hostname !== '' OR $this->hostname = 'localhost';
			$_protocol = preg_match('/protocol=([^;\s]+)/', $this->dsn, $matches) ? $matches[1] : 'onsoctcp';
			$_options = preg_match('/EnableScrollableCursors=(0|1)/', $this->dsn, $matches)
					? '; EnableScrollableCursors='.$matches[1]
					: '';

			$this->dsn = 'informix:host='.$this->hostname.'; service='.$this->port.'; database='.$this->database
					.'; server='.$_server.'; protocol='.$_protocol.$_options;
			return TRUE;
		}

		/* The rest of the subdrivers (mysql, pgsql, mssql|dblib|sybase, cubrid, 4D, firebird)
		 * have the same DSN format. Only some parameters may or may not exist depending on
		 * the driver in use. We'll work around that while treating all of them as a group.
		 *
		 * $_options will hold any extra parameters that might be needed
		 */
		$_options = array();

		// Postgre & 4D allow passing usernames and/or passwords in the DSN
		if (in_array($this->subdriver, array('pgsql', '4D')))
		{
			if (preg_match('/username=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->username = $matches[1];
			}

			if (preg_match('/password=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->password = $matches[1];
			}
		}

		// dbname
		if (preg_match('/dbname=([^;\s]+)/', $this->dsn, $matches))
		{
			$this->database = $matches[1];
		}
		elseif ($this->subdriver === 'firebird' && $this->database === '' && $this->hostname !== '')
		{
			$this->database = $this->hostname;
		}
		elseif ($this->database === '')
		{
			return FALSE;
		}

		// host & port
		if (in_array($this->subdriver, array('mssql', 'dblib', 'sybase')))
		{
			if (preg_match('/host=([^:,;\s]+)([:,]([1-9][0-9]{0,4}))?/', $this->dsn, $matches))
			{
				$this->hostname = $matches[1];
				empty($matches[3]) OR $this->port = $matches[3];
			}

			// We'll set these, while we're at the dblib subdriver
			if (preg_match('/appname=([^;]+)/', $this->dsn, $matches))
			{
				$_options['appname'] = $matches[1];
			}
			elseif ( ! empty($this->appname))
			{
				$_options['appname'] = $this->appname;
			}

			if (preg_match('/secure=([^;]+)/', $this->dsn, $matches))
			{
				$_options['secure'] = $matches[1];
			}
			elseif ( ! empty($this->secure))
			{
				$_options['secure'] = $this->secure;
			}
		}
		elseif ($this->subdriver === 'firebird')
		{
			/* Firebird only uses files, but also has a 'role' option.
			 *
			 * We have to make a check to exclude it anyway, and since
			 * the port field is useless here - we'll try and get the
			 * role from it, if it isn't already passed.
			 *
			 * Hostname field is already used as an alternative way to
			 * get the dbname.
			 */
			if (preg_match('/role=([^;\s]+)/', $this->dsn, $matches))
			{
				$_options['role'] = $matches[1];
			}
			elseif ( ! empty($this->role))
			{
				$_options['role'] = $this->role;
			}
			elseif ( ! empty($this->port))
			{
				$_options['role'] = $this->port;
			}
		}
		else
		{
			/* On UNIX systems, MySQL and Postgre allow connecting directly via
			 * UNIX sockets. Configurations for this differ, but host & port
			 * fields should be skipped from the DSN string.
			 */
			if ($this->subdriver === 'mysql' && DIRECTORY_SEPARATOR === '/')
			{
				if (preg_match('/unix_socket=([^;\s]+)/', $this->dsn, $matches))
				{
					$_options['unix_socket'] = $matches[1];
				}
				elseif ( ! empty($this->unix_socket) && is_string($this->unix_socket))
				{
					$_options['unix_socket'] = $this->unix_socket;
				}
			}
			elseif ($this->subdriver === 'pgsql' && isset($this->unix_socket))
			{
				$_options['unix_socket'] = (bool) $this->unix_socket;
			}

			if ( ! isset($_options['unix_socket']))
			{
				if (preg_match('/host=([^;\s]+)/', $this->dsn, $matches))
				{
					$this->hostname = $matches[1];
				}
				elseif ($this->hostname === '')
				{
					if ($this->subdriver === 'pgsql')
					{
						$_options['unix_socket'] = TRUE;
					}
					else
					{
						$this->hostname = 'localhost';
					}
				}

				if (preg_match('/port=([1-9][0-9]{0,4})/', $this->dsn, $matches))
				{
					$this->port = $matches[1];
				}
			}
		}

		// charset
		if (in_array($this->subdriver, array('mysql', 'mssql', 'dblib', 'sybase', '4d', 'firebird')))
		{
			if (preg_match('/charset=([^;\s]+)/', $this->dsn, $matches))
			{
				$this->char_set = $matches[1];
			}
		}
		else
		{
			// Not needed
			$this->char_set = '';
		}

		$this->dsn = $this->subdriver.':'
				.(($this->subdriver !== 'firebird' && ! isset($_options['unix_socket']))
					? 'host='.$this->hostname
						.(empty($port)
							? ''
							: (in_array($this->subdriver, array('mssql', 'dblib', 'sybase')) ? ',' : ';port=')
								.$this->port.';'
							)
					: (is_string($_options['unix_socket']) ? 'unix_socket='.$_options['unix_socket'].';' : '')
				)
				.($this->database !== '' ? 'dbname='.$this->database : '')
				.(empty($this->char_set) ? '' : ';charset='.$this->char_set);

		if (isset($_options['unix_socket']))
		{
			unset($_options['unix_socket']);
		}

		if (count($_options) > 0)
		{
			foreach ($_options as $key => $value)
			{
				$this->dsn .= ';'.$key.'='.$value;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Non-persistent database connection
	 *
	 * @return	object
	 */
	public function db_connect()
	{
		return $this->_pdo_connect();
	}

	// --------------------------------------------------------------------

	/**
	 * Persistent database connection
	 *
	 * @return	object
	 */
	public function db_pconnect()
	{
		return $this->_pdo_connect(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * PDO connection
	 *
	 * @param	bool
	 * @return	object
	 */
	protected function _pdo_connect($persistent = FALSE)
	{
		$this->options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_SILENT;
		$persistent == FALSE OR $this->options[PDO::ATTR_PERSISTENT] = TRUE;

		/* Prior to PHP 5.3.6, even if the charset was supplied in the DSN
		 * on connect - it was ignored. This is a work-around for the issue.
		 *
		 * Reference: http://www.php.net/manual/en/ref.pdo-mysql.connection.php
		 */
		if ($this->subdriver === 'mysql' && ! is_php('5.3.6') && ! empty($this->char_set))
		{
			$this->options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES '.$this->char_set
										.( ! empty($this->db_collat) ? " COLLATE '".$this->dbcollat."'" : '');
		}

		// Connecting...
		try
		{
			return new PDO($this->dsn, $this->username, $this->password, $this->options);
		}
		catch (PDOException $e)
		{
			if ($this->db_debug && empty($this->failover))
			{
				$this->display_error($e->getMessage(), '', TRUE);
			}

			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Reconnect
	 *
	 * Keep / reestablish the db connection if no queries have been
	 * sent for a length of time exceeding the server's idle timeout
	 *
	 * @return	void
	 */
	public function reconnect()
	{
		return ($this->db_debug) ? $this->display_error('db_unsuported_feature') : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database
	 *
	 * @return	bool
	 */
	public function db_select()
	{
		// Not needed for PDO
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @return	string
	 */
	protected function _version()
	{
		return $this->conn_id->getAttribute(PDO::ATTR_SERVER_VERSION);
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the query
	 *
	 * @param	string	an SQL query
	 * @return	object
	 */
	protected function _execute($sql)
	{
		$sql = $this->_prep_query($sql);
		return $this->conn_id->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @param	string	an SQL query
	 * @return	string
	 */
	protected function _prep_query($sql)
	{
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @return	bool
	 */
	public function trans_begin($test_mode = FALSE)
	{
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ( ! $this->trans_enabled OR $this->_trans_depth > 0)
		{
			return TRUE;
		}

		// Reset the transaction failure flag.
		// If the $test_mode flag is set to TRUE transactions will be rolled back
		// even if the queries produce a successful result.
		$this->_trans_failure = ($test_mode === TRUE);

		return $this->conn_id->beginTransaction();
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @return	bool
	 */
	public function trans_commit()
	{
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ( ! $this->trans_enabled OR $this->_trans_depth > 0)
		{
			return TRUE;
		}

		return $this->conn->commit();
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	public function trans_rollback()
	{
		// When transactions are nested we only begin/commit/rollback the outermost ones
		if ( ! $this->trans_enabled OR $this->_trans_depth > 0)
		{
			return TRUE;
		}

		return $this->conn_id->rollBack();
	}

	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @param	string
	 * @param	bool	whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	public function escape_str($str, $like = FALSE)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->escape_str($val, $like);
			}

			return $str;
		}

		// Escape the string
		$str = $this->conn_id->quote($str);

		// If there are duplicated quotes - trim them away
		if (strpos($str, "'") === 0)
		{
			$str = substr($str, 1, -1);
		}

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(array('%', '_', $this->_like_escape_chr),
						array($this->_like_escape_chr.'%',
						      $this->_like_escape_chr.'_',
						      $this->_like_escape_chr.$this->_like_escape_chr
						),
						$str
				);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Affected Rows
	 *
	 * @return	int
	 */
	public function affected_rows()
	{
		return is_object($this->result_id) ? $this->result_id->rowCount() : 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert ID
	 *
	 * @param	string
	 * @return	int
	 */
	public function insert_id($name = NULL)
	{
		if ($this->subdriver === 'pgsql' && $name === NULL && $this->_version >= '8.1')
		{
			$query = $this->query('SELECT LASTVAL() AS ins_id');
			$query = $query->row();
			return $query->ins_id;
		}

		return $this->conn_id->lastInsertId($name);
	}

	// --------------------------------------------------------------------

	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @param	string
	 * @return	string
	 */
	public function count_all($table = '')
	{
		if ($table == '')
		{
			return 0;
		}

		$query = $this->query($this->_count_string.$this->_protect_identifiers('numrows').' FROM '.$this->_protect_identifiers($table, TRUE, NULL, FALSE));
		if ($query->num_rows() == 0)
		{
			return 0;
		}

		$query = $query->row();
		$this->_reset_select();

		return (int) $query->numrows;
	}

	// --------------------------------------------------------------------

	/**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		if ($this->subdriver === 'pgsql')
		{
			// Analog function to show all tables in Postgre
			$sql = "SELECT * FROM information_schema.tables WHERE table_schema = 'public'";
		}
		elseif (in_array($this->subdriver, array('sqlite', 'sqlite2')))
		{
			return 'SELECT "name" FROM "SQLITE_MASTER"'
				.(($prefix_limit !== FALSE && $this->dbprefix !== '') ? ' WHERE "name" LIKE \''.$this->dbprefix."%'" : '');
		}
		else
		{
			$sql = 'SHOW TABLES FROM `'.$this->database.'`';
		}

		return ($prefix_limit !== FALSE && $this->dbprefix != '') ? FALSE : $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		return 'SHOW COLUMNS FROM '.$this->_from_tables($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _field_data($table)
	{
		return 'SELECT TOP 1 FROM '.$this->_from_tables($table);
	}

	// --------------------------------------------------------------------

	/**
	 * The error message string
	 *
	 * @return	string
	 */
	protected function _error_message()
	{
		$error_array = $this->conn_id->errorInfo();
		return $error_array[2];
	}

	// --------------------------------------------------------------------

	/**
	 * The error message number
	 *
	 * @return	string
	 */
	protected function _error_number()
	{
		return $this->conn_id->errorCode();
	}

	// --------------------------------------------------------------------

	/**
	 * Escape the SQL Identifiers
	 *
	 * This function escapes column and table names
	 *
	 * @param	string
	 * @return	string
	 */
	public function _escape_identifiers($item)
	{
		if ($this->_escape_char == '')
		{
			return $item;
		}

		foreach ($this->_reserved_identifiers as $id)
		{
			if (strpos($item, '.'.$id) !== FALSE)
			{
				$item = str_replace('.', $this->_escape_char.'.', $item);

				// remove duplicates if the user already included the escape
				return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $this->_escape_char.$item);
			}
		}

		if (strpos($item, '.') !== FALSE)
		{
			$item  = str_replace('.', $this->_escape_char.'.'.$this->_escape_char, $item);
		}

		// remove duplicates if the user already included the escape
		return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $this->_escape_char.$str.$this->_escape_char);
	}

	// --------------------------------------------------------------------

	/**
	 * From Tables
	 *
	 * This function implicitly groups FROM tables so there is no confusion
	 * about operator precedence in harmony with SQL standards
	 *
	 * @param	array
	 * @return	string
	 */
	protected function _from_tables($tables)
	{
		if ( ! is_array($tables))
		{
			$tables = array($tables);
		}

		return (count($tables) === 1) ? '`'.$tables[0].'`' : '('.implode(', ', $tables).')';
	}

	// --------------------------------------------------------------------

	/**
	 * Insert statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	protected function _insert($table, $keys, $values)
	{
		return 'INSERT INTO '.$this->_from_tables($table).' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------

	/**
	 * Insert_batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param   string  the table name
	 * @param   array   the insert keys
	 * @param   array   the insert values
	 * @return  string
	 */
	protected function _insert_batch($table, $keys, $values)
	{
		return 'INSERT INTO '.$this->_from_tables($table).' ('.implode(', ', $keys).') VALUES '.implode(', ', $values);
	}

	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @param	array	the orderby clause
	 * @param	array	the limit clause
	 * @return	string
	 */
	protected function _update($table, $values, $where, $orderby = array(), $limit = FALSE)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}

		return 'UPDATE '.$this->_from_tables($table).' SET '.implode(', ', $valstr)
			.(($where != '' && count($where) > 0) ? ' WHERE '.implode(' ', $where) : '')
			.(count($orderby) > 0 ? ' ORDER BY '.implode(', ', $orderby) : '')
			.( ! $limit ? '' : ' LIMIT '.$limit);
	}

	// --------------------------------------------------------------------

	/**
	 * Update_Batch statement
	 *
	 * Generates a platform-specific batch update string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the update data
	 * @param	array	the where clause
	 * @return	string
	 */
	protected function _update_batch($table, $values, $index, $where = NULL)
	{
		$ids   = array();
		foreach ($values as $key => $val)
		{
			$ids[] = $val[$index];

			foreach (array_keys($val) as $field)
			{
				if ($field != $index)
				{
					$final[$field][] =  'WHEN '.$index.' = '.$val[$index].' THEN '.$val[$field];
				}
			}
		}

		$cases = '';
		foreach ($final as $k => $v)
		{
			$cases .= $k." = CASE \n".implode("\n", $v)."\n"
				.'ELSE '.$k.' END, ';
		}

		return 'UPDATE'.$this->_from_tables($table).' SET '
			.substr($cases, 0, -2)
			.' WHERE '.(($where != '' && count($where) > 0) ? implode(' ', $where).' AND ' : '')
			.$index.' IN ('.implode(',', $ids).')';
	}


	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 * If the database does not support the truncate() command
	 * This function maps to "DELETE FROM table"
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _truncate($table)
	{
		return $this->_delete($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the where clause
	 * @param	string	the limit clause
	 * @return	string
	 */
	protected function _delete($table, $where = array(), $like = array(), $limit = FALSE)
	{
		$conditions = '';
		if (count($where) > 0 OR count($like) > 0)
		{
			$conditions = "\nWHERE ".implode("\n", $this->ar_where)
					.((count($where) > 0 && count($like) > 0) ? ' AND ' : '')
					.implode("\n", $like);
		}

		return 'DELETE FROM '.$this->_from_tables($table).$conditions.( ! $limit ? '' : ' LIMIT '.$limit);
	}

	// --------------------------------------------------------------------

	/**
	 * Limit string
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @param	string	the sql query string
	 * @param	int	the number of rows to limit the query to
	 * @param	int	the offset value
	 * @return	string
	 */
	protected function _limit($sql, $limit, $offset)
	{
		if ($this->subdriver === 'cubrid' OR $this->subdriver === 'sqlite')
		{
			return $sql.' LIMIT'.($offset == 0 ? '' : $offset.', ').$limit;
		}

		return $sql.' LIMIT '.$limit.($offset == 0 ? '' : ' OFFSET '.$offset);
	}

	// --------------------------------------------------------------------

	/**
	 * Close DB Connection
	 *
	 * @param	object
	 * @return	void
	 */
	protected function _close($conn_id)
	{
		$this->conn_id = NULL;
	}

}

/* End of file pdo_driver.php */
/* Location: ./system/database/drivers/pdo/pdo_driver.php */
