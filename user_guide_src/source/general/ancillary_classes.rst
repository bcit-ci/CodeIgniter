##########################
Creating Ancillary Classes
##########################

In some cases you may want to develop classes that exist apart from your
controllers but have the ability to utilize all of CodeIgniter's
resources. This is easily possible as you'll see.

get_instance()
==============

.. php:function:: get_instance()

	:returns:	Reference to your controller's instance
	:rtype:	CI_Controller

**Any class that you instantiate within your controller methods can
access CodeIgniter's native resources** simply by using the
``get_instance()`` function. This function returns the main
CodeIgniter object.

Normally, to call any of the available methods, CodeIgniter requires
you to use the ``$this`` construct::

	$this->load->helper('url');
	$this->load->library('session');
	$this->config->item('base_url');
	// etc.

``$this``, however, only works within your controllers, your models,
or your views. If you would like to use CodeIgniter's classes from
within your own custom classes you can do so as follows:

First, assign the CodeIgniter object to a variable::

	$CI =& get_instance();

Once you've assigned the object to a variable, you'll use that variable
*instead* of ``$this``::

	$CI =& get_instance();

	$CI->load->helper('url');
	$CI->load->library('session');
	$CI->config->item('base_url');
	// etc.

If you'll be using ``get_instance()`` inside another class, then it would
be better if you assign it to a property. This way, you won't need to call
``get_instance()`` in every single method.

Example::

	class Example {

		protected $CI;

		// We'll use a constructor, as you can't directly call a function
		// from a property definition.
		public function __construct()
		{
			// Assign the CodeIgniter super-object
			$this->CI =& get_instance();
		}

		public function foo()
		{
			$this->CI->load->helper('url');
			redirect();
		}

		public function bar()
		{
			$this->CI->config->item('base_url');
		}
	}

In the above example, both methods ``foo()`` and ``bar()`` will work
after you instantiate the Example class, without the need to call
``get_instance()`` in each of them.
