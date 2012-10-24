<?php

class UserAgent_test extends CI_TestCase {

	protected $_user_agent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-us) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27';
	protected $_mobile_ua = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_1 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8B117 Safari/6531.22.7';

	public function set_up()
	{
		// set a baseline user agent
		$_SERVER['HTTP_USER_AGENT'] = $this->_user_agent;

		$this->ci_vfs_clone('application/config/user_agents.php');

		$this->agent = new Mock_Libraries_UserAgent();

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

	public function test_util_is_functions()
	{
		$this->assertTrue($this->agent->is_browser());
		$this->assertFalse($this->agent->is_robot());
		$this->assertFalse($this->agent->is_mobile());
		$this->assertFalse($this->agent->is_referral());
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
		$this->assertEquals('', $this->agent->referrer());
	}

	// --------------------------------------------------------------------

	public function test_charsets()
	{
		$_SERVER['HTTP_ACCEPT_CHARSET'] = 'utf8';

		$charsets = $this->agent->charsets();

		$this->assertEquals('utf8', $charsets[0]);

		unset($_SERVER['HTTP_ACCEPT_CHARSET']);

		$this->assertFalse($this->agent->accept_charset());
	}

}