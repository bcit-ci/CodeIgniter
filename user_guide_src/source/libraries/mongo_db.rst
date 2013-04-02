#############
MongoDB Class
#############

CodeIgniter's robust MongoDB Class allows you to easily integrate your Mongo
database into your projects.

Database Configuration
======================

The config settings are stored in the config file ``mongo_db.php`` with this
prototype::

	$config['active_group'] = 'default';

	$config['default'] = array(
		'dsn'		=> '',
		'hostname'	=> 'localhost',
		'port'		=> 27017,
		'username'	=> '',
		'password'	=> '',
		'database'	=> '',
		'host_db_flag'	=> FALSE,
		'db_debug'		=> FALSE,
		'result_set'	=> 'array',
		'options'	=> array(
			'write_concern'	=> 1,
			'fsync'			=> FALSE,
			'timeout'		=> 10000
		),
		'autoinit'	=> TRUE
	);

Thus there can be any number of configurations selectable by ``$config['active_group']``.

The following is a list of all the preferences that can be set.

============================= ====================== ============================ ================================================================
Preference                    Default Value          Options                      Description
============================= ====================== ============================ ================================================================
**dsn**                       No Default             None                         The DSN connect string (an all-in-one configuration sequence). 
                                                                                  Leave this blank to auto-generate the string.
**hostname**                  localhost              None                         The hostname of your database server. Often this is 'localhost'.
**port**                      27017                  None                         The port to connect to the database.
**username**                  No Default             None                         The username used to connect to the database.
**password**                  No Default             None                         The password used to connect to the database.
**database**                  No Default             None                         The name of the database you want to connect to.
**host_db_flag**              No Default             None                         Whether to append database flag while building the DSN.
**db_debug**                  TRUE                   TRUE or FALSE (boolean)      Whether database errors should be displayed.
**result_set**                array                  array or object              The return format of the result set.
**options['write_concern']**  FALSE                  TRUE or FALSE (boolean)      Extent of durable writes. 
                                                                                  See http://www.php.net/manual/en/mongo.writeconcerns.php
**options['fsync']**          No Default             TRUE or FALSE (boolean)      Insert synced to disk. 
                                                                                  See http://www.php.net/manual/en/mongocollection.insert.php
**options['timeout']**        TRUE                   None                         Client-side timeout. 
                                                                                  See http://www.php.net/manual/en/mongocursor.timeout.php
**autoinit**                  TRUE                   TRUE or FALSE (boolean)      Whether or not to automatically connect to the database when the 
                                                                                  library loads. If set to false, connection must be established
                                                                                  by calling ``$this->mongo_db->connect()``
============================= ====================== ============================ ================================================================

Setting Configuration in a Config File
---------------------------------------

If you prefer not to set preferences using the above method, you can
pass these as an array to the ``$this->mongo_db->initialize()`` method. 

Selecting Documents
===================

$this->mongo_db->find_one()
---------------------------

Retriev a single document from a collection::

	$this->mongo_db->find_one('mycollection', array(
		'_id' => new MongoId('513a0d2e80fd6f0b2ed4f870)
	));

$this->mongo_db->find()
-----------------------

Retrieve all documents from a collection::

	$this->mongo_db->find('mycollection');

The second parameter enable you to set a where condition::

	$this->mongo_db->find('mycollection', array(
		'name'	=> 'John',
		'age'	=> 22
	));

	The third parameter enable you to select specific fields::

	$this->mongo_db->find('mycollection', array(
		'name'	=> 'John',
		'age'	=> 22
	), array(
		'_id' => -1,
		'occupation' => 1,
	));

..note:: ``find()`` only builds the cursor object, the records will be 
	returned by calling the ``result()`` method or its variants 
	(``$this->mongo_db->find('mycollection')->result()``)

find_and_modify()
-----------------

Retrieve a single document from a collection while modifying it at the same 
time::

	$this->mongo_db->find_and_modify('mycollection', array(
		'name' => 'John',
	), array(
		'$set' => array(
			'age' => 25
		)
	));

Handling Errors
===============

If the previous query failed for some reason, it can be obtained via 
``$this->mongo_db->last_error_message()`` and ``$this->mongo_db->last_error_code()`` 
methods ::

	if($this->mongo_db->insert('users', $data))
	{
		echo 'User added with id:', $this->mongo_db->insert_id();
	}
	else
	{
		if($this->mongo_db->last_error_code() === 11000)
			echo 'Username already exists';
		else
			echo 'Unknown error:', $this->mongo_db->last_error_message();
	}