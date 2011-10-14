############
Email Helper
############

The Email Helper provides some assistive functions for working with
Email. For a more robust email solution, see CodeIgniter's :doc:`Email
Class <../libraries/email>`.

.. contents:: Page Contents

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('email');


The following functions are available:

valid_email('email')
====================

Checks if an email is a correctly formatted email. Note that is doesn't
actually prove the email will recieve mail, simply that it is a validly
formed address.

It returns TRUE/FALSE

::

	$this->load->helper('email');

	if (valid_email('email@somesite.com'))
	{
		echo 'email is valid';
	}
	else
	{
		echo 'email is not valid';
	}

send_email('recipient', 'subject', 'message')
=============================================

Sends an email using PHP's native
`mail() <http://www.php.net/function.mail>`_ function. For a more robust
email solution, see CodeIgniter's :doc:`Email
Class <../libraries/email>`.
