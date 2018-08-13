##################
Encryption Library
##################

.. important:: DO NOT use this or any other *encryption* library for
	user password storage! Passwords must be *hashed* instead, and you
	should do that via PHP's own `Password Hashing extension
	<https://secure.php.net/password>`_.

The Encryption Library provides two-way data encryption. To do so in
a cryptographically secure way, it utilizes PHP extensions that are
unfortunately not always available on all systems.
You must meet one of the following dependencies in order to use this
library:

- `OpenSSL <https://secure.php.net/openssl>`_
- `MCrypt <https://secure.php.net/mcrypt>`_ (and `MCRYPT_DEV_URANDOM` availability)

If neither of the above dependencies is met, we simply cannot offer
you a good enough implementation to meet the high standards required
for proper cryptography.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

****************************
Using the Encryption Library
****************************

Initializing the Class
======================

Like most other classes in CodeIgniter, the Encryption library is
initialized in your controller using the ``$this->load->library()``
method::

	$this->load->library('encryption');

Once loaded, the Encryption library object will be available using::

	$this->encryption

Default behavior
================

By default, the Encryption Library will use the AES-128 cipher in CBC
mode, using your configured *encryption_key* and SHA512 HMAC authentication.

.. note:: AES-128 is chosen both because it is proven to be strong and
	because of its wide availability across different cryptographic
	software and programming languages' APIs.

However, the *encryption_key* is not used as is.

If you are somewhat familiar with cryptography, you should already know
that a HMAC also requires a secret key and using the same key for both
encryption and authentication is a bad practice.

Because of that, two separate keys are derived from your already configured
*encryption_key*: one for encryption and one for authentication. This is
done via a technique called `HMAC-based Key Derivation Function
<https://en.wikipedia.org/wiki/HKDF>`_ (HKDF).

Setting your encryption_key
===========================

An *encryption key* is a piece of information that controls the
cryptographic process and permits a plain-text string to be encrypted,
and afterwards - decrypted. It is the secret "ingredient" in the whole
process that allows you to be the only one who is able to decrypt data
that you've decided to hide from the eyes of the public.
After one key is used to encrypt data, that same key provides the **only**
means to decrypt it, so not only must you chose one carefully, but you
must not lose it or you will also lose access to the data.

It must be noted that to ensure maximum security, such key *should* not
only be as strong as possible, but also often changed. Such behavior
however is rarely practical or possible to implement, and that is why
CodeIgniter gives you the ability to configure a single key that is to be
used (almost) every time.

It goes without saying that you should guard your key carefully. Should
someone gain access to your key, the data will be easily decrypted. If
your server is not totally under your control it's impossible to ensure
key security so you may want to think carefully before using it for
anything that requires high security, like storing credit card numbers.

Your encryption key **must** be as long as the encyption algorithm in use
allows. For AES-128, that's 128 bits or 16 bytes (characters) long.
You will find a table below that shows the supported key lengths of
different ciphers.

The key should be as random as possible and it **must not** be a regular
text string, nor the output of a hashing function, etc. In order to create
a proper key, you must use the Encryption library's ``create_key()`` method
::

	// $key will be assigned a 16-byte (128-bit) random key
	$key = $this->encryption->create_key(16);

The key can be either stored in your *application/config/config.php*, or
you can design your own storage mechanism and pass the key dynamically
when encrypting/decrypting.

To save your key to your *application/config/config.php*, open the file
and set::

	$config['encryption_key'] = 'YOUR KEY';

You'll notice that the ``create_key()`` method outputs binary data, which
is hard to deal with (i.e. a copy-paste may damage it), so you may use
``bin2hex()``, ``hex2bin()`` or Base64-encoding to work with the key in
a more friendly manner. For example::

	// Get a hex-encoded representation of the key:
	$key = bin2hex($this->encryption->create_key(16));

	// Put the same value in your config with hex2bin(),
	// so that it is still passed as binary to the library:
	$config['encryption_key'] = hex2bin(<your hex-encoded key>);

.. _ciphers-and-modes:

Supported encryption ciphers and modes
======================================

.. note:: The terms 'cipher' and 'encryption algorithm' are interchangeable.

Portable ciphers
----------------

Because MCrypt and OpenSSL (also called drivers throughout this document)
each support different sets of encryption algorithms and often implement
them in different ways, our Encryption library is designed to use them in
a portable fashion, or in other words - it enables you to use them
interchangeably, at least for the ciphers supported by both drivers.

It is also implemented in a way that aims to match the standard
implementations in other programming languages and libraries.

Here's a list of the so called "portable" ciphers, where
"CodeIgniter name" is the string value that you'd have to pass to the
Encryption library to use that cipher:

======================== ================== ============================ ===============================
Cipher name              CodeIgniter name   Key lengths (bits / bytes)   Supported modes
======================== ================== ============================ ===============================
AES-128 / Rijndael-128   aes-128            128 / 16                     CBC, CTR, CFB, CFB8, OFB, ECB
AES-192                  aes-192            192 / 24                     CBC, CTR, CFB, CFB8, OFB, ECB
AES-256                  aes-256            256 / 32                     CBC, CTR, CFB, CFB8, OFB, ECB
DES                      des                56 / 7                       CBC, CFB, CFB8, OFB, ECB
TripleDES                tripledes          56 / 7, 112 / 14, 168 / 21   CBC, CFB, CFB8, OFB
Blowfish                 blowfish           128-448 / 16-56              CBC, CFB, OFB, ECB
CAST5 / CAST-128         cast5              88-128 / 11-16               CBC, CFB, OFB, ECB
RC4 / ARCFour            rc4                40-2048 / 5-256              Stream
======================== ================== ============================ ===============================

.. important:: Because of how MCrypt works, if you fail to provide a key
	with the appropriate length, you might end up using a different
	algorithm than the one configured, so be really careful with that!

.. note:: In case it isn't clear from the above table, Blowfish, CAST5
	and RC4 support variable length keys. That is, any number in the
	shown ranges is valid, although in bit terms that only happens
	in 8-bit increments.

.. note:: Even though CAST5 supports key lengths lower than 128 bits
	(16 bytes), in fact they will just be zero-padded to the
	maximum length, as specified in `RFC 2144
	<https://tools.ietf.org/rfc/rfc2144.txt>`_.

.. note:: Blowfish supports key lengths as small as 32 bits (4 bytes), but
	our tests have shown that only lengths of 128 bits (16 bytes) or
	higher are properly supported by both MCrypt and OpenSSL. It is
	also a bad practice to use such low-length keys anyway.

Driver-specific ciphers
-----------------------

As noted above, MCrypt and OpenSSL support different sets of encryption
ciphers. For portability reasons and because we haven't tested them
properly, we do not advise you to use the ones that are driver-specific,
but regardless, here's a list of most of them:


============== ========= ============================== =========================================
Cipher name    Driver    Key lengths (bits / bytes)     Supported modes
============== ========= ============================== =========================================
AES-128        OpenSSL   128 / 16                       CBC, CTR, CFB, CFB8, OFB, ECB, XTS
AES-192        OpenSSL   192 / 24                       CBC, CTR, CFB, CFB8, OFB, ECB, XTS
AES-256        OpenSSL   256 / 32                       CBC, CTR, CFB, CFB8, OFB, ECB, XTS
Rijndael-128   MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
Rijndael-192   MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
Rijndael-256   MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
GOST           MCrypt    256 / 32                       CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
Twofish        MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
CAST-128       MCrypt    40-128 / 5-16                  CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
CAST-256       MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
Loki97         MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
SaferPlus      MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
Serpent        MCrypt    128 / 16, 192 / 24, 256 / 32   CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
XTEA           MCrypt    128 / 16                       CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
RC2            MCrypt    8-1024 / 1-128                 CBC, CTR, CFB, CFB8, OFB, OFB8, ECB
RC2            OpenSSL   8-1024 / 1-128                 CBC, CFB, OFB, ECB
Camellia-128   OpenSSL   128 / 16                       CBC, CFB, CFB8, OFB, ECB
Camellia-192   OpenSSL   192 / 24                       CBC, CFB, CFB8, OFB, ECB
Camellia-256   OpenSSL   256 / 32                       CBC, CFB, CFB8, OFB, ECB
Seed           OpenSSL   128 / 16                       CBC, CFB, OFB, ECB
============== ========= ============================== =========================================

.. note:: If you wish to use one of those ciphers, you'd have to pass
	its name in lower-case to the Encryption library.

.. note:: You've probably noticed that all AES cipers (and Rijndael-128)
	are also listed in the portable ciphers list. This is because
	drivers support different modes for these ciphers. Also, it is
	important to note that AES-128 and Rijndael-128 are actually
	the same cipher, but **only** when used with a 128-bit key.

.. note:: CAST-128 / CAST-5 is also listed in both the portable and
	driver-specific ciphers list. This is because OpenSSL's
	implementation doesn't appear to be working correctly with
	key sizes of 80 bits and lower.

.. note:: RC2 is listed as supported by both MCrypt and OpenSSL.
	However, both drivers implement them differently and they
	are not portable. It is probably worth noting that we only
	found one obscure source confirming that it is MCrypt that
	is not properly implementing it.

.. _encryption-modes:

Encryption modes
----------------

Different modes of encryption have different characteristics and serve
for different purposes. Some are stronger than others, some are faster
and some offer extra features.
We are not going in depth into that here, we'll leave that to the
cryptography experts. The table below is to provide brief informational
reference to our more experienced users. If you are a beginner, just
stick to the CBC mode - it is widely accepted as strong and secure for
general purposes.

=========== ================== ================= ===================================================================================================================================================
Mode name   CodeIgniter name   Driver support    Additional info
=========== ================== ================= ===================================================================================================================================================
CBC         cbc                MCrypt, OpenSSL   A safe default choice
CTR         ctr                MCrypt, OpenSSL   Considered as theoretically better than CBC, but not as widely available
CFB         cfb                MCrypt, OpenSSL   N/A
CFB8        cfb8               MCrypt, OpenSSL   Same as CFB, but operates in 8-bit mode (not recommended).
OFB         ofb                MCrypt, OpenSSL   N/A
OFB8        ofb8               MCrypt            Same as OFB, but operates in 8-bit mode (not recommended).
ECB         ecb                MCrypt, OpenSSL   Ignores IV (not recommended).
XTS         xts                OpenSSL           Usually used for encrypting random access data such as RAM or hard-disk storage.
Stream      stream             MCrypt, OpenSSL   This is not actually a mode, it just says that a stream cipher is being used. Required because of the general cipher+mode initialization process.
=========== ================== ================= ===================================================================================================================================================

Message Length
==============

It's probably important for you to know that an encrypted string is usually
longer than the original, plain-text string (depending on the cipher).

This is influenced by the cipher algorithm itself, the IV prepended to the
cipher-text and the HMAC authentication message that is also prepended.
Furthermore, the encrypted message is also Base64-encoded so that it is safe
for storage and transmission, regardless of a possible character set in use.

Keep this information in mind when selecting your data storage mechanism.
Cookies, for example, can only hold 4K of information.

.. _configuration:

Configuring the library
=======================

For usability, performance, but also historical reasons tied to our old
:doc:`Encrypt Class <encrypt>`, the Encryption library is designed to
use repeatedly the same driver, encryption cipher, mode and key.

As noted in the "Default behavior" section above, this means using an
auto-detected driver (OpenSSL has a higher priority), the AES-128 ciper
in CBC mode, and your ``$config['encryption_key']`` value.

If you wish to change that however, you need to use the ``initialize()``
method. It accepts an associative array of parameters, all of which are
optional:

======== ===============================================
Option   Possible values
======== ===============================================
driver   'mcrypt', 'openssl'
cipher   Cipher name (see :ref:`ciphers-and-modes`)
mode     Encryption mode (see :ref:`encryption-modes`)
key      Encryption key 
======== ===============================================

For example, if you were to change the encryption algorithm and
mode to AES-256 in CTR mode, this is what you should do::

	$this->encryption->initialize(
		array(
			'cipher' => 'aes-256',
			'mode' => 'ctr',
			'key' => '<a 32-character random string>'
		)
	);

Note that we only mentioned that you want to change the ciper and mode,
but we also included a key in the example. As previously noted, it is
important that you choose a key with a proper size for the used algorithm.

There's also the ability to change the driver, if for some reason you
have both, but want to use MCrypt instead of OpenSSL::

	// Switch to the MCrypt driver
	$this->encryption->initialize(array('driver' => 'mcrypt'));

	// Switch back to the OpenSSL driver
	$this->encryption->initialize(array('driver' => 'openssl'));

Encrypting and decrypting data
==============================

Encrypting and decrypting data with the already configured library
settings is simple. As simple as just passing the string to the
``encrypt()`` and/or ``decrypt()`` methods::

	$plain_text = 'This is a plain-text message!';
	$ciphertext = $this->encryption->encrypt($plain_text);

	// Outputs: This is a plain-text message!
	echo $this->encryption->decrypt($ciphertext);

And that's it! The Encryption library will do everything necessary
for the whole process to be cryptographically secure out-of-the-box.
You don't need to worry about it.

.. important:: Both methods will return FALSE in case of an error.
	While for ``encrypt()`` this can only mean incorrect
	configuration, you should always check the return value
	of ``decrypt()`` in production code.

How it works
------------

If you must know how the process works, here's what happens under
the hood:

- ``$this->encryption->encrypt($plain_text)``

  #. Derive an encryption key and a HMAC key from your configured
     *encryption_key* via HKDF, using the SHA-512 digest algorithm.

  #. Generate a random initialization vector (IV).

  #. Encrypt the data via AES-128 in CBC mode (or another previously
     configured cipher and mode), using the above-mentioned derived
     encryption key and IV.

  #. Prepend said IV to the resulting cipher-text.

  #. Base64-encode the resulting string, so that it can be safely
     stored or transferred without worrying about character sets.

  #. Create a SHA-512 HMAC authentication message using the derived
     HMAC key to ensure data integrity and prepend it to the Base64
     string.

- ``$this->encryption->decrypt($ciphertext)``

  #. Derive an encryption key and a HMAC key from your configured
     *encryption_key* via HKDF, using the SHA-512 digest algorithm.
     Because your configured *encryption_key* is the same, this
     will produce the same result as in the ``encrypt()`` method
     above - otherwise you won't be able to decrypt it.

  #. Check if the string is long enough, separate the HMAC out of
     it and validate if it is correct (this is done in a way that
     prevents timing attacks against it). Return FALSE if either of
     the checks fails.

  #. Base64-decode the string.

  #. Separate the IV out of the cipher-text and decrypt the said
     cipher-text using that IV and the derived encryption key.

.. _custom-parameters:

Using custom parameters
-----------------------

Let's say you have to interact with another system that is out
of your control and uses another method to encrypt data. A
method that will most certainly not match the above-described
sequence and probably not use all of the steps either.

The Encryption library allows you to change how its encryption
and decryption processes work, so that you can easily tailor a
custom solution for such situations.

.. note:: It is possible to use the library in this way, without
	setting an *encryption_key* in your configuration file.

All you have to do is to pass an associative array with a few
parameters to either the ``encrypt()`` or ``decrypt()`` method.
Here's an example::

	// Assume that we have $ciphertext, $key and $hmac_key
	// from on outside source

	$message = $this->encryption->decrypt(
		$ciphertext,
		array(
			'cipher' => 'blowfish',
			'mode' => 'cbc',
			'key' => $key,
			'hmac_digest' => 'sha256',
			'hmac_key' => $hmac_key
		)
	);

In the above example, we are decrypting a message that was encrypted
using the Blowfish cipher in CBC mode and authenticated via a SHA-256
HMAC.

.. important:: Note that both 'key' and 'hmac_key' are used in this
	example. When using custom parameters, encryption and HMAC keys
	are not derived like the default behavior of the library is.

Below is a list of the available options.

However, unless you really need to and you know what you are doing,
we advise you to not change the encryption process as this could
impact security, so please do so with caution.

============= =============== ============================= ======================================================
Option        Default value   Mandatory / Optional          Description
============= =============== ============================= ======================================================
cipher        N/A             Yes                           Encryption algorithm (see :ref:`ciphers-and-modes`).
mode          N/A             Yes                           Encryption mode (see :ref:`encryption-modes`).
key           N/A             Yes                           Encryption key.
hmac          TRUE            No                            Whether to use a HMAC.
                                                            Boolean. If set to FALSE, then *hmac_digest* and
                                                            *hmac_key* will be ignored.
hmac_digest   sha512          No                            HMAC message digest algorithm (see :ref:`digests`).
hmac_key      N/A             Yes, unless *hmac* is FALSE   HMAC key.
raw_data      FALSE           No                            Whether the cipher-text should be raw.
                                                            Boolean. If set to TRUE, then Base64 encoding and
                                                            decoding will not be performed and HMAC will not
                                                            be a hexadecimal string.
============= =============== ============================= ======================================================

.. important:: ``encrypt()`` and ``decrypt()`` will return FALSE if
	a mandatory parameter is not provided or if a provided
	value is incorrect. This includes *hmac_key*, unless *hmac*
	is set to FALSE.

.. _digests:

Supported HMAC authentication algorithms
----------------------------------------

For HMAC message authentication, the Encryption library supports
usage of the SHA-2 family of algorithms:

=========== ==================== ============================
Algorithm   Raw length (bytes)   Hex-encoded length (bytes)
=========== ==================== ============================
sha512      64                   128
sha384      48                   96
sha256      32                   64
sha224      28                   56
=========== ==================== ============================

The reason for not including other popular algorithms, such as
MD5 or SHA1 is that they are no longer considered secure enough
and as such, we don't want to encourage their usage.
If you absolutely need to use them, it is easy to do so via PHP's
native `hash_hmac() <https://secure.php.net/manual/en/function.hash-hmac.php>`_ function.

Stronger algorithms of course will be added in the future as they
appear and become widely available.

***************
Class Reference
***************

.. php:class:: CI_Encryption

	.. php:method:: initialize($params)

		:param	array	$params: Configuration parameters
		:returns:	CI_Encryption instance (method chaining)
		:rtype:	CI_Encryption

		Initializes (configures) the library to use a different
		driver, cipher, mode or key.

		Example::

			$this->encryption->initialize(
				array('mode' => 'ctr')
			);

		Please refer to the :ref:`configuration` section for detailed info.

	.. php:method:: encrypt($data[, $params = NULL])

		:param	string	$data: Data to encrypt
		:param	array	$params: Optional parameters
		:returns:	Encrypted data or FALSE on failure
		:rtype:	string

		Encrypts the input data and returns its ciphertext.

		Example::

			$ciphertext = $this->encryption->encrypt('My secret message');

		Please refer to the :ref:`custom-parameters` section for information
		on the optional parameters.

	.. php:method:: decrypt($data[, $params = NULL])

		:param	string	$data: Data to decrypt
		:param	array	$params: Optional parameters
		:returns:	Decrypted data or FALSE on failure
		:rtype:	string

		Decrypts the input data and returns it in plain-text.

		Example::

			echo $this->encryption->decrypt($ciphertext);

		Please refer to the :ref:`custom-parameters` secrion for information
		on the optional parameters.

	.. php:method:: create_key($length)

		:param	int	$length: Output length
		:returns:	A pseudo-random cryptographic key with the specified length, or FALSE on failure
		:rtype:	string

		Creates a cryptographic key by fetching random data from
		the operating system's sources (i.e. /dev/urandom).

	.. php:method:: hkdf($key[, $digest = 'sha512'[, $salt = NULL[, $length = NULL[, $info = '']]]])

		:param	string	$key: Input key material
		:param	string	$digest: A SHA-2 family digest algorithm
		:param	string	$salt: Optional salt
		:param	int	$length: Optional output length
		:param	string	$info: Optional context/application-specific info
		:returns:	A pseudo-random key or FALSE on failure
		:rtype:	string

		Derives a key from another, presumably weaker key.

		This method is used internally to derive an encryption and HMAC key
		from your configured *encryption_key*.

		It is publicly available due to its otherwise general purpose. It is
		described in `RFC 5869 <https://tools.ietf.org/rfc/rfc5869.txt>`_.

		However, as opposed to the description in RFC 5869, this implementation
		doesn't support SHA1.

		Example::

			$hmac_key = $this->encryption->hkdf(
				$key,
				'sha512',
				NULL,
				NULL,
				'authentication'
			);

			// $hmac_key is a pseudo-random key with a length of 64 bytes
