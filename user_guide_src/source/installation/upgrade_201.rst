#############################
Upgrading from 2.0.0 to 2.0.1
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your "system" folder and replace
your index.php file. If any modifications were made to your index.php
they will need to be made fresh in this new one.

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Replace config/mimes.php
================================

This config file has been updated to contain more mime types, please
copy it to application/config/mimes.php.

Step 3: Check for forms posting to default controller
=====================================================

The default behavior for form_open() when called with no parameters
used to be to post to the default controller, but it will now just leave
an empty action="" meaning the form will submit to the current URL. If
submitting to the default controller was the expected behavior it will
need to be changed from::

	echo form_open(); //<form action="" method="post" accept-charset="utf-8">

to use either a / or base_url()::

	echo form_open('/'); //<form action="http://example.com/index.php/" method="post" accept-charset="utf-8">
	echo form_open(base_url()); //<form action="http://example.com/" method="post" accept-charset="utf-8">

