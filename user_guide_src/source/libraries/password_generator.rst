##################
Password Generator
##################

The Password Generator class contains methods for generating (secure) random 
strings to be used in passwords.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

************************************
Using the Password Generator Library
************************************

Like most other classes in CodeIgniter, the Password Generator 
library is initialized in your controller using the 
``$this->load->library()`` method::

	$this->load->library('password_generator');

Once loaded, the Encryption library object will be available using::

	$this->password_generator

Configuring the Library
=======================

If you specify ``$config['password_rules']`` in your configuration files, the 
library will use this as its default settings. Password rules should be an 
array with 4 elements (keys: ``upper``, ``lower``, ``digits``, and ``special``;
the value of each element should be a boolean ``TRUE`` or ``FALSE``).

    $config['password_rules'] = array(
        'upper' => TRUE,
        'lower' => TRUE,
        'digits' => TRUE,
        'special' => FALSE
    );

If you do not define ``$config['password_rules']`` then 

Creating a Password
===================

To generate a random password, simply call the ``create_password`` method.

For example, this will generate a password (16 characters long, by default)
based on the configured/default password rules.

    $password = $this->password_generator->create_password();

This will generate 40 random lowercase English letters.

    $password = $this->password_generator->create_password(
        40,
        array(
            'digits' => FALSE,
            'upper' => TRUE,
            'lower' => FALSE,
            'special' => FALSE
        )
    );

Other Utilities in this Class
=============================

Unpredictable Random Numbers
----------------------------

If you need to generate a random number between two given integers (i.e. 1 and
1000), and you need it to be unpredictable, use this method.

    $random = $this->password_generator->get_random_number(1, 1000);

``$random`` will contain an integer that is greater than or equal to ``1``, but
less than or equal to ``1000``. Furthermore, the randomness is derived from the
operating system's CSRPNG, which means that it should be unpredictable.

In this case, "unpredictable" means if you observe the output of 
``get_random_number()`` many times, you will be no better at predicting the 
next output as you were during your first guess.

Unpredictable Random Character Strings
--------------------------------------

This string generator is called by ``create_password()`` internally. It accepts
two parameters: The length of the desired output and a string containing every
possible character desired in the output. It will return a string comprsied of
``$length`` random selectons of ``$keyspace`` (possible characters)

    $string = $this->password_generator->get_random_string(8, '0123456789');

In the example above, ``$string`` will return a string consisting of  8 numbers
chosen randomly.
