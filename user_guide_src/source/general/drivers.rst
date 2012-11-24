#########################
Using CodeIgniter Drivers
#########################

Drivers are a special type of Library that has a parent class and any
number of potential child classes. Child classes have access to the
parent class, but not their siblings. Drivers provide an elegant syntax
in your :doc:`controllers <controllers>` for libraries that benefit
from or require being broken down into discrete classes.

Drivers are found in the *system/libraries/* directory, in their own
sub-directory which is identically named to the parent library class.
Also inside that directory is a subdirectory named drivers, which
contains all of the possible child class files.

To use a driver you will initialize it within a controller using the
following initialization method::

	$this->load->driver('class_name');

Where class name is the name of the driver class you want to invoke. For
example, to load a driver named "Some_parent" you would do this::

	$this->load->driver('some_parent');

Methods of that class can then be invoked with::

	$this->some_parent->some_method();

The child classes, the drivers themselves, can then be called directly
through the parent class, without initializing them::

	$this->some_parent->child_one->some_method();
	$this->some_parent->child_two->another_method();

Creating Your Own Drivers
=========================

Please read the section of the user guide that discusses how to :doc:`create
your own drivers <creating_drivers>`.