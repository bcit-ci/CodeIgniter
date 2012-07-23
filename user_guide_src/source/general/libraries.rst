###########################
Using CodeIgniter Libraries
###########################

All of the available libraries are located in your system/libraries
folder. In most cases, to use one of these classes involves initializing
it within a :doc:`controller <controllers>` using the following
initialization function::

	$this->load->library('class name'); 

Where class name is the name of the class you want to invoke. For
example, to load the form validation class you would do this::

	$this->load->library('form_validation'); 

Once initialized you can use it as indicated in the user guide page
corresponding to that class.

Additionally, multiple libraries can be loaded at the same time by
passing an array of libraries to the load function.

::

	$this->load->library(array('email', 'table'));

Creating Your Own Libraries
===========================

Please read the section of the user guide that discusses how to :doc:`create
your own libraries <creating_libraries>`
