##########################
Managing your Applications
##########################

By default it is assumed that you only intend to use CodeIgniter to
manage one application, which you will build in your application/
directory. It is possible, however, to have multiple sets of
applications that share a single CodeIgniter installation, or even to
rename or relocate your application folder.

Renaming the Application Folder
===============================

If you would like to rename your application folder you may do so as
long as you open your main index.php file and set its name using the
$application_folder variable::

	$application_folder = "application";

Relocating your Application Folder
==================================

It is possible to move your application folder to a different location
on your server than your system folder. To do so open your main
index.php and set a *full server path* in the $application_folder
variable.

::

	$application_folder = "/Path/to/your/application";

Running Multiple Applications with one CodeIgniter Installation
===============================================================

If you would like to share a common CodeIgniter installation to manage
several different applications simply put all of the directories located
inside your application folder into their own sub-folder.

For example, let's say you want to create two applications, "foo" and
"bar". You could structure your application folders like this::

	applications/foo/
	applications/foo/config/
	applications/foo/controllers/
	applications/foo/errors/
	applications/foo/libraries/
	applications/foo/models/
	applications/foo/views/
	applications/bar/
	applications/bar/config/
	applications/bar/controllers/
	applications/bar/errors/
	applications/bar/libraries/
	applications/bar/models/
	applications/bar/views/

To select a particular application for use requires that you open your
main index.php file and set the $application_folder variable. For
example, to select the "foo" application for use you would do this::

	$application_folder = "applications/foo";

.. note:: Each of your applications will need its own index.php file
	which calls the desired application. The index.php file can be named
	anything you want.
