#############################
Upgrading from 1.5.0 to 1.5.2
#############################

.. note:: The instructions on this page assume you are running version
	1.5.0 or 1.5.1. If you have not upgraded to that version please do so
	first.

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace these files and directories in your "system" folder with the new
versions:

-  system/helpers/download_helper.php
-  system/helpers/form_helper.php
-  system/libraries/Table.php
-  system/libraries/User_agent.php
-  system/libraries/Exceptions.php
-  system/libraries/Input.php
-  system/libraries/Router.php
-  system/libraries/Loader.php
-  system/libraries/Image_lib.php
-  system/language/english/unit_test_lang.php
-  system/database/DB_active_rec.php
-  system/database/drivers/mysqli/mysqli_driver.php
-  codeigniter/

.. note:: If you have any custom developed files in these folders please
	make copies of them first.

Step 2: Update your user guide
==============================

Please also replace your local copy of the user guide with the new
version.
