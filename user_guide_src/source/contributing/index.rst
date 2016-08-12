###########################
Contributing to CodeIgniter
###########################

.. toctree::
	:titlesonly:

	../documentation/index
	../DCO

CodeIgniter is a community driven project and accepts contributions of code
and documentation from the community. These contributions are made in the form
of Issues or `Pull Requests <https://help.github.com/articles/using-pull-requests/>`_ 
on the `CodeIgniter repository <https://github.com/bcit-ci/CodeIgniter>`_ on GitHub.

Issues are a quick way to point out a bug. If you find a bug or documentation
error in CodeIgniter then please check a few things first:

- There is not already an open Issue
- The issue has already been fixed (check the develop branch, or look for
  closed Issues)
- Is it something really obvious that you fix it yourself?

Reporting issues is helpful but an even better approach is to send a Pull
Request, which is done by "Forking" the main repository and committing to your
own copy. This will require you to use the version control system called Git.

*******
Support
*******

Please note that GitHub is not for general support questions! If you are
having trouble using a feature of CodeIgniter, ask for help on our
`forums <http://forum.codeigniter.com/>`_ instead.

If you are not sure whether you are using something correctly or if you
have found a bug, again - please ask on the forums first.

********
Security
********

Did you find a security issue in CodeIgniter?

Please *don't* disclose it publicly, but e-mail us at security@codeigniter.com,
or report it via our page on `HackerOne <https://hackerone.com/codeigniter>`_.

If you've found a critical vulnerability, we'd be happy to credit you in our
`ChangeLog <../changelog>`.

****************************
Tips for a Good Issue Report
****************************

Use a descriptive subject line (eg parser library chokes on commas) rather than a vague one (eg. your code broke).

Address a single issue in a report.

Identify the CodeIgniter version (eg 3.0-develop) and the component if you know it (eg. parser library)

Explain what you expected to happen, and what did happen.
Include error messages and stacktrace, if any.

Include short code segments if they help to explain.
Use a pastebin or dropbox facility to include longer segments of code or screenshots - do not include them in the issue report itself.
This means setting a reasonable expiry for those, until the issue is resolved or closed.

If you know how to fix the issue, you can do so in your own fork & branch, and submit a pull request.
The issue report information above should be part of that.

If your issue report can describe the steps to reproduce the problem, that is great.
If you can include a unit test that reproduces the problem, that is even better, as it gives whoever is fixing
it a clearer target!


**********
Guidelines
**********

Before we look into how, here are the guidelines. If your Pull Requests fail
to pass these guidelines it will be declined and you will need to re-submit
when you’ve made the changes. This might sound a bit tough, but it is required
for us to maintain quality of the code-base.

PHP Style
=========

All code must meet the `Style Guide
<https://codeigniter.com/userguide3/general/styleguide.html>`_, which is
essentially the `Allman indent style
<https://en.wikipedia.org/wiki/Indent_style#Allman_style>`_, underscores and
readable operators. This makes certain that all code is the same format as the
existing code and means it will be as readable as possible.

Documentation
=============

If you change anything that requires a change to documentation then you will
need to add it. New classes, methods, parameters, changing default values, etc
are all things that will require a change to documentation. The change-log
must also be updated for every change. Also PHPDoc blocks must be maintained.

Compatibility
=============

CodeIgniter recommends PHP 5.6 or newer to be used, but it should be
compatible with PHP 5.3.7 so all code supplied must stick to this
requirement. If PHP 5.4 (and above) functions or features are used then
there must be a fallback for PHP 5.3.7.

Branching
=========

CodeIgniter uses the `Git-Flow
<http://nvie.com/posts/a-successful-git-branching-model/>`_ branching model
which requires all pull requests to be sent to the "develop" branch. This is
where the next planned version will be developed. The "master" branch will
always contain the latest stable version and is kept clean so a "hotfix" (e.g:
an emergency security patch) can be applied to master to create a new version,
without worrying about other features holding it up. For this reason all
commits need to be made to "develop" and any sent to "master" will be closed
automatically. If you have multiple changes to submit, please place all
changes into their own branch on your fork.

One thing at a time: A pull request should only contain one change. That does
not mean only one commit, but one change - however many commits it took. The
reason for this is that if you change X and Y but send a pull request for both
at the same time, we might really want X but disagree with Y, meaning we
cannot merge the request. Using the Git-Flow branching model you can create
new branches for both of these features and send two requests.

Signing
=======
You must sign your work, certifying that you either wrote the work or
otherwise have the right to pass it on to an open source project. git makes
this trivial as you merely have to use `--signoff` on your commits to your
CodeIgniter fork.

.. code-block:: bash

	git commit --signoff

or simply

.. code-block:: bash

	git commit -s

This will sign your commits with the information setup in your git config, e.g.

	Signed-off-by: John Q Public <john.public@example.com>

If you are using Tower there is a "Sign-Off" checkbox in the commit window. You
could even alias git commit to use the -s flag so you don’t have to think about
it.

By signing your work in this manner, you certify to a "Developer's Certificate
of Origin". The current version of this certificate is in the :doc:`/DCO` file
in the root of this documentation.
