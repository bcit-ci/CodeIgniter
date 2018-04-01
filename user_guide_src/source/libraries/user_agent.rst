################
User Agent Class
################

The User Agent Class provides functions that help identify information
about the browser, mobile device, or robot visiting your site. In
addition you can get referrer information as well as language and
supported character-set information.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

**************************
Using the User Agent Class
**************************

Initializing the Class
======================

Like most other classes in CodeIgniter, the User Agent class is
initialized in your controller using the $this->load->library function::

	$this->load->library('user_agent');

Once loaded, the object will be available using: ``$this->agent``

User Agent Definitions
======================

The user agent name definitions are located in a config file located at:
application/config/user_agents.php. You may add items to the various
user agent arrays if needed.

Example
=======

When the User Agent class is initialized it will attempt to determine
whether the user agent browsing your site is a web browser, a mobile
device, or a robot. It will also gather the platform information if it
is available.

::

	$this->load->library('user_agent');

	if ($this->agent->is_browser())
	{
		$agent = $this->agent->browser().' '.$this->agent->version();
	}
	elseif ($this->agent->is_robot())
	{
		$agent = $this->agent->robot();
	}
	elseif ($this->agent->is_mobile())
	{
		$agent = $this->agent->mobile();
	}
	else
	{
		$agent = 'Unidentified User Agent';
	}

	echo $agent;

	echo $this->agent->platform(); // Platform info (Windows, Linux, Mac, etc.)

***************
Class Reference
***************

.. php:class:: CI_User_agent

	.. php:method:: is_browser([$key = NULL])

		:param	string	$key: Optional browser name
		:returns:	TRUE if the user agent is a (specified) browser, FALSE if not
		:rtype:	bool

		Returns TRUE/FALSE (boolean) if the user agent is a known web browser.
		::

			if ($this->agent->is_browser('Safari'))
			{
				echo 'You are using Safari.';
			}
			elseif ($this->agent->is_browser())
			{
				echo 'You are using a browser.';
			}

		.. note:: The string "Safari" in this example is an array key in the list of browser definitions.
			You can find this list in **application/config/user_agents.php** if you want to add new
			browsers or change the stings.

	.. php:method:: is_mobile([$key = NULL])

		:param	string	$key: Optional mobile device name
		:returns:	TRUE if the user agent is a (specified) mobile device, FALSE if not
		:rtype:	bool

		Returns TRUE/FALSE (boolean) if the user agent is a known mobile device.
		::

			if ($this->agent->is_mobile('iphone'))
			{
				$this->load->view('iphone/home');
			}
			elseif ($this->agent->is_mobile())
			{
				$this->load->view('mobile/home');
			}
			else
			{
				$this->load->view('web/home');
			}

	.. php:method:: is_robot([$key = NULL])

		:param	string	$key: Optional robot name
		:returns:	TRUE if the user agent is a (specified) robot, FALSE if not
		:rtype:	bool

		Returns TRUE/FALSE (boolean) if the user agent is a known robot.

		.. note:: The user agent library only contains the most common robot definitions. It is not a complete list of bots.
			There are hundreds of them so searching for each one would not be very efficient. If you find that some bots
			that commonly visit your site are missing from the list you can add them to your
			**application/config/user_agents.php** file.

	.. php:method:: is_referral()

		:returns:	TRUE if the user agent is a referral, FALSE if not
		:rtype:	bool

		Returns TRUE/FALSE (boolean) if the user agent was referred from another site.

	.. php:method:: browser()

		:returns:	Detected browser or an empty string
		:rtype:	string

		Returns a string containing the name of the web browser viewing your site.

	.. php:method:: version()

		:returns:	Detected browser version or an empty string
		:rtype:	string

		Returns a string containing the version number of the web browser viewing your site.

	.. php:method:: mobile()

		:returns:	Detected mobile device brand or an empty string
		:rtype:	string

		Returns a string containing the name of the mobile device viewing your site.

	.. php:method:: robot()

		:returns:	Detected robot name or an empty string
		:rtype:	string

		Returns a string containing the name of the robot viewing your site.

	.. php:method:: platform()

		:returns:	Detected operating system or an empty string
		:rtype:	string

		Returns a string containing the platform viewing your site (Linux, Windows, OS X, etc.).

	.. php:method:: referrer()

		:returns:	Detected referrer or an empty string
		:rtype:	string

		The referrer, if the user agent was referred from another site. Typically you'll test for this as follows::

			if ($this->agent->is_referral())
			{
				echo $this->agent->referrer();
			}

	.. php:method:: agent_string()

		:returns:	Full user agent string or an empty string
		:rtype:	string

		Returns a string containing the full user agent string. Typically it will be something like this::

			Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.0.4) Gecko/20060613 Camino/1.0.2

	.. php:method:: accept_lang([$lang = 'en'])

		:param	string	$lang: Language key
		:returns:	TRUE if provided language is accepted, FALSE if not
		:rtype:	bool

		Lets you determine if the user agent accepts a particular language. Example::

			if ($this->agent->accept_lang('en'))
			{
				echo 'You accept English!';
			}

		.. note:: This method is not typically very reliable since some	browsers do not provide language info,
			and even among those that do, it is not always accurate.

	.. php:method:: languages()

		:returns:	An array list of accepted languages
		:rtype:	array

		Returns an array of languages supported by the user agent.

	.. php:method:: accept_charset([$charset = 'utf-8'])

		:param	string	$charset: Character set
		:returns:	TRUE if the character set is accepted, FALSE if not
		:rtype:	bool

		Lets you determine if the user agent accepts a particular character set. Example::

			if ($this->agent->accept_charset('utf-8'))
			{
				echo 'You browser supports UTF-8!';
			}

		.. note:: This method is not typically very reliable since some browsers do not provide character-set info,
			and even among those that do, it is not always accurate.

	.. php:method:: charsets()

		:returns:	An array list of accepted character sets
		:rtype:	array

		Returns an array of character sets accepted by the user agent.

	.. php:method:: parse($string)

		:param	string	$string: A custom user-agent string
		:rtype:	void

		Parses a custom user-agent string, different from the one reported by the current visitor.