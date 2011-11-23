###########
Email Class
###########

CodeIgniter's robust Email Class supports the following features:

-  Multiple Protocols: Mail, Sendmail, and SMTP
-  TLS and SSL Encryption for SMTP
-  Multiple recipients
-  CC and BCCs
-  HTML or Plaintext email
-  Attachments
-  Word wrapping
-  Priorities
-  BCC Batch Mode, enabling large email lists to be broken into small
   BCC batches.
-  Email Debugging tools

Sending Email
=============

Sending email is not only simple, but you can configure it on the fly or
set your preferences in a config file.

Here is a basic example demonstrating how you might send email. Note:
This example assumes you are sending the email from one of your
:doc:`controllers <../general/controllers>`.

::

	$this->load->library('email');

	$this->email->from('your@example.com', 'Your Name');
	$this->email->to('someone@example.com'); 
	$this->email->cc('another@another-example.com'); 
	$this->email->bcc('them@their-example.com'); 

	$this->email->subject('Email Test');
	$this->email->message('Testing the email class.');	

	$this->email->send();

	echo $this->email->print_debugger();

Setting Email Preferences
=========================

There are 17 different preferences available to tailor how your email
messages are sent. You can either set them manually as described here,
or automatically via preferences stored in your config file, described
below:

Preferences are set by passing an array of preference values to the
email initialize function. Here is an example of how you might set some
preferences::

	$config['protocol'] = 'sendmail';
	$config['mailpath'] = '/usr/sbin/sendmail';
	$config['charset'] = 'iso-8859-1';
	$config['wordwrap'] = TRUE;

	$this->email->initialize($config);

.. note:: Most of the preferences have default values that will be used
	if you do not set them.

Setting Email Preferences in a Config File
------------------------------------------

If you prefer not to set preferences using the above method, you can
instead put them into a config file. Simply create a new file called the
email.php, add the $config array in that file. Then save the file at
config/email.php and it will be used automatically. You will NOT need to
use the $this->email->initialize() function if you save your preferences
in a config file.

Email Preferences
=================

The following is a list of all the preferences that can be set when
sending email.

=================== ====================== ============================ =======================================================================
Preference          Default Value          Options                      Description
=================== ====================== ============================ =======================================================================
**useragent**       CodeIgniter            None                         The "user agent".
**protocol**        mail                   mail, sendmail, or smtp      The mail sending protocol.
**mailpath**        /usr/sbin/sendmail     None                         The server path to Sendmail. 
**smtp_host**       No Default             None                         SMTP Server Address.
**smtp_user**       No Default             None                         SMTP Username.
**smtp_pass**       No Default             None                         SMTP Password.
**smtp_port**       25                     None                         SMTP Port.
**smtp_timeout**    5                      None                         SMTP Timeout (in seconds).
**smtp_crypto**     No Default             tls or ssl                   SMTP Encryption
**wordwrap**        TRUE                   TRUE or FALSE (boolean)      Enable word-wrap.
**wrapchars**       76                                                  Character count to wrap at.
**mailtype**        text                   text or html                 Type of mail. If you send HTML email you must send it as a complete web
                                                                        page. Make sure you don't have any relative links or relative image
                                                                        paths otherwise they will not work.
**charset**         utf-8                                               Character set (utf-8, iso-8859-1, etc.).
**validate**        FALSE                  TRUE or FALSE (boolean)      Whether to validate the email address.
**priority**        3                      1, 2, 3, 4, 5                Email Priority. 1 = highest. 5 = lowest. 3 = normal.
**crlf**            \\n                    "\\r\\n" or "\\n" or "\\r"   Newline character. (Use "\\r\\n" to comply with RFC 822).
**newline**         \\n                    "\\r\\n" or "\\n" or "\\r"   Newline character. (Use "\\r\\n" to comply with RFC 822).
**bcc_batch_mode**  FALSE                  TRUE or FALSE (boolean)      Enable BCC Batch Mode.
**bcc_batch_size**  200                    None                         Number of emails in each BCC batch.
=================== ====================== ============================ =======================================================================

Email Function Reference
========================

$this->email->from()
--------------------

Sets the email address and name of the person sending the email::

	$this->email->from('you@example.com', 'Your Name');

$this->email->reply_to()
-------------------------

Sets the reply-to address. If the information is not provided the
information in the "from" function is used. Example::

	$this->email->reply_to('you@example.com', 'Your Name');

$this->email->to()
------------------

Sets the email address(s) of the recipient(s). Can be a single email, a
comma-delimited list or an array::

	$this->email->to('someone@example.com');

::

	$this->email->to('one@example.com, two@example.com, three@example.com');

::

	$list = array('one@example.com', 'two@example.com', 'three@example.com');

	$this->email->to($list);

$this->email->cc()
------------------

Sets the CC email address(s). Just like the "to", can be a single email,
a comma-delimited list or an array.

$this->email->bcc()
-------------------

Sets the BCC email address(s). Just like the "to", can be a single
email, a comma-delimited list or an array.

$this->email->subject()
-----------------------

Sets the email subject::

	$this->email->subject('This is my subject');

$this->email->message()
-----------------------

Sets the email message body::

	$this->email->message('This is my message');

$this->email->set_alt_message()
---------------------------------

Sets the alternative email message body::

	$this->email->set_alt_message('This is the alternative message');

This is an optional message string which can be used if you send HTML
formatted email. It lets you specify an alternative message with no HTML
formatting which is added to the header string for people who do not
accept HTML email. If you do not set your own message CodeIgniter will
extract the message from your HTML email and strip the tags.

$this->email->clear()
---------------------

Initializes all the email variables to an empty state. This function is
intended for use if you run the email sending function in a loop,
permitting the data to be reset between cycles.

::

	foreach ($list as $name => $address)
	{
	    $this->email->clear();

	    $this->email->to($address);
	    $this->email->from('your@example.com');
	    $this->email->subject('Here is your info '.$name);
	    $this->email->message('Hi '.$name.' Here is the info you requested.');
	    $this->email->send();
	}

If you set the parameter to TRUE any attachments will be cleared as
well::

	$this->email->clear(TRUE);

$this->email->send()
--------------------

The Email sending function. Returns boolean TRUE or FALSE based on
success or failure, enabling it to be used conditionally::

	if ( ! $this->email->send())
	{
	    // Generate error
	}

$this->email->attach()
----------------------

Enables you to send an attachment. Put the file path/name in the first
parameter. Note: Use a file path, not a URL. For multiple attachments
use the function multiple times. For example::

	$this->email->attach('/path/to/photo1.jpg');
	$this->email->attach('/path/to/photo2.jpg');
	$this->email->attach('/path/to/photo3.jpg');

If you'd like to change the disposition or add a custom file name, you can use the second and third paramaters. To use the default disposition (attachment), leave the second parameter blank. Here's an example::
  
	$this->email->attach('/path/to/photo1.jpg', 'inline');
	$this->email->attach('/path/to/photo1.jpg', '', 'birthday.jpg');
	

$this->email->print_debugger()
-------------------------------

Returns a string containing any server messages, the email headers, and
the email messsage. Useful for debugging.

Overriding Word Wrapping
========================

If you have word wrapping enabled (recommended to comply with RFC 822)
and you have a very long link in your email it can get wrapped too,
causing it to become un-clickable by the person receiving it.
CodeIgniter lets you manually override word wrapping within part of your
message like this::

	The text of your email that
	gets wrapped normally.

	{unwrap}http://example.com/a_long_link_that_should_not_be_wrapped.html{/unwrap}

	More text that will be
	wrapped normally.
	

Place the item you do not want word-wrapped between: {unwrap} {/unwrap}
