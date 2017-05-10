######################
Auto-loading Resources
######################

CodeIgniter comes with an "Auto-load" feature that permits libraries,
helpers, and models to be initialized automatically every time the
system runs. If you need certain resources globally throughout your
application you should consider auto-loading them for convenience.

The following items can be loaded automatically:

-  Classes found in the *libraries/* directory
-  Helper files found in the *helpers/* directory
-  Custom config files found in the *config/* directory
-  Language files found in the *system/language/* directory
-  Models found in the *models/* folder

To autoload resources, open the **application/config/autoload.php**
file and add the item you want loaded to the autoload array. You'll
find instructions in that file corresponding to each type of item.

.. note:: Do not include the file extension (.php) when adding items to
	the autoload array.

Additionally, if you want CodeIgniter to use a `Composer <https://getcomposer.org/>`_
auto-loader, just set ``$config['composer_autoload']`` to ``TRUE`` or
a custom path in **application/config/config.php**.