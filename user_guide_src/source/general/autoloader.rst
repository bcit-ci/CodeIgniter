######################
Auto-loading Resources
######################

CodeIgniter comes with an "Auto-load" feature that permits most
elements to be initialized automatically every time the
system runs. If you need certain resources globally throughout your
application you should consider auto-loading them for convenience.

The following items can be loaded automatically (in this order):

-  Package Paths to include when loading (and autoloading) elements
-  Custom config files found in the "config" folder
-  Helper files found in the "helpers" folder
-  Language files found in the "system/language" folder
-  Library or Driver classes found in the "libraries" folder
-  Controllers found in the "controllers" folder
-  Models found in the "models" folder

Models and Controllers may also be autoloaded from your configured
:ref:`Module Path <hmvc-modules>`.

To autoload resources, open the application/config/autoload.php file and
add the item you want loaded to the autoload array. You'll find
instructions in that file corresponding to each type of item.

.. note:: Do not include the file extension (.php) when adding items to
	the autoload array.
