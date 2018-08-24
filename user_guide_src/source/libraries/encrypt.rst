#############
Encrypt Class
#############

The Encrypt Class provides two-way data encryption. It encrypted using
the Mcrypt PHP extension, which is required for the Encrypt Class to run.

.. important:: This library has been DEPRECATED and is only kept for
	backwards compatibility. Please use the new :doc:`Encryption Library
	<encryption>`.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

*************************
Using the Encrypt Library
*************************

Setting your Key
================

A *key* is a piece of information that controls the cryptographic
process and permits an encrypted string to be decoded. In fact, the key
you chose will provide the **only** means to decode data that was
encrypted with that key, so not only must you choose the key carefully,
you must never change it if you intend use it for persistent data.

It goes without saying that you should guard your key carefully. Should
someone gain access to your key, the data will be easily decoded. If
your server is not totally under your control it's impossible to ensure
key security so you may want to think carefully before using it for
anything that requires high security, like storing credit card numbers.

To take maximum advantage of the encryption algorithm, your key should
be 32 characters in length (256 bits). The key should be as random a
string as you can concoct, with numbers and uppercase and lowercase
letters. Your key should **not** be a simple text string. In order to be
cryptographically secure it needs to be as random as possible.

Your key can be either stored in your **application/config/config.php**, or
you can design your own storage mechanism and pass the key dynamically
when encoding/decoding.

To save your key to your **application/config/config.php**, open the file
and set::

	$config['encryption_key'] = "YOUR KEY";

Message Length
==============

It's important for you to know that the encoded messages the encryption
function generates will be approximately 2.6 times longer than the
original message. For example, if you encrypt the string "my super
secret data", which is 21 characters in length, you'll end up with an
encoded string that is roughly 55 characters (we say "roughly" because
the encoded string length increments in 64 bit clusters, so it's not
exactly linear). Keep this information in mind when selecting your data
storage mechanism. Cookies, for example, can only hold 4K of
information.

Initializing the Class
======================

Like most other classes in CodeIgniter, the Encrypt class is
initialized in your controller using the ``$this->load->library()``
method::

	$this->load->library('encrypt');

Once loaded, the Encrypt library object will be available using::

	$this->encrypt

***************
Class Reference
***************

.. php:class:: CI_Encrypt

	.. php:method:: encode($string[, $key = ''])

		:param	string	$string: Data to encrypt
		:param	string	$key: Encryption key
		:returns:	Encrypted string
		:rtype:	string

		Performs the data encryption and returns it as a string. Example::

			$msg = 'My secret message';

			$encrypted_string = $this->encrypt->encode($msg);

		You can optionally pass your encryption key via the second parameter if
		you don't want to use the one in your config file::

			$msg = 'My secret message';
			$key = 'super-secret-key';

			$encrypted_string = $this->encrypt->encode($msg, $key);

	.. php:method:: decode($string[, $key = ''])

		:param	string	$string: String to decrypt
		:param	string	$key: Encryption key
		:returns:	Plain-text string
		:rtype:	string

		Decrypts an encoded string. Example::

			$encrypted_string = 'APANtByIGI1BpVXZTJgcsAG8GZl8pdwwa84';

			$plaintext_string = $this->encrypt->decode($encrypted_string);

		You can optionally pass your encryption key via the second parameter if
		you don't want to use the one in your config file::

			$msg = 'My secret message';
			$key = 'super-secret-key';

			$encrypted_string = $this->encrypt->decode($msg, $key);

	.. php:method:: set_cipher($cipher)

		:param	int	$cipher: Valid PHP MCrypt cypher constant
		:returns:	CI_Encrypt instance (method chaining)
		:rtype:	CI_Encrypt

		Permits you to set an Mcrypt cipher. By default it uses
		``MCRYPT_RIJNDAEL_256``. Example::

			$this->encrypt->set_cipher(MCRYPT_BLOWFISH);

		Please visit php.net for a list of `available ciphers <https://secure.php.net/mcrypt>`_.

		If you'd like to manually test whether your server supports MCrypt you
		can use::

			echo extension_loaded('mcrypt') ? 'Yup' : 'Nope';

	.. php:method:: set_mode($mode)

		:param	int	$mode: Valid PHP MCrypt mode constant
		:returns:	CI_Encrypt instance (method chaining)
		:rtype:	CI_Encrypt

		Permits you to set an Mcrypt mode. By default it uses **MCRYPT_MODE_CBC**.
		Example::

			$this->encrypt->set_mode(MCRYPT_MODE_CFB);

		Please visit php.net for a list of `available modes <https://secure.php.net/mcrypt>`_.

	.. php:method:: encode_from_legacy($string[, $legacy_mode = MCRYPT_MODE_ECB[, $key = '']])

		:param	string	$string: String to encrypt
		:param	int	$legacy_mode: Valid PHP MCrypt cipher constant
		:param	string	$key: Encryption key
		:returns:	Newly encrypted string
		:rtype:	string

		Enables you to re-encode data that was originally encrypted with
		CodeIgniter 1.x to be compatible with the Encrypt library in
		CodeIgniter 2.x. It is only necessary to use this method if you have
		encrypted data stored permanently such as in a file or database and are
		on a server that supports Mcrypt. "Light" use encryption such as
		encrypted session data or transitory encrypted flashdata require no
		intervention on your part. However, existing encrypted Sessions will be
		destroyed since data encrypted prior to 2.x will not be decoded.

		.. important::
			**Why only a method to re-encode the data instead of maintaining legacy
			methods for both encoding and decoding?** The algorithms in the
			Encrypt library have improved in CodeIgniter 2.x both for performance
			and security, and we do not wish to encourage continued use of the older
			methods. You can of course extend the Encryption library if you wish and
			replace the new methods with the old and retain seamless compatibility
			with CodeIgniter 1.x encrypted data, but this a decision that a
			developer should make cautiously and deliberately, if at all.

		::

			$new_data = $this->encrypt->encode_from_legacy($old_encrypted_string);

		======================	===============	 =======================================================================
		Parameter		 Default	  Description
		======================	===============  =======================================================================
		**$orig_data**		n/a 		 The original encrypted data from CodeIgniter 1.x's Encryption library
		**$legacy_mode**	MCRYPT_MODE_ECB	 The Mcrypt mode that was used to generate the original encrypted data.
							 CodeIgniter 1.x's default was MCRYPT_MODE_ECB, and it will assume that
							 to be the case unless overridden by this parameter.
		**$key**		n/a 		 The encryption key. This it typically specified in your config file as
							 outlined above.
		======================	===============	 =======================================================================