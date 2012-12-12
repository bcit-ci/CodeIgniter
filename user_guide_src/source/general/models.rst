######
Models
######

Models are **optionally** available for those who want to use a more
traditional MVC approach.

.. contents:: Page Contents

What is a Model?
================

Models are PHP classes that are designed to work with information in
your database. For example, let's say you use CodeIgniter to manage a
blog. You might have a model class that contains functions to insert,
update, and retrieve your blog data. Here is an example of what such a
model class might look like::

	class Blog_model extends CI_Model {

		public $title;
		public $content;
		public $date;

		public function __construct()
		{
			// Call the CI_Model constructor
			parent::__construct();
		}

		public function get_last_ten_entries()
		{
			$query = $this->db->get('entries', 10);
			return $query->result();
		}

		public function insert_entry()
		{
			$this->title	= $_POST['title']; // please read the below note
			$this->content	= $_POST['content'];
			$this->date	= time();

			$this->db->insert('entries', $this);
		}

		public function update_entry()
		{
			$this->title	= $_POST['title'];
			$this->content	= $_POST['content'];
			$this->date	= time();

			$this->db->update('entries', $this, array('id' => $_POST['id']));
		}

	}

.. note:: The methods in the above example use the :doc:`Query Builder
	<../database/query_builder>` database methods.

.. note:: For the sake of simplicity in this example we're using ``$_POST``
	directly. This is generally bad practice, and a more common approach
	would be to use the :doc:`Input Library <../libraries/input>`
	``$this->input->post('title')``.

Anatomy of a Model
==================

Model classes are stored in your **application/models/** directory.
They can be nested within sub-directories if you want this type of
organization.

The basic prototype for a model class is this::

	class Model_name extends CI_Model {

		public function __construct()
		{
			parent::__construct();
		}

	}

Where **Model_name** is the name of your class. Class names **must** have
the first letter capitalized with the rest of the name lowercase. Make
sure your class extends the base Model class.

The file name will be a lower case version of your class name. For
example, if your class is this::

	class User_model extends CI_Model {

		public function __construct()
		{
			parent::__construct();
		}

	}

Your file will be this::

	application/models/user_model.php

Loading a Model
===============

Your models will typically be loaded and called from within your
:doc:`controller <controllers>` methods. To load a model you will use
the following method::

	$this->load->model('model_name');

If your model is located in a sub-directory, include the relative path
from your models directory. For example, if you have a model located at
*application/models/blog/queries.php* you'll load it using::

	$this->load->model('blog/queries');

Once loaded, you will access your model methods using an object with the
same name as your class::

	$this->load->model('model_name');

	$this->model_name->method();

If you would like your model assigned to a different object name you can
specify it via the second parameter of the loading method::

	$this->load->model('model_name', 'foobar');

	$this->foobar->method();

Here is an example of a controller, that loads a model, then serves a
view::

	class Blog_controller extends CI_Controller {

		public function blog()
		{
			$this->load->model('blog');

			$data['query'] = $this->Blog->get_last_ten_entries();

			$this->load->view('blog', $data);
		}
	}
	

Auto-loading Models
===================

If you find that you need a particular model globally throughout your
application, you can tell CodeIgniter to auto-load it during system
initialization. This is done by opening the
**application/config/autoload.php** file and adding the model to the
autoload array.

Connecting to your Database
===========================

When a model is loaded it does **NOT** connect automatically to your
database. The following options for connecting are available to you:

-  You can connect using the standard database methods :doc:`described
   here <../database/connecting>`, either from within your
   Controller class or your Model class.
-  You can tell the model loading method to auto-connect by passing
   TRUE (boolean) via the third parameter, and connectivity settings,
   as defined in your database config file will be used::

	$this->load->model('model_name', '', TRUE);

-  You can manually pass database connectivity settings via the third
   parameter::

	$config['hostname'] = 'localhost';
	$config['username'] = 'myusername';
	$config['password'] = 'mypassword';
	$config['database'] = 'mydatabase';
	$config['dbdriver'] = 'mysqli';
	$config['dbprefix'] = '';
	$config['pconnect'] = FALSE;
	$config['db_debug'] = TRUE;

	$this->load->model('Model_name', '', $config);