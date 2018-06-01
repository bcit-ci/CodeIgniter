###################
Running via the CLI
###################

As well as calling an applications :doc:`Controllers <./controllers>`
via the URL in a browser they can also be loaded via the command-line
interface (CLI).

.. contents:: Page Contents

What is the CLI?
================

The command-line interface is a text-based method of interacting with
computers. For more information, check the `Wikipedia
article <https://en.wikipedia.org/wiki/Command-line_interface>`_.

Why run via the command-line?
=============================

There are many reasons for running CodeIgniter from the command-line,
but they are not always obvious.

-  Run your cron-jobs without needing to use *wget* or *curl*
-  Make your cron-jobs inaccessible from being loaded in the URL by
   checking the return value of :php:func:`is_cli()`.
-  Make interactive "tasks" that can do things like set permissions,
   prune cache folders, run backups, etc.
-  Integrate with other applications in other languages. For example, a
   random C++ script could call one command and run code in your models!

Let's try it: Hello World!
==========================

Let's create a simple controller so you can see it in action. Using your
text editor, create a file called Tools.php, and put the following code
in it::

	<?php
	class Tools extends CI_Controller {

		public function message($to = 'World')
		{
			echo "Hello {$to}!".PHP_EOL;
		}
	}

Then save the file to your *application/controllers/* folder.

Now normally you would visit the site using a URL similar to this::

	example.com/index.php/tools/message/to

Instead, we are going to open the terminal in Mac/Linux or go to Run > "cmd"
in Windows and navigate to our CodeIgniter project.

.. code-block:: bash

	$ cd /path/to/project;
	$ php index.php tools message

If you did it right, you should see *Hello World!* printed.

.. code-block:: bash

	$ php index.php tools message "John Smith"

Here we are passing it a argument in the same way that URL parameters
work. "John Smith" is passed as a argument and output is::

	Hello John Smith!

That's it!
==========

That, in a nutshell, is all there is to know about controllers on the
command line. Remember that this is just a normal controller, so routing
and ``_remap()`` works fine.
