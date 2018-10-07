#############################
Upgrading from 1.3.2 to 1.3.3
#############################

.. note:: The instructions on this page assume you are running version
	1.3.2. If you have not upgraded to that version please do so first.

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace the following directories in your "system" folder with the new
versions:

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

-  codeigniter
-  drivers
-  helpers
-  init
-  libraries

Step 2: Update your Models
==========================

If you are **NOT** using CodeIgniter's
:doc:`Models <../general/models>` feature disregard this step.

As of version 1.3.3, CodeIgniter does **not** connect automatically to
your database when a model is loaded. This allows you greater
flexibility in determining which databases you would like used with your
models. If your application is not connecting to your database prior to
a model being loaded you will have to update your code. There are
several options for connecting, :doc:`as described
here <../general/models>`.

Step 3: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
