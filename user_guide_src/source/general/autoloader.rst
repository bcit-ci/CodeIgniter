######################
Auto-loading Resources
######################

CodeIgniter comes with an "Auto-load" feature that permits libraries,
helpers, and models to be initialized automatically every time the
system runs. If you need certain resources globally throughout your
application you should consider auto-loading them for convenience.

The following items can be loaded automatically:

-  Core classes found in the "libraries" folder
-  Helper files found in the "helpers" folder
-  Custom config files found in the "config" folder
-  Language files found in the "system/language" folder
-  Models found in the "models" folder

To autoload resources, open the application/config/autoload.php file and
add the item you want loaded to the autoload array. You'll find
instructions in that file corresponding to each type of item.

.. note:: Do not include the file extension (.php) when adding items to
	the autoload array.
