##############
Caching Driver
##############

CodeIgniter features wrappers around some of the most popular forms of
fast and dynamic caching. All but file-based caching require specific
server requirements, and a Fatal Exception will be thrown if server
requirements are not met.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

*************
Example Usage
*************

The following example will load the cache driver, specify `APC <#alternative-php-cache-apc-caching>`_
as the driver to use, and fall back to file-based caching if APC is not
available in the hosting environment.

::

	$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));

	if ( ! $foo = $this->cache->get('foo'))
	{
		echo 'Saving to the cache!<br />';
		$foo = 'foobarbaz!';

		// Save into the cache for 5 minutes
		$this->cache->save('foo', $foo, 300);
	}

	echo $foo;

You can also prefix cache item names via the **key_prefix** setting, which is useful
to avoid collisions when you're running multiple applications on the same environment.

::

	$this->load->driver('cache',
		array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'my_')
	);

	$this->cache->get('foo'); // Will get the cache entry named 'my_foo'

***************
Class Reference
***************

.. php:class:: CI_Cache

	.. php:method:: is_supported($driver)

		:param	string	$driver: the name of the caching driver
		:returns:	TRUE if supported, FALSE if not
		:rtype:	bool

		This method is automatically called when accessing drivers via
		``$this->cache->get()``. However, if the individual drivers are used,
		make sure to call this method to ensure the driver is supported in the
		hosting environment.
		::

			if ($this->cache->apc->is_supported())
			{
				if ($data = $this->cache->apc->get('my_cache'))
				{
					// do things.
				}
			}

	.. php:method:: get($id)

		:param	string	$id: Cache item name
		:returns:	Item value or FALSE if not found
		:rtype:	mixed

		This method will attempt to fetch an item from the cache store. If the
		item does not exist, the method will return FALSE.
		::

			$foo = $this->cache->get('my_cached_item');

	.. php:method:: save($id, $data[, $ttl = 60[, $raw = FALSE]])

		:param	string	$id: Cache item name
		:param	mixed	$data: the data to save
		:param	int	$ttl: Time To Live, in seconds (default 60)
		:param	bool	$raw: Whether to store the raw value
		:returns:	TRUE on success, FALSE on failure
		:rtype:	string

		This method will save an item to the cache store. If saving fails, the
		method will return FALSE.
		::

			$this->cache->save('cache_item_id', 'data_to_cache');

		.. note:: The ``$raw`` parameter is only utilized by APC and Memcache,
			in order to allow usage of ``increment()`` and ``decrement()``.

	.. php:method:: delete($id)

		:param	string	$id: name of cached item
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		This method will delete a specific item from the cache store. If item
		deletion fails, the method will return FALSE.
		::

			$this->cache->delete('cache_item_id');

	.. php:method:: increment($id[, $offset = 1])

		:param	string	$id: Cache ID
		:param	int	$offset: Step/value to add
		:returns:	New value on success, FALSE on failure
		:rtype:	mixed

		Performs atomic incrementation of a raw stored value.
		::

			// 'iterator' has a value of 2

			$this->cache->increment('iterator'); // 'iterator' is now 3

			$this->cache->increment('iterator', 3); // 'iterator' is now 6

	.. php:method:: decrement($id[, $offset = 1])

		:param	string	$id: Cache ID
		:param	int	$offset: Step/value to reduce by
		:returns:	New value on success, FALSE on failure
		:rtype:	mixed

		Performs atomic decrementation of a raw stored value.
		::

			// 'iterator' has a value of 6

			$this->cache->decrement('iterator'); // 'iterator' is now 5

			$this->cache->decrement('iterator', 2); // 'iterator' is now 3

	.. php:method:: clean()

		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		This method will 'clean' the entire cache. If the deletion of the
		cache files fails, the method will return FALSE.
		::

			$this->cache->clean();

	.. php:method:: cache_info()

		:returns:	Information on the entire cache database
		:rtype:	mixed

		This method will return information on the entire cache.
		::

			var_dump($this->cache->cache_info());

		.. note:: The information returned and the structure of the data is dependent
			on which adapter is being used.

	.. php:method:: get_metadata($id)

		:param	string	$id: Cache item name
		:returns:	Metadata for the cached item
		:rtype:	mixed

		This method will return detailed information on a specific item in the
		cache.
		::

			var_dump($this->cache->get_metadata('my_cached_item'));

		.. note:: The information returned and the structure of the data is dependent
			on which adapter is being used.

*******
Drivers
*******

Alternative PHP Cache (APC) Caching
===================================

All of the methods listed above can be accessed without passing a
specific adapter to the driver loader as follows::

	$this->load->driver('cache');
	$this->cache->apc->save('foo', 'bar', 10);

For more information on APC, please see
`http://php.net/apc <http://php.net/apc>`_.

File-based Caching
==================

Unlike caching from the Output Class, the driver file-based caching
allows for pieces of view files to be cached. Use this with care, and
make sure to benchmark your application, as a point can come where disk
I/O will negate positive gains by caching.

All of the methods listed above can be accessed without passing a
specific adapter to the driver loader as follows::

	$this->load->driver('cache');
	$this->cache->file->save('foo', 'bar', 10);

Memcached Caching
=================

Multiple Memcached servers can be specified in the memcached.php
configuration file, located in the _application/config/* directory.

All of the methods listed above can be accessed without passing a
specific adapter to the driver loader as follows::

	$this->load->driver('cache');
	$this->cache->memcached->save('foo', 'bar', 10);

For more information on Memcached, please see
`http://php.net/memcached <http://php.net/memcached>`_.

WinCache Caching
================

Under Windows, you can also utilize the WinCache driver.

All of the methods listed above can be accessed without passing a
specific adapter to the driver loader as follows::

	$this->load->driver('cache');
	$this->cache->wincache->save('foo', 'bar', 10);

For more information on WinCache, please see
`http://php.net/wincache <http://php.net/wincache>`_.

Redis Caching
=============

Redis is an in-memory key-value store which can operate in LRU cache mode. 
To use it, you need `Redis server and phpredis PHP extension <https://github.com/phpredis/phpredis>`_.

Config options to connect to redis server must be stored in the application/config/redis.php file.
Available options are::
	
	$config['host'] = '127.0.0.1';
	$config['password'] = NULL;
	$config['port'] = 6379;
	$config['timeout'] = 0;

All of the methods listed above can be accessed without passing a
specific adapter to the driver loader as follows::

	$this->load->driver('cache');
	$this->cache->redis->save('foo', 'bar', 10);

For more information on Redis, please see
`http://redis.io <http://redis.io>`_.

Dummy Cache
===========

This is a caching backend that will always 'miss.' It stores no data,
but lets you keep your caching code in place in environments that don't
support your chosen cache.