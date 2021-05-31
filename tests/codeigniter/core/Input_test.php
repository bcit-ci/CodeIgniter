<?php

class Input_test extends CI_TestCase {

	public function set_up()
	{
		// Set server variable to GET as default, since this will leave unset in STDIN env
		$_SERVER['REQUEST_METHOD'] = 'GET';

		// Set config for Input class
		$this->ci_set_config('allow_get_array',	TRUE);
		$this->ci_set_config('global_xss_filtering', FALSE);
		$this->ci_set_config('csrf_protection', FALSE);

		$security = new Mock_Core_Security('UTF-8');
		$this->input = new CI_Input($security);
	}

	// --------------------------------------------------------------------

	public function tear_down()
	{
		$_POST = [];
		$_GET = [];
	}

	// --------------------------------------------------------------------

	public function test_get_not_exists()
	{
		$this->assertSame(array(), $this->input->get());
		$this->assertNull($this->input->get('foo'));
	}

	// --------------------------------------------------------------------

	public function test_get_exist()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['foo'] = 'bar';

		$this->assertArrayHasKey('foo', $this->input->get());
		$this->assertEquals('bar', $this->input->get('foo'));
	}

	// --------------------------------------------------------------------

	public function test_get_exist_with_xss_clean()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['harm'] = "Hello, i try to <script>alert('Hack');</script> your site";

		$this->assertArrayHasKey('harm', $this->input->get());
		$this->assertEquals("Hello, i try to <script>alert('Hack');</script> your site", $this->input->get('harm'));
		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $this->input->get('harm', TRUE));
	}

	// --------------------------------------------------------------------

	public function test_post_not_exists()
	{
		$this->assertSame(array(), $this->input->post());
		$this->assertNull($this->input->post('foo'));
	}

	// --------------------------------------------------------------------

	public function test_post_exist()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['foo'] = 'bar';

		$this->assertArrayHasKey('foo', $this->input->post());
		$this->assertEquals('bar', $this->input->post('foo'));
	}

	// --------------------------------------------------------------------

	public function test_post_exist_with_xss_clean()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['harm'] = "Hello, i try to <script>alert('Hack');</script> your site";

		$this->assertArrayHasKey('harm', $this->input->post());
		$this->assertEquals("Hello, i try to <script>alert('Hack');</script> your site", $this->input->post('harm'));
		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $this->input->post('harm', TRUE));
	}

	// --------------------------------------------------------------------

	public function test_post_get()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['foo'] = 'bar';

		$this->assertEquals('bar', $this->input->post_get('foo'));
	}

	// --------------------------------------------------------------------

	public function test_post_get_array_notation()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['foo'] = array('bar' => 'baz');
		$barArray = array('bar' => 'baz');

		$this->assertEquals('baz', $this->input->get_post('foo[bar]'));
		$this->assertEquals($barArray, $this->input->get_post('foo[]'));
		$this->assertNull($this->input->get_post('foo[baz]'));

		$this->assertEquals('baz', $this->input->post_get('foo[bar]'));
		$this->assertEquals($barArray, $this->input->post_get('foo[]'));
		$this->assertNull($this->input->post_get('foo[baz]'));
	}

	// --------------------------------------------------------------------

	public function test_get_post()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['foo'] = 'bar';

		$this->assertEquals('bar', $this->input->get_post('foo'));
	}

	// --------------------------------------------------------------------

	public function test_get_post_array_notation()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_GET['foo'] = array('bar' => 'baz');
		$barArray = array('bar' => 'baz');

		$this->assertEquals('baz', $this->input->get_post('foo[bar]'));
		$this->assertEquals($barArray, $this->input->get_post('foo[]'));
		$this->assertNull($this->input->get_post('foo[baz]'));

		$this->assertEquals('baz', $this->input->post_get('foo[bar]'));
		$this->assertEquals($barArray, $this->input->post_get('foo[]'));
		$this->assertNull($this->input->post_get('foo[baz]'));
	}

	// --------------------------------------------------------------------

	public function test_cookie()
	{
		$_COOKIE['foo'] = 'bar';
		$this->assertEquals('bar', $this->input->cookie('foo'));
		$this->assertNull($this->input->cookie('bar'));
	}

	// --------------------------------------------------------------------

	public function test_server()
	{
		$this->assertEquals('GET', $this->input->server('REQUEST_METHOD'));
	}

	// --------------------------------------------------------------------

	public function test_fetch_from_array()
	{
		$reflection = new ReflectionMethod($this->input, '_fetch_from_array');
		$reflection->setAccessible(TRUE);

		$data = array(
			'foo' => 'bar',
			'harm' => 'Hello, i try to <script>alert(\'Hack\');</script> your site',
		);

		$foo      = $reflection->invokeArgs($this->input, [&$data, 'foo']);
		$harm     = $reflection->invokeArgs($this->input, [&$data, 'harm']);
		$harmless = $reflection->invokeArgs($this->input, [&$data, 'harm', TRUE]);

		$this->assertEquals('bar', $foo);
		$this->assertEquals("Hello, i try to <script>alert('Hack');</script> your site", $harm);
		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless);

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST['foo'] = array('bar' => 'baz');
		$barArray = array('bar' => 'baz');

		$this->assertEquals('baz', $this->input->post('foo[bar]'));
		$this->assertEquals($barArray, $this->input->post('foo[]'));
		$this->assertNull($this->input->post('foo[baz]'));
	}

	// --------------------------------------------------------------------

	public function test_valid_ip()
	{
		$this->assertTrue($this->input->valid_ip('192.18.0.1'));
		$this->assertTrue($this->input->valid_ip('192.18.0.1', 'ipv4'));
		$this->assertFalse($this->input->valid_ip('555.0.0.0'));
		$this->assertFalse($this->input->valid_ip('2001:db8:0:85a3::ac1f:8001', 'ipv4'));

		// v6 tests
		$this->assertFalse($this->input->valid_ip('192.18.0.1', 'ipv6'));

		$ip_v6 = array(
			'2001:0db8:0000:85a3:0000:0000:ac1f:8001',
			'2001:db8:0:85a3:0:0:ac1f:8001',
			'2001:db8:0:85a3::ac1f:8001'
		);

		foreach ($ip_v6 as $ip)
		{
			$this->assertTrue($this->input->valid_ip($ip));
			$this->assertTrue($this->input->valid_ip($ip, 'ipv6'));
		}
	}

	// --------------------------------------------------------------------

	public function test_method()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->assertEquals('get', $this->input->method());
		$this->assertEquals('GET', $this->input->method(TRUE));
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->assertEquals('post', $this->input->method());
		$this->assertEquals('POST', $this->input->method(TRUE));
	}

	// --------------------------------------------------------------------

	public function test_is_ajax_request()
	{
		$this->assertFalse($this->input->is_ajax_request());
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'test';
		$this->assertFalse($this->input->is_ajax_request());
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$this->assertTrue($this->input->is_ajax_request());
	}

	// --------------------------------------------------------------------

	public function test_input_stream()
	{
		$this->markTestSkipped('TODO: Find a way to test input://');
	}

	// --------------------------------------------------------------------

	public function test_set_cookie()
	{
		$this->markTestSkipped('TODO: Find a way to test HTTP headers');
	}

	// --------------------------------------------------------------------

	public function test_get_request_header()
	{
		$this->markTestSkipped('TODO: Find a way to test HTTP headers');
	}

	// --------------------------------------------------------------------

	public function test_ip_address()
	{
		$reflection = new ReflectionProperty($this->input, 'ip_address');
		$reflection->setAccessible(TRUE);

		$reflection->setValue($this->input, '127.0.0.1');
		$this->assertEquals('127.0.0.1', $this->input->ip_address());

		// 127.0.0.1 is set in our Bootstrap file
		$reflection->setValue($this->input, FALSE);
		$this->assertEquals('127.0.0.1', $this->input->ip_address());

		// Invalid
		$_SERVER['REMOTE_ADDR'] = 'invalid_ip_address';
		$reflection->setValue($this->input, FALSE); // reset cached value
		$this->assertEquals('0.0.0.0', $this->input->ip_address());

		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		// Proxy_ips tests
		$reflection->setValue($this->input, FALSE);
		$this->ci_set_config('proxy_ips', '127.0.0.3, 127.0.0.4, 127.0.0.2');
		$_SERVER['HTTP_CLIENT_IP'] = '127.0.0.2';
		$this->assertEquals('127.0.0.1', $this->input->ip_address());

		// Invalid spoof
		$reflection->setValue($this->input, FALSE);
		$this->ci_set_config('proxy_ips', 'invalid_ip_address');
		$_SERVER['HTTP_CLIENT_IP'] = 'invalid_ip_address';
		$this->assertEquals('127.0.0.1', $this->input->ip_address());

		$reflection->setValue($this->input, FALSE);
		$this->ci_set_config('proxy_ips', 'http://foo/bar/baz, 127.0.0.1/1');
		$_SERVER['HTTP_CLIENT_IP'] = '127.0.0.1';
		$this->assertEquals('127.0.0.1', $this->input->ip_address());

		$reflection->setValue($this->input, FALSE);
		$this->ci_set_config('proxy_ips', 'http://foo/bar/baz, 127.0.0.2');
		$_SERVER['HTTP_CLIENT_IP'] = '127.0.0.2';
		$_SERVER['REMOTE_ADDR'] = '127.0.0.2';
		$this->assertEquals('127.0.0.2', $this->input->ip_address());

		// IPv6
		$reflection->setValue($this->input, FALSE);
		$this->ci_set_config('proxy_ips', 'FE80:0000:0000:0000:0202:B3FF:FE1E:8329/1, FE80:0000:0000:0000:0202:B3FF:FE1E:8300/2');
		$_SERVER['HTTP_CLIENT_IP'] = 'FE80:0000:0000:0000:0202:B3FF:FE1E:8300';
		$_SERVER['REMOTE_ADDR'] = 'FE80:0000:0000:0000:0202:B3FF:FE1E:8329';
		$this->assertEquals('FE80:0000:0000:0000:0202:B3FF:FE1E:8300', $this->input->ip_address());

		$reflection->setValue($this->input, FALSE);
		$this->ci_set_config('proxy_ips', '0::/32');
		$_SERVER['HTTP_CLIENT_IP'] = '127.0.0.7';
		$_SERVER['REMOTE_ADDR'] = '0000:0000:0000:0000:0000:0000:0000:0001';
		$this->assertEquals('127.0.0.7', $this->input->ip_address());

		$reflection->setValue($this->input, FALSE);
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1'; // back to reality
	}

	// --------------------------------------------------------------------

	public function test_user_agent()
	{
		$_SERVER['HTTP_USER_AGENT'] = 'test';
		$this->assertEquals('test', $this->input->user_agent());
	}
}
