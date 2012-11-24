############
Email Helper
############

The Email Helper provides some assistive functions for working with
Email. For a more robust email solution, see CodeIgniter's :doc:`Email
Class <../libraries/email>`.

.. contents:: Page Contents

.. important:: The Email helper is DEPRECATED.

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('email');

The following functions are available:

valid_email()
=============

.. php:function:: valid_email($email)

	:param	string	$email: Email address
	:returns:	bool

Checks if the input is a correctly formatted e-mail address. Note that is
doesn't actually prove that the address will be able recieve mail, but
simply that it is a validly formed address.

Example::

	if (valid_email('email@somesite.com'))
	{
		echo 'email is valid';
	}
	else
	{
		echo 'email is not valid';
	}

.. note:: All that this function does is to use PHP's native ``filter_var()``:
	|
	| (bool) filter_var($email, FILTER_VALIDATE_EMAIL);

send_email()
============

.. php:function:: send_email($recipient, $subject, $message)

	:param	string	$recipient: E-mail address
	:param	string	$subject: Mail subject
	:param	string	$message: Message body
	:returns:	bool

Sends an email using PHP's native `mail() <http://www.php.net/function.mail>`_
function.

.. note:: All that this function does is to use PHP's native ``mail``:
	|
	| mail($recipient, $subject, $message);

For a more robust email solution, see CodeIgniter's :doc:`Email Library
<../libraries/email>`.
