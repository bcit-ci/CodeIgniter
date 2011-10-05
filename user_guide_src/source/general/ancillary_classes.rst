##########################
Creating Ancillary Classes
##########################

In some cases you may want to develop classes that exist apart from your
controllers but have the ability to utilize all of CodeIgniter's
resources. This is easily possible as you'll see.

get_instance()
===============

**Any class that you instantiate within your controller functions can
access CodeIgniter's native resources** simply by using the
get_instance() function. This function returns the main CodeIgniter
object.

Normally, to call any of the available CodeIgniter functions requires
you to use the $this construct::

	$this->load->helper('url');
	$this->load->library('session');
	$this->config->item('base_url');
	// etc.

$this, however, only works within your controllers, your models, or your
views. If you would like to use CodeIgniter's classes from within your
own custom classes you can do so as follows:

First, assign the CodeIgniter object to a variable::

	$CI =& get_instance();

Once you've assigned the object to a variable, you'll use that variable
*instead* of $this::

	$CI =& get_instance();

	$CI->load->helper('url');
	$CI->load->library('session');
	$CI->config->item('base_url');
	// etc.

.. note:: You'll notice that the above get_instance() function is being
	passed by reference::

		$CI =& get_instance();
	
	This is very important. Assigning by reference allows you to use the
	original CodeIgniter object rather than creating a copy of it.
