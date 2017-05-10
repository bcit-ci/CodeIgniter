################
Creating Drivers
################

Driver Directory and File Structure
===================================

Sample driver directory and file structure layout:

-  /application/libraries/Driver_name

   -  Driver_name.php
   -  drivers

      -  Driver_name_subclass_1.php
      -  Driver_name_subclass_2.php
      -  Driver_name_subclass_3.php

.. note:: In order to maintain compatibility on case-sensitive
	file systems, the Driver_name directory must be
	named in the format returned by ``ucfirst()``.

.. note:: The Driver library's architecture is such that
	the subclasses don't extend and therefore don't inherit
	properties or methods of the main driver.