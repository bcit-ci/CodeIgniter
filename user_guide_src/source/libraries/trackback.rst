###############
Trackback Class
###############

The Trackback Class provides functions that enable you to send and
receive Trackback data.

If you are not familiar with Trackbacks you'll find more information
`here <http://en.wikipedia.org/wiki/Trackback>`_.

Initializing the Class
======================

Like most other classes in CodeIgniter, the Trackback class is
initialized in your controller using the $this->load->library function::

	$this->load->library('trackback');

Once loaded, the Trackback library object will be available using:
$this->trackback

Sending Trackbacks
==================

A Trackback can be sent from any of your controller functions using code
similar to this example::

	$this->load->library('trackback');

	$tb_data = array(
	                'ping_url'  => 'http://example.com/trackback/456',
	                'url'       => 'http://www.my-example.com/blog/entry/123',
	                'title'     => 'The Title of My Entry',
	                'excerpt'   => 'The entry content.',
	                'blog_name' => 'My Blog Name',
	                'charset'   => 'utf-8'
	                );

	if ( ! $this->trackback->send($tb_data))
	{
	     echo $this->trackback->display_errors();
	}
	else
	{
	     echo 'Trackback was sent!';
	}

Description of array data:

-  **ping_url** - The URL of the site you are sending the Trackback to.
   You can send Trackbacks to multiple URLs by separating each URL with
   a comma.
-  **url** - The URL to YOUR site where the weblog entry can be seen.
-  **title** - The title of your weblog entry.
-  **excerpt** - The content of your weblog entry. Note: the Trackback
   class will automatically send only the first 500 characters of your
   entry. It will also strip all HTML.
-  **blog_name** - The name of your weblog.
-  **charset** - The character encoding your weblog is written in. If
   omitted, UTF-8 will be used.

The Trackback sending function returns TRUE/FALSE (boolean) on success
or failure. If it fails, you can retrieve the error message using::

	$this->trackback->display_errors();

Receiving Trackbacks
====================

Before you can receive Trackbacks you must create a weblog. If you don't
have a blog yet there's no point in continuing.

Receiving Trackbacks is a little more complex than sending them, only
because you will need a database table in which to store them, and you
will need to validate the incoming trackback data. You are encouraged to
implement a thorough validation process to guard against spam and
duplicate data. You may also want to limit the number of Trackbacks you
allow from a particular IP within a given span of time to further
curtail spam. The process of receiving a Trackback is quite simple; the
validation is what takes most of the effort.

Your Ping URL
=============

In order to accept Trackbacks you must display a Trackback URL next to
each one of your weblog entries. This will be the URL that people will
use to send you Trackbacks (we will refer to this as your "Ping URL").

Your Ping URL must point to a controller function where your Trackback
receiving code is located, and the URL must contain the ID number for
each particular entry, so that when the Trackback is received you'll be
able to associate it with a particular entry.

For example, if your controller class is called Trackback, and the
receiving function is called receive, your Ping URLs will look something
like this::

	http://example.com/index.php/trackback/receive/entry_id

Where entry_id represents the individual ID number for each of your
entries.

Creating a Trackback Table
==========================

Before you can receive Trackbacks you must create a table in which to
store them. Here is a basic prototype for such a table::

	CREATE TABLE trackbacks (
	 tb_id int(10) unsigned NOT NULL auto_increment,
	 entry_id int(10) unsigned NOT NULL default 0,
	 url varchar(200) NOT NULL,
	 title varchar(100) NOT NULL,
	 excerpt text NOT NULL,
	 blog_name varchar(100) NOT NULL,
	 tb_date int(10) NOT NULL,
	 ip_address varchar(45) NOT NULL,
	 PRIMARY KEY `tb_id` (`tb_id`),
	 KEY `entry_id` (`entry_id`)
	);

The Trackback specification only requires four pieces of information to
be sent in a Trackback (url, title, excerpt, blog_name), but to make
the data more useful we've added a few more fields in the above table
schema (date, IP address, etc.).

Processing a Trackback
======================

Here is an example showing how you will receive and process a Trackback.
The following code is intended for use within the controller function
where you expect to receive Trackbacks.

::

	$this->load->library('trackback');
	$this->load->database();

	if ($this->uri->segment(3) == FALSE)
	{
	    $this->trackback->send_error("Unable to determine the entry ID");
	}

	if ( ! $this->trackback->receive())
	{
	    $this->trackback->send_error("The Trackback did not contain valid data");
	}

	$data = array(
	                'tb_id'      => '',
	                'entry_id'   => $this->uri->segment(3),
	                'url'        => $this->trackback->data('url'),
	                'title'      => $this->trackback->data('title'),
	                'excerpt'    => $this->trackback->data('excerpt'),
	                'blog_name'  => $this->trackback->data('blog_name'),
	                'tb_date'    => time(),
	                'ip_address' => $this->input->ip_address()
	                );

	$sql = $this->db->insert_string('trackbacks', $data);
	$this->db->query($sql);

	$this->trackback->send_success();

Notes:
^^^^^^

The entry ID number is expected in the third segment of your URL. This
is based on the URI example we gave earlier::

	http://example.com/index.php/trackback/receive/entry_id

Notice the entry_id is in the third URI segment, which you can retrieve
using::

	$this->uri->segment(3);

In our Trackback receiving code above, if the third segment is missing,
we will issue an error. Without a valid entry ID, there's no reason to
continue.

The $this->trackback->receive() function is simply a validation function
that looks at the incoming data and makes sure it contains the four
pieces of data that are required (url, title, excerpt, blog_name). It
returns TRUE on success and FALSE on failure. If it fails you will issue
an error message.

The incoming Trackback data can be retrieved using this function::

	$this->trackback->data('item')

Where item represents one of these four pieces of info: url, title,
excerpt, or blog_name

If the Trackback data is successfully received, you will issue a success
message using::

	$this->trackback->send_success();

.. note:: The above code contains no data validation, which you are
	encouraged to add.
