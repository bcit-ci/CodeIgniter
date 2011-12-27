#############################
Upgrading from 2.1.0 to 3.0.0
#############################

.. note:: These upgrade notes are for a version that is yet to be released.


Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your "system" folder and replace
your index.php file. If any modifications were made to your index.php
they will need to be made fresh in this new one.

Step 2: Change References to the SHA Library
============================================

The previously deprecated SHA library has been removed in CodeIgniter 3.0.
Alter your code to use the native `sha1()` PHP function to generate a sha1 hash.

Additionally, the `sha1()` method in the :doc:`Encryption Library <../libraries/encryption>` has been removed.


.. note:: If you have any custom developed files in these folders please
	make copies of them first.
