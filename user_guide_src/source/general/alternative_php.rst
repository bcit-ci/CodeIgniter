###################################
Alternate PHP Syntax for View Files
###################################

If you do not utilize CodeIgniter's :doc:`template
engine <../libraries/parser>`, you'll be using pure PHP in your
View files. To minimize the PHP code in these files, and to make it
easier to identify the code blocks it is recommended that you use PHPs
alternative syntax for control structures and short tag echo statements.
If you are not familiar with this syntax, it allows you to eliminate the
braces from your code, and eliminate "echo" statements.

Automatic Short Tag Support
===========================

.. note:: If you find that the syntax described in this page does not
	work on your server it might be that "short tags" are disabled in your
	PHP ini file. CodeIgniter will optionally rewrite short tags on-the-fly,
	allowing you to use that syntax even if your server doesn't support it.
	This feature can be enabled in your *config/config.php* file.

Please note that if you do use this feature, if PHP errors are
encountered in your **view files**, the error message and line number
will not be accurately shown. Instead, all errors will be shown as
``eval()`` errors.

Alternative Echos
=================

Normally to echo, or print out a variable you would do this::

	<?php echo $variable; ?>

With the alternative syntax you can instead do it this way::

	<?=$variable?>

Alternative Control Structures
==============================

Controls structures, like if, for, foreach, and while can be written in
a simplified format as well. Here is an example using ``foreach``::

	<ul>

	<?php foreach ($todo as $item): ?>

		<li><?=$item?></li>

	<?php endforeach; ?>

	</ul>

Notice that there are no braces. Instead, the end brace is replaced with
``endforeach``. Each of the control structures listed above has a similar
closing syntax: ``endif``, ``endfor``, ``endforeach``, and ``endwhile``

Also notice that instead of using a semicolon after each structure
(except the last one), there is a colon. This is important!

Here is another example, using ``if``/``elseif``/``else``. Notice the colons::

	<?php if ($username === 'sally'): ?>

		<h3>Hi Sally</h3>

	<?php elseif ($username === 'joe'): ?>

		<h3>Hi Joe</h3>

	<?php else: ?>

		<h3>Hi unknown user</h3>

	<?php endif; ?>