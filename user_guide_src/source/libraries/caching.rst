##############
Caching Driver
##############

CodeIgniter features wrappers around some of the most popular forms of
fast and dynamic caching. All but file-based caching require specific
server requirements, and a Fatal Exception will be thrown if server
requirements are not met.

.. contents:: Table of Contents

*************
Example Usage
*************

The following example will load the cache driver, specify `APC <#apc>`_
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

******************
Function Reference
******************

.. php:class:: CI_Cache

is_supported()
==============

	.. php:method:: is_supported ( $driver )

		This function is automatically called when accessing drivers via
		$this->cache->get(). However, if the individual drivers are used, make
		sure to call this function to ensure the driver is supported in the
		hosting environment.
		
		:param string $driver: the name of the caching driver
		:returns: TRUE if supported, FALSE if not
		:rtype: Boolean
		
		::
				
			if ($this->cache->apc->is_supported()
			{
				if ($data = $this->cache->apc->get('my_cache'))
				{
					// do things.
				}
			}


get()
=====

	.. php:method:: get ( $id )
	
		This function will attempt to fetch an item from the cache store. If the
		item does not exist, the function will return FALSE.

		:param string $id: name of cached item
		:returns: The item if it exists, FALSE if it does not
		:rtype: Mixed
		
		::

			$foo = $this->cache->get('my_cached_item');


save()
======

	.. php:method:: save ( $id , $data [, $ttl])
	
		This function will save an item to the cache store. If saving fails, the
		function will return FALSE.

		:param string $id: name of the cached item
		:param mixed $data: the data to save
		:param int $ttl: Time To Live, in seconds (default 60)
		:returns: TRUE on success, FALSE on failure
		:rtype: Boolean

		::

			$this->cache->save('cache_item_id', 'data_to_cache');
	
delete()
========

	.. php:method:: delete ( $id )
	
		This function will delete a specific item from the cache store. If item
		deletion fails, the function will return FALSE.

		:param string $id: name of cached item
		:returns: TRUE if deleted, FALSE if the deletion fails
		:rtype: Boolean
		
		::

			$this->cache->delete('cache_item_id');

clean()
=======

	.. php:method:: clean ( )
	
		This function will 'clean' the entire cache. If the deletion of the
		cache files fails, the function will return FALSE.

		:returns: TRUE if deleted, FALSE if the deletion fails
		:rtype: Boolean
		
		::

			$this->cache->clean();

cache_info()
============

	.. php:method:: cache_info ( )

		This function will return information on the entire cache.

		:returns: information on the entire cache
		:rtype: Mixed
		
		::

			var_dump($this->cache->cache_info());
		
		.. note:: The information returned and the structure of the data is dependent
			on which adapter is being used.
	

get_metadata()
==============

	.. php:method:: get_metadata ( $id )
	
		This function will return detailed information on a specific item in the
		cache.
		
		:param string $id: name of cached item
		:returns: metadadta for the cached item
		:rtype: Mixed
		
		::

			var_dump($this->cache->get_metadata('my_cached_item'));

		.. note:: The information returned and the structure of the data is dependent
			on which adapter is being used.

*******
Drivers
*******

Alternative PHP Cache (APC) Caching
===================================

All of the functions listed above can be accessed without passing a
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

All of the functions listed above can be accessed without passing a
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

All of the functions listed above can be accessed without passing a
specific adapter to the driver loader as follows::

	$this->load->driver('cache');
	$this->cache->wincache->save('foo', 'bar', 10);

For more information on WinCache, please see
`http://php.net/wincache <http://php.net/wincache>`_.

Redis Caching
=============

All of the methods listed above can be accessed without passing a
specific adapter to the driver loader as follows::

	$this->load->driver('cache');
	$this->cache->redis->save('foo', 'bar', 10);

.. important:: Redis may require one or more of the following options:
	**host**, **post**, **timeout**, **password**.

The Redis PHP extension repository is located at
`https://github.com/nicolasff/phpredis <https://github.com/nicolasff/phpredis>`_.

Dummy Cache
===========

This is a caching backend that will always 'miss.' It stores no data,
but lets you keep your caching code in place in environments that don't
support your chosen cache.