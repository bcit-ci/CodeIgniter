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

.. class: CI_User_agent

	.. method:: is_browser([$key = NULL])

		:param string $key: optional browser name
		:returns: bool

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

	.. method:: is_mobile([$key = NULL])

		:param string $key: optional mobile device name
		:returns: bool

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

	.. method:: is_robot([$key = NULL])

		:param string $key: optional robot name
		:returns: bool

		Returns TRUE/FALSE (boolean) if the user agent is a known robot.

		.. note:: The user agent library only contains the most common robot definitions. It is not a complete list of bots.
			There are hundreds of them so searching for each one would not be very efficient. If you find that some bots
			that commonly visit your site are missing from the list you can add them to your
			**application/config/user_agents.php** file.

	.. method:: is_referral()

		:returns: bool

		Returns TRUE/FALSE (boolean) if the user agent was referred from another site.

	.. method:: browser()

		:returns: string

		Returns a string containing the name of the web browser viewing your site.

	.. method:: version()

		:returns: string

		Returns a string containing the version number of the web browser viewing your site.

	.. method:: mobile()

		:returns: string

		Returns a string containing the name of the mobile device viewing your site.

	.. method:: robot()

		:returns: string

		Returns a string containing the name of the robot viewing your site.

	.. method:: platform()

		:returns: string

		Returns a string containing the platform viewing your site (Linux, Windows, OS X, etc.).

	.. method:: referrer()

		:returns: string

		The referrer, if the user agent was referred from another site. Typically you'll test for this as follows::

			if ($this->agent->is_referral())
			{
				echo $this->agent->referrer();
			}

	.. method:: agent_string()

		:returns: string

		Returns a string containing the full user agent string. Typically it will be something like this::

			Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.0.4) Gecko/20060613 Camino/1.0.2

	.. method:: accept_lang([$lang = 'en'])

		:param string $lang: language key
		:returns: bool

		Lets you determine if the user agent accepts a particular language. Example::

			if ($this->agent->accept_lang('en'))
			{
				echo 'You accept English!';
			}

		.. note:: This method is not typically very reliable since some	browsers do not provide language info,
			and even among those that do, it is not always accurate.

	.. method:: languages()

		:returns: array

		Returns an array of languages supported by the user agent.

	.. method:: accept_charset([$charset = 'utf-8'])

		:param string $charset: character set
		:returns: bool

		Lets you determine if the user agent accepts a particular character set. Example::

			if ($this->agent->accept_charset('utf-8'))
			{
				echo 'You browser supports UTF-8!';
			}

		.. note:: This method is not typically very reliable since some browsers do not provide character-set info,
			and even among those that do, it is not always accurate.

	.. method:: charsets()

		:returns: array

		Returns an array of character sets accepted by the user agent.

	.. method:: parse($string)

		:param string $string: A custom user-agent string
		:returns: void

		Parses a custom user-agent string, different from the one reported by the current visitor.