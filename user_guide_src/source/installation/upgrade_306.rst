#############################
Upgrading from 3.0.5 to 3.0.6
#############################

Before performing an update you should take your site offline by
replacing the index.php file with a static one.

Step 1: Update your CodeIgniter files
=====================================

Replace all files and directories in your *system/* directory.

.. note:: If you have any custom developed files in these directories,
	please make copies of them first.

Step 2: Update your index.php file (optional)
=============================================

We've made some tweaks to the index.php file, mostly related to proper
usage of directory separators (i.e. use the ``DIRECTORY_SEPARATOR``
constant instead of a hard coded forward slash "/").

Nothing will break if you skip this step, but if you're running Windows
or just want to be up to date with every change - we do recommend that
you update your index.php file.

*Tip: Just copy the ``ENVIRONMENT``, ``$system_path``, ``$application_folder``
and ``$view_folder`` declarations from the old file and put them into the
new one, replacing the defaults.*

Step 3: Remove 'prep_for_form' usage (deprecation)
==================================================

The :doc:`Form Validation Library <../libraries/form_validation>` has a
``prep_for_form()`` method, which is/can also be used as a rule in
``set_rules()`` to automatically perform HTML encoding on input data.

Automatically encoding input (instead of output) data is a bad practice in
the first place, and CodeIgniter and PHP itself offer other alternatives
to this method anyway.
For example, :doc:`Form Helper <../helpers/form_helper>` functions will
automatically perform HTML escaping when necessary.

Therefore, the *prep_for_form* method/rule is pretty much useless and is now
deprecated and scheduled for removal in 3.1+.

.. note:: The method is still available, but you're strongly encouraged to
	remove its usage sooner rather than later.
