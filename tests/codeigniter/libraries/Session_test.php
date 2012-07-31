<?php

/**
 * Session driver library unit test
 */
class Session_test extends CI_TestCase {
	protected $settings = array(
		'use_cookies' => 0,
	   	'use_only_cookies' => 0,
	   	'cache_limiter' => false
	);
	protected $setting_vals = array();
	protected $cookie_vals;
	protected $session;

	/**
	 * Set up test framework
	 */
	public function set_up()
	{
		// Override settings
		foreach ($this->settings as $name => $value) {
			$this->setting_vals[$name] = ini_get('session.'.$name);
			ini_set('session.'.$name, $value);
		}

		// Start with clean environment
		$this->cookie_vals = $_COOKIE;
		$_COOKIE = array();

		// Establish necessary support classes
		$obj = new stdClass;
		$classes = array(
			'config' => 'cfg',
			'load' => 'load',
			'input' => 'in'
		);
		foreach ($classes as $name => $abbr) {
			$class = $this->ci_core_class($abbr);
			$obj->$name = new $class;
		}
		$this->ci_instance($obj);

		// Attach session instance locally
		$config = array(
			'sess_encrypt_cookie' => FALSE,
			'sess_use_database' => FALSE,
			'sess_table_name' => '',
			'sess_expiration' => 7200,
			'sess_expire_on_close' => FALSE,
			'sess_match_ip' => FALSE,
			'sess_match_useragent' => TRUE,
			'sess_cookie_name' => 'ci_session',
			'cookie_path' => '',
			'cookie_domain' => '',
			'cookie_secure' => FALSE,
			'cookie_httponly' => FALSE,
			'sess_time_to_update' => 300,
			'time_reference' => 'local',
			'cookie_prefix' => '',
			'encryption_key' => 'foobar',
			'sess_valid_drivers' => array(
				'Mock_Libraries_Session_native',
			   	'Mock_Libraries_Session_cookie'
			)
		);
		$this->session = new Mock_Libraries_Session($config);
	}

	/**
	 * Tear down test framework
	 */
	public function tear_down()
	{
		// Restore environment
		if (session_id()) session_destroy();
		$_SESSION = array();
		$_COOKIE = $this->cookie_vals;

		// Restore settings
		foreach ($this->settings as $name => $value) {
			ini_set('session.'.$name, $this->setting_vals[$name]);
		}
	}

	/**
	 * Test set_userdata() function
	 */
	public function test_set_userdata()
	{
		// Set a userdata message for each driver
		$cmsg = 'Some test data';
		$this->session->cookie->set_userdata('test1', $cmsg);
		$nmsg = 'Other test data';
		$this->session->native->set_userdata('test1', $nmsg);

		// Verify independent messages
		$this->assertEquals($this->session->cookie->userdata('test1'), $cmsg);
		$this->assertEquals($this->session->native->userdata('test1'), $nmsg);
	}

	/**
	 * Test the has_userdata() function
	 */
	public function test_has_userdata()
	{
		// Set a userdata message for each driver
		$cmsg = 'My test data';
		$this->session->cookie->set_userdata('test2', $cmsg);
		$nmsg = 'Your test data';
		$this->session->native->set_userdata('test2', $nmsg);

		// Verify independent messages
		$this->assertTrue($this->session->cookie->has_userdata('test2'));
		$this->assertTrue($this->session->native->has_userdata('test2'));
	}

	/**
	 * Test the all_userdata() function
	 */
	public function test_all_userdata()
	{
		// Set a specific series of data for each driver
		$cdata = array(
			'one' => 'first',
			'two' => 'second',
		   	'three' => 'third',
		   	'foo' => 'bar',
		   	'bar' => 'baz'
		);
		$ndata = array(
			'one' => 'gold',
		   	'two' => 'silver',
		   	'three' => 'bronze',
		   	'foo' => 'baz',
		   	'bar' => 'foo'
		);
		$this->session->cookie->set_userdata($cdata);
		$this->session->native->set_userdata($ndata);

		// Make sure all values are present
		$call = $this->session->cookie->all_userdata();
		foreach ($cdata as $key => $value) {
			$this->assertEquals($value, $call[$key]);
		}
		$nall = $this->session->native->all_userdata();
		foreach ($ndata as $key => $value) {
			$this->assertEquals($value, $nall[$key]);
		}
	}

	/**
	 * Test the unset_userdata() function
	 */
	public function test_unset_userdata()
	{
		// Set a userdata message for each driver
		$cmsg = 'Other test data';
		$this->session->cookie->set_userdata('test3', $cmsg);
		$nmsg = 'Sundry test data';
		$this->session->native->set_userdata('test3', $nmsg);

		// Verify independent messages
		$this->assertEquals($this->session->cookie->userdata('test3'), $cmsg);
		$this->assertEquals($this->session->native->userdata('test3'), $nmsg);

		// Unset them and verify absence
		$this->session->cookie->unset_userdata('test3');
		$this->session->native->unset_userdata('test3');
		$this->assertEquals($this->session->cookie->userdata('test3'), NULL);
		$this->assertEquals($this->session->native->userdata('test3'), NULL);
	}

	/**
	 * Test the set_flashdata() function
	 */
	public function test_set_flashdata()
	{
		// Set flashdata message for each driver
		$cmsg = 'Some flash data';
		$this->session->cookie->set_flashdata('test4', $cmsg);
		$nmsg = 'Other flash data';
		$this->session->native->set_flashdata('test4', $nmsg);

		// Simulate page reload
		$this->session->cookie->reload();
		$this->session->native->reload();

		// Verify independent messages
		$this->assertEquals($this->session->cookie->flashdata('test4'), $cmsg);
		$this->assertEquals($this->session->native->flashdata('test4'), $nmsg);

		// Simulate next page reload
		$this->session->cookie->reload();
		$this->session->native->reload();

		// Verify absence of messages
		$this->assertEquals($this->session->cookie->flashdata('test4'), NULL);
		$this->assertEquals($this->session->native->flashdata('test4'), NULL);
	}

	/**
	 * Test the keep_flashdata() function
	 */
	public function test_keep_flashdata()
	{
		// Set flashdata message for each driver
		$cmsg = 'My flash data';
		$this->session->cookie->set_flashdata('test5', $cmsg);
		$nmsg = 'Your flash data';
		$this->session->native->set_flashdata('test5', $nmsg);

		// Simulate page reload and verify independent messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($this->session->cookie->flashdata('test5'), $cmsg);
		$this->assertEquals($this->session->native->flashdata('test5'), $nmsg);

		// Keep messages
		$this->session->cookie->keep_flashdata('test5');
		$this->session->native->keep_flashdata('test5');

		// Simulate next page reload and verify message persistence
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($this->session->cookie->flashdata('test5'), $cmsg);
		$this->assertEquals($this->session->native->flashdata('test5'), $nmsg);

		// Simulate next page reload and verify absence of messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($this->session->cookie->flashdata('test5'), NULL);
		$this->assertEquals($this->session->native->flashdata('test5'), NULL);
	}

	/**
	 * Test the all_flashdata() function
	 */
	public function test_all_flashdata()
	{
		// Set a specific series of data for each driver
		$cdata = array(
			'one' => 'first',
		   	'two' => 'second',
		   	'three' => 'third',
		   	'foo' => 'bar',
		   	'bar' => 'baz'
		);
		$ndata = array(
			'one' => 'gold',
		   	'two' => 'silver',
		   	'three' => 'bronze',
		   	'foo' => 'baz',
		   	'bar' => 'foo'
		);
		$this->session->cookie->set_flashdata($cdata);
		$this->session->native->set_flashdata($ndata);

		// Simulate page reload and make sure all values are present
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($cdata, $this->session->cookie->all_flashdata());
		$this->assertEquals($ndata, $this->session->native->all_flashdata());
	}

	/**
	 * Test the set_tempdata() function
	 */
	public function test_set_tempdata()
	{
		// Set tempdata message for each driver - 1 second timeout
		$cmsg = 'Some temp data';
		$this->session->cookie->set_tempdata('test6', $cmsg, 1);
		$nmsg = 'Other temp data';
		$this->session->native->set_tempdata('test6', $nmsg, 1);

		// Simulate page reload and verify independent messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($this->session->cookie->tempdata('test6'), $cmsg);
		$this->assertEquals($this->session->native->tempdata('test6'), $nmsg);

		// Wait 2 seconds, simulate page reload and verify message absence
		sleep(2);
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($this->session->cookie->tempdata('test6'), NULL);
		$this->assertEquals($this->session->native->tempdata('test6'), NULL);
	}

	/**
	 * Test the unset_tempdata() function
	 */
	public function test_unset_tempdata()
	{
		// Set tempdata message for each driver - 1 second timeout
		$cmsg = 'My temp data';
		$this->session->cookie->set_tempdata('test7', $cmsg, 1);
		$nmsg = 'Your temp data';
		$this->session->native->set_tempdata('test7', $nmsg, 1);

		// Verify independent messages
		$this->assertEquals($this->session->cookie->tempdata('test7'), $cmsg);
		$this->assertEquals($this->session->native->tempdata('test7'), $nmsg);

		// Unset data and verify message absence
		$this->session->cookie->unset_tempdata('test7');
		$this->session->native->unset_tempdata('test7');
		$this->assertEquals($this->session->cookie->tempdata('test7'), NULL);
		$this->assertEquals($this->session->native->tempdata('test7'), NULL);
	}

	/**
	 * Test the sess_regenerate() function
	 */
	public function test_sess_regenerate()
	{
		// Get current session id, regenerate, and compare
		// Cookie driver
		$oldid = $this->session->cookie->userdata('session_id');
		$this->session->cookie->sess_regenerate();
		$newid = $this->session->cookie->userdata('session_id');
		$this->assertFalse($oldid === $newid);

		// Native driver - bug #55267 (https://bugs.php.net/bug.php?id=55267) prevents testing this
		/*$oldid = session_id();
		$this->session->native->sess_regenerate();
		$oldid = session_id();
		$this->assertFalse($oldid === $newid);*/
	}

	/**
	 * Test the sess_destroy() function
	 */
	public function test_sess_destroy()
	{
		// Set a userdata message, destroy session, and verify absence
		$msg = 'More test data';

		// Cookie driver
		$this->session->cookie->set_userdata('test8', $msg);
		$this->assertEquals($this->session->cookie->userdata('test8'), $msg);
		$this->session->cookie->sess_destroy();
		$this->assertEquals($this->session->cookie->userdata('test8'), NULL);

		// Native driver
		$this->session->native->set_userdata('test8', $msg);
		$this->assertEquals($this->session->native->userdata('test8'), $msg);
		$this->session->native->sess_destroy();
		$this->assertEquals($this->session->native->userdata('test8'), NULL);
	}
}

