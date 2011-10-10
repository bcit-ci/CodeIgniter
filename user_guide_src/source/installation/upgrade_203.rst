#############################
Upgrading from 2.0.2 to 2.0.3
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

Step 2: Update your main index.php file
=======================================

If you are running a stock index.php file simply replace your version
with the new one.

If your index.php file has internal modifications, please add your
modifications to the new file and use it.

Step 3: Replace config/user_agents.php
=======================================

This config file has been updated to contain more user agent types,
please copy it to application/config/user_agents.php.

Step 4: Change references of the EXT constant to ".php"
=======================================================

.. note:: The EXT Constant has been marked as deprecated, but has not
	been removed from the application. You are encouraged to make the
	changes sooner rather than later.

Step 5: Remove APPPATH.'third_party' from autoload.php
=======================================================

Open application/config/autoload.php, and look for the following::

	$autoload['packages'] = array(APPPATH.'third_party');

If you have not chosen to load any additional packages, that line can be
changed to::

	$autoload['packages'] = array();

Which should provide for nominal performance gains if not autoloading
packages.

Update Sessions Database Tables
===============================

If you are using database sessions with the CI Session Library, please
update your ci_sessions database table as follows::

	CREATE INDEX last_activity_idx ON ci_sessions(last_activity);
	ALTER TABLE ci_sessions MODIFY user_agent VARCHAR(120);

