<?php

class UserAgent_test extends CI_TestCase {

	protected $_user_agent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27';
	protected $_mobile_ua = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_1 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8B117 Safari/6531.22.7';

	public function set_up()
	{
		// set a baseline user agent
		$_SERVER['HTTP_USER_AGENT'] = $this->_user_agent;

		$this->ci_vfs_clone('application/config/user_agents.php');
		$this->agent = new CI_User_agent();
		$this->ci_instance_var('agent', $this->agent);
	}

	// --------------------------------------------------------------------

	public function test_accept_lang()
	{
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
		$this->assertTrue($this->agent->accept_lang());
		unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$this->assertTrue($this->agent->accept_lang('en'));
		$this->assertFalse($this->agent->accept_lang('fr'));
	}

	// --------------------------------------------------------------------

	public function test_mobile()
	{
		// Mobile Not Set
		$_SERVER['HTTP_USER_AGENT'] = $this->_mobile_ua;
		$this->assertEquals('', $this->agent->mobile());
		unset($_SERVER['HTTP_USER_AGENT']);
	}

	// --------------------------------------------------------------------

	public function test_is_functions()
	{
		$this->assertTrue($this->agent->is_browser());
		$this->assertTrue($this->agent->is_browser('Safari'));
		$this->assertFalse($this->agent->is_browser('Firefox'));
		$this->assertFalse($this->agent->is_robot());
		$this->assertFalse($this->agent->is_mobile());
	}

	// --------------------------------------------------------------------

	public function test_referrer()
	{
		$_SERVER['HTTP_REFERER'] = 'http://codeigniter.com/user_guide/';
		$this->assertTrue($this->agent->is_referral());
		$this->assertEquals('http://codeigniter.com/user_guide/', $this->agent->referrer());

		$this->agent->referer = NULL;
		unset($_SERVER['HTTP_REFERER']);
		$this->assertFalse($this->agent->is_referral());
		$this->assertEquals('', $this->agent->referrer());
	}

	// --------------------------------------------------------------------

	public function test_agent_string()
	{
		$this->assertEquals($this->_user_agent, $this->agent->agent_string());
	}

	// --------------------------------------------------------------------

	public function test_browser_info()
	{
		$this->assertEquals('Mac OS X', $this->agent->platform());
		$this->assertEquals('Safari', $this->agent->browser());
		$this->assertEquals('533.20.27', $this->agent->version());
		$this->assertEquals('', $this->agent->robot());
	}

	// --------------------------------------------------------------------

	public function test_charsets()
	{
		$_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf8';
		$this->agent->charsets = array();
		$this->agent->charsets();
		$this->assertTrue($this->agent->accept_charset('utf8'));
		$this->assertFalse($this->agent->accept_charset('foo'));
		$this->assertEquals('utf8', $this->agent->charsets[0]);

		$_SERVER['HTTP_ACCEPT_CHARSET'] = '';
		$this->agent->charsets = array();
		$this->assertFalse($this->agent->accept_charset());
		$this->assertEquals('Undefined', $this->agent->charsets[0]);

		$_SERVER['HTTP_ACCEPT_CHARSET'] = 'iso-8859-5, unicode-1-1; q=0.8';
		$this->agent->charsets = array();
		$this->assertTrue($this->agent->accept_charset('iso-8859-5'));
		$this->assertTrue($this->agent->accept_charset('unicode-1-1'));
		$this->assertFalse($this->agent->accept_charset('foo'));
		$this->assertEquals('iso-8859-5', $this->agent->charsets[0]);
		$this->assertEquals('unicode-1-1', $this->agent->charsets[1]);

		unset($_SERVER['HTTP_ACCEPT_CHARSET']);
	}

	public function test_parse()
	{
		$new_agent = 'Mozilla/5.0 (Android; Mobile; rv:13.0) Gecko/13.0 Firefox/13.0';
		$this->agent->parse($new_agent);

		$this->assertEquals('Android', $this->agent->platform());
		$this->assertEquals('Firefox', $this->agent->browser());
		$this->assertEquals('13.0', $this->agent->version());
		$this->assertEquals('', $this->agent->robot());
		$this->assertEquals('Android', $this->agent->mobile());
		$this->assertEquals($new_agent, $this->agent->agent_string());
		$this->assertTrue($this->agent->is_browser());
		$this->assertFalse($this->agent->is_robot());
		$this->assertTrue($this->agent->is_mobile());
		$this->assertTrue($this->agent->is_mobile('android'));
	}

}