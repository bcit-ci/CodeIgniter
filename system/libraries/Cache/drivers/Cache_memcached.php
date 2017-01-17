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
 * @since	Version 2.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Memcached Caching Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache_memcached extends CI_Driver {

	/**
	 * Holds the memcached object
	 *
	 * @var object
	 */
	protected $_memcached;

	/**
	 * Memcached configuration
	 *
	 * @var array
	 */
	protected $_config = array(
		'default' => array(
			'host'		=> '127.0.0.1',
			'port'		=> 11211,
			'weight'	=> 1
		)
	);

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * Setup Memcache(d)
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// Try to load memcached server info from the config file.
		$CI =& get_instance();
		$defaults = $this->_config['default'];

		if ($CI->config->load('memcached', TRUE, TRUE))
		{
			$this->_config = $CI->config->config['memcached'];
		}

		if (class_exists('Memcached', FALSE))
		{
			$this->_memcached = new Memcached();
		}
		elseif (class_exists('Memcache', FALSE))
		{
			$this->_memcached = new Memcache();
		}
		else
		{
			log_message('error', 'Cache: Failed to create Memcache(d) object; extension not loaded?');
			return;
		}

		foreach ($this->_config as $cache_name => $cache_server)
		{
			if ( ! isset($cache_server['hostname']))
			{
				log_message('debug', 'Cache: Memcache(d) configuration "'.$cache_name.'" doesn\'t include a hostname; ignoring.');
				continue;
			}
			elseif ($cache_server['hostname'][0] === '/')
			{
				$cache_server['port'] = 0;
			}
			elseif (empty($cache_server['port']))
			{
				$cache_server['port'] = $defaults['port'];
			}

			isset($cache_server['weight']) OR $cache_server['weight'] = $defaults['weight'];

			if ($this->_memcached instanceof Memcache)
			{
				// Third parameter is persistence and defaults to TRUE.
				$this->_memcached->addServer(
					$cache_server['hostname'],
					$cache_server['port'],
					TRUE,
					$cache_server['weight']
				);
			}
			elseif ($this->_memcached instanceof Memcached)
			{
				$this->_memcached->addServer(
					$cache_server['hostname'],
					$cache_server['port'],
					$cache_server['weight']
				);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch from cache
	 *
	 * @param	string	$id	Cache ID
	 * @return	mixed	Data on success, FALSE on failure
	 */
	public function get($id)
	{
		$data = $this->_memcached->get($id);

		return is_array($data) ? $data[0] : $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save
	 *
	 * @param	string	$id	Cache ID
	 * @param	mixed	$data	Data being cached
	 * @param	int	$ttl	Time to live
	 * @param	bool	$raw	Whether to store the raw value
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		if ($raw !== TRUE)
		{
			$data = array($data, time(), $ttl);
		}

		if ($this->_memcached instanceof Memcached)
		{
			return $this->_memcached->set($id, $data, $ttl);
		}
		elseif ($this->_memcached instanceof Memcache)
		{
			return $this->_memcached->set($id, $data, 0, $ttl);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 *
	 * @param	mixed	$id	key to be deleted.
	 * @return	bool	true on success, false on failure
	 */
	public function delete($id)
	{
		return $this->_memcached->delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to add
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function increment($id, $offset = 1)
	{
		return $this->_memcached->increment($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to reduce by
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function decrement($id, $offset = 1)
	{
		return $this->_memcached->decrement($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 *
	 * @return	bool	false on failure/true on success
	 */
	public function clean()
	{
		return $this->_memcached->flush();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @return	mixed	array on success, false on failure
	 */
	public function cache_info()
	{
		return $this->_memcached->getStats();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 *
	 * @param	mixed	$id	key to get cache metadata on
	 * @return	mixed	FALSE on failure, array on success.
	 */
	public function get_metadata($id)
	{
		$stored = $this->_memcached->get($id);

		if (count($stored) !== 3)
		{
			return FALSE;
		}

		list($data, $time, $ttl) = $stored;

		return array(
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> $data
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Is supported
	 *
	 * Returns FALSE if memcached is not supported on the system.
	 * If it is, we setup the memcached object & return TRUE
	 *
	 * @return	bool
	 */
	public function is_supported()
	{
		return (extension_loaded('memcached') OR extension_loaded('memcache'));
	}

	// ------------------------------------------------------------------------

	/**
	 * Class destructor
	 *
	 * Closes the connection to Memcache(d) if present.
	 *
	 * @return	void
	 */
	public function __destruct()
	{
		if ($this->_memcached instanceof Memcache)
		{
			$this->_memcached->close();
		}
		elseif ($this->_memcached instanceof Memcached && method_exists($this->_memcached, 'quit'))
		{
			$this->_memcached->quit();
		}
	}
}
