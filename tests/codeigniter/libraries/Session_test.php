<?php

/**
 * Session driver library unit test
 */
class Session_test extends CI_TestCase {

	protected $settings = array(
		'use_cookies' => 0,
		'use_only_cookies' => 0,
		'cache_limiter' => FALSE
	);
	protected $setting_vals = array();
	protected $cookie_vals;
	protected $session;

	/**
	 * Set up test framework
	 */
	public function set_up()
	{
return;
		// Override settings
		foreach ($this->settings as $name => $value) {
			$this->setting_vals[$name] = ini_get('session.'.$name);
			ini_set('session.'.$name, $value);
		}

		// Start with clean environment
		$this->cookie_vals = $_COOKIE;
		$_COOKIE = array();

		// Set subclass prefix to match our mock
		$this->ci_set_config('subclass_prefix', 'Mock_Libraries_');

		// Establish necessary support classes
		$ci = $this->ci_instance();
		$ldr = $this->ci_core_class('load');
		$ci->load = new $ldr();
		$security = new Mock_Core_Security('UTF-8');
		$ci->input = new CI_Input($security);

		// Make sure string helper is available
		$this->ci_vfs_clone('system/helpers/string_helper.php');

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
			'encryption_key' => 'foobar'
		);
		$this->session = new Mock_Libraries_Session($config);
	}

	/**
	 * Tear down test framework
	 */
	public function tear_down()
	{
return;
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
return;
		// Set userdata values for each driver
		$key1 = 'test1';
		$ckey2 = 'test2';
		$nkey2 = 'test3';
		$cmsg1 = 'Some test data';
		$cmsg2 = 42;
		$nmsg1 = 'Other test data';
		$nmsg2 = TRUE;
		$this->session->cookie->set_userdata($key1, $cmsg1);
		$this->session->set_userdata($ckey2, $cmsg2);
		$this->session->native->set_userdata($key1, $nmsg1);
		$this->session->set_userdata($nkey2, $nmsg2);

		// Verify independent messages
		$this->assertEquals($cmsg1, $this->session->cookie->userdata($key1));
		$this->assertEquals($nmsg1, $this->session->native->userdata($key1));

		// Verify pre-selected driver sets
		$this->assertEquals($cmsg2, $this->session->cookie->userdata($ckey2));
		$this->assertEquals($nmsg2, $this->session->native->userdata($nkey2));

		// Verify no crossover
		$this->assertNull($this->session->cookie->userdata($nkey2));
		$this->assertNull($this->session->native->userdata($ckey2));
	}

	/**
	 * Test the has_userdata() function
	 */
	public function test_has_userdata()
	{
return;
		// Set a userdata value for each driver
		$key = 'hastest';
		$cmsg = 'My test data';
		$this->session->cookie->set_userdata($key, $cmsg);
		$nmsg = 'Your test data';
		$this->session->native->set_userdata($key, $nmsg);

		// Verify values exist
		$this->assertTrue($this->session->cookie->has_userdata($key));
		$this->assertTrue($this->session->native->has_userdata($key));

		// Verify non-existent values
		$nokey = 'hasnot';
		$this->assertFalse($this->session->cookie->has_userdata($nokey));
		$this->assertFalse($this->session->native->has_userdata($nokey));
	}

	/**
	 * Test the all_userdata() function
	 */
	public function test_all_userdata()
	{
return;
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
		$call = $this->session->cookie->userdata();
		foreach ($cdata as $key => $value) {
			$this->assertEquals($value, $call[$key]);
		}
		$nall = $this->session->native->userdata();
		foreach ($ndata as $key => $value) {
			$this->assertEquals($value, $nall[$key]);
		}
	}

	/**
	 * Test the unset_userdata() function
	 */
	public function test_unset_userdata()
	{
return;
		// Set a userdata message for each driver
		$key = 'untest';
		$cmsg = 'Other test data';
		$this->session->cookie->set_userdata($key, $cmsg);
		$nmsg = 'Sundry test data';
		$this->session->native->set_userdata($key, $nmsg);

		// Verify independent messages
		$this->assertEquals($this->session->cookie->userdata($key), $cmsg);
		$this->assertEquals($this->session->native->userdata($key), $nmsg);

		// Unset them and verify absence
		$this->session->cookie->unset_userdata($key);
		$this->session->native->unset_userdata($key);
		$this->assertNull($this->session->cookie->userdata($key));
		$this->assertNull($this->session->native->userdata($key));
	}

	/**
	 * Test the flashdata() functions
	 */
	public function test_flashdata()
	{
return;
		// Set flashdata message for each driver
		$key = 'fltest';
		$cmsg = 'Some flash data';
		$this->session->cookie->set_flashdata($key, $cmsg);
		$nmsg = 'Other flash data';
		$this->session->native->set_flashdata($key, $nmsg);

		// Simulate page reload
		$this->session->cookie->reload();
		$this->session->native->reload();

		// Verify independent messages
		$this->assertEquals($cmsg, $this->session->cookie->flashdata($key));
		$this->assertEquals($nmsg, $this->session->native->flashdata($key));

		// Simulate next page reload
		$this->session->cookie->reload();
		$this->session->native->reload();

		// Verify absence of messages
		$this->assertNull($this->session->cookie->flashdata($key));
		$this->assertNull($this->session->native->flashdata($key));
	}

	/**
	 * Test the keep_flashdata() function
	 */
	public function test_keep_flashdata()
	{
return;
		// Set flashdata message for each driver
		$key = 'kfltest';
		$cmsg = 'My flash data';
		$this->session->cookie->set_flashdata($key, $cmsg);
		$nmsg = 'Your flash data';
		$this->session->native->set_flashdata($key, $nmsg);

		// Simulate page reload and verify independent messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($cmsg, $this->session->cookie->flashdata($key));
		$this->assertEquals($nmsg, $this->session->native->flashdata($key));

		// Keep messages
		$this->session->cookie->keep_flashdata($key);
		$this->session->native->keep_flashdata($key);

		// Simulate next page reload and verify message persistence
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($cmsg, $this->session->cookie->flashdata($key));
		$this->assertEquals($nmsg, $this->session->native->flashdata($key));

		// Simulate next page reload and verify absence of messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertNull($this->session->cookie->flashdata($key));
		$this->assertNull($this->session->native->flashdata($key));
	}

	public function test_keep_flashdata_with_array()
	{
return;
		// Set flashdata array for each driver
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
		$kdata = array(
			'one',
			'two',
			'three',
			'foo',
			'bar'
		);
		$this->session->cookie->set_flashdata($cdata);
		$this->session->native->set_flashdata($ndata);

		// Simulate page reload and verify independent messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($cdata, $this->session->cookie->flashdata());
		$this->assertEquals($ndata, $this->session->native->flashdata());

		// Keep messages
		$this->session->cookie->keep_flashdata($kdata);
		$this->session->native->keep_flashdata($kdata);

		// Simulate next page reload and verify message persistence
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($cdata, $this->session->cookie->flashdata());
		$this->assertEquals($ndata, $this->session->native->flashdata());

		// Simulate next page reload and verify absence of messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEmpty($this->session->cookie->flashdata());
		$this->assertEmpty($this->session->native->flashdata());
	}

	/**
	 * Test the all_flashdata() function
	 */
	public function test_all_flashdata()
	{
return;
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
		$this->assertEquals($cdata, $this->session->cookie->flashdata());
		$this->assertEquals($ndata, $this->session->native->flashdata());
	}

	/**
	 * Test the tempdata() functions
	 */
	public function test_set_tempdata()
	{
return;
		// Set tempdata message for each driver - 1 second timeout
		$key = 'tmptest';
		$cmsg = 'Some temp data';
		$this->session->cookie->set_tempdata($key, $cmsg, 1);
		$nmsg = 'Other temp data';
		$this->session->native->set_tempdata($key, $nmsg, 1);

		// Simulate page reload and verify independent messages
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertEquals($cmsg, $this->session->cookie->tempdata($key));
		$this->assertEquals($nmsg, $this->session->native->tempdata($key));

		// Wait 2 seconds, simulate page reload and verify message absence
		sleep(2);
		$this->session->cookie->reload();
		$this->session->native->reload();
		$this->assertNull($this->session->cookie->tempdata($key));
		$this->assertNull($this->session->native->tempdata($key));
	}

	/**
	 * Test the unset_tempdata() function
	 */
	public function test_unset_tempdata()
	{
return;
		// Set tempdata message for each driver - 1 second timeout
		$key = 'utmptest';
		$cmsg = 'My temp data';
		$this->session->cookie->set_tempdata($key, $cmsg, 1);
		$nmsg = 'Your temp data';
		$this->session->native->set_tempdata($key, $nmsg, 1);

		// Verify independent messages
		$this->assertEquals($cmsg, $this->session->cookie->tempdata($key));
		$this->assertEquals($nmsg, $this->session->native->tempdata($key));

		// Unset data and verify message absence
		$this->session->cookie->unset_tempdata($key);
		$this->session->native->unset_tempdata($key);
		$this->assertNull($this->session->cookie->tempdata($key));
		$this->assertNull($this->session->native->tempdata($key));
	}

	/**
	 * Test the sess_regenerate() function
	 */
	public function test_sess_regenerate()
	{
return;
		// Get current session id, regenerate, and compare
		// Cookie driver
		$oldid = $this->session->cookie->userdata('session_id');
		$this->session->cookie->sess_regenerate();
		$newid = $this->session->cookie->userdata('session_id');
		$this->assertNotEquals($oldid, $newid);

		// Native driver - bug #55267 (https://bugs.php.net/bug.php?id=55267) prevents testing this
		// $oldid = session_id();
		// $this->session->native->sess_regenerate();
		// $oldid = session_id();
		// $this->assertNotEquals($oldid, $newid);
	}

	/**
	 * Test the sess_destroy() function
	 */
	public function test_sess_destroy()
	{
return;
		// Set a userdata message, destroy session, and verify absence
		$key = 'dsttest';
		$msg = 'More test data';

		// Cookie driver
		$this->session->cookie->set_userdata($key, $msg);
		$this->assertEquals($msg, $this->session->cookie->userdata($key));
		$this->session->cookie->sess_destroy();
		$this->assertNull($this->session->cookie->userdata($key));

		// Native driver
		$this->session->native->set_userdata($key, $msg);
		$this->assertEquals($msg, $this->session->native->userdata($key));
		$this->session->native->sess_destroy();
		$this->assertNull($this->session->native->userdata($key));
	}

}
