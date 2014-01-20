<?php

class URI_test extends CI_TestCase {

	public function set_up()
	{
		$this->uri = new Mock_Core_URI();
	}

	// --------------------------------------------------------------------

	/* As of the following commit, _set_uri_string() is a protected method:

		https://github.com/EllisLab/CodeIgniter/commit/d461934184d95b0cfb2feec93f27b621ef72a5c2

	public function test_set_uri_string()
	{
		// Slashes get killed
		$this->uri->_set_uri_string('/');
		$this->assertEquals('', $this->uri->uri_string);

		$this->uri->_set_uri_string('nice/uri');
		$this->assertEquals('nice/uri', $this->uri->uri_string);
	}
	*/

	// --------------------------------------------------------------------

	/*

		This has been moved to the constructor

	public function test_fetch_uri_string()
	{
		define('SELF', 'index.php');

		// uri_protocol: AUTO
		$this->uri->config->set_item('uri_protocol', 'AUTO');

		// Test a variety of request uris
		$requests = array(
			'/index.php/controller/method' => 'controller/method',
			'/index.php?/controller/method' => 'controller/method',
			'/index.php?/controller/method/?var=foo' => 'controller/method'
		);

		foreach ($requests as $request => $expected)
		{
			$_SERVER['SCRIPT_NAME'] = '/index.php';
			$_SERVER['REQUEST_URI'] = $request;

			$this->uri->_fetch_uri_string();
			$this->assertEquals($expected, $this->uri->uri_string);
		}

		// Test a subfolder
		$_SERVER['SCRIPT_NAME'] = '/subfolder/index.php';
		$_SERVER['REQUEST_URI'] = '/subfolder/index.php/controller/method';

		$this->uri->_fetch_uri_string();
		$this->assertEquals('controller/method', $this->uri->uri_string);

		// death to request uri
		unset($_SERVER['REQUEST_URI']);

		// life to path info
		$_SERVER['PATH_INFO'] = '/controller/method/';

		$this->uri->_fetch_uri_string();
		$this->assertEquals('controller/method', $this->uri->uri_string);

		// death to path info
		// At this point your server must be seriously drunk
		unset($_SERVER['PATH_INFO']);

		$_SERVER['QUERY_STRING'] = '/controller/method/';

		$this->uri->_fetch_uri_string();
		$this->assertEquals('controller/method', $this->uri->uri_string);

		// At this point your server is a labotomy victim
		unset($_SERVER['QUERY_STRING']);

		$_GET['/controller/method/'] = '';

		$this->uri->_fetch_uri_string();
		$this->assertEquals('controller/method', $this->uri->uri_string);

		// Test coverage implies that these will work
		// uri_protocol: REQUEST_URI
		// uri_protocol: CLI
	}
	*/

	// --------------------------------------------------------------------

	/*

		This has been moved into _set_uri_string()

	public function test_explode_segments()
	{
		// Let's test the function's ability to clean up this mess
		$uris = array(
			'test/uri' => array('test', 'uri'),
			'/test2/uri2' => array('test2', 'uri2'),
			'//test3/test3///' => array('test3', 'test3')
		);

		foreach ($uris as $uri => $a)
		{
			$this->uri->segments = array();
			$this->uri->uri_string = $uri;
			$this->uri->_explode_segments();

			$this->assertEquals($a, $this->uri->segments);
		}
	}
	*/
	// --------------------------------------------------------------------

	public function test_filter_uri()
	{
		$this->uri->_set_permitted_uri_chars('a-z 0-9~%.:_\-');

		$str_in = 'abc01239~%.:_-';
		$str = $this->uri->filter_uri($str_in);

		$this->assertEquals($str, $str_in);
	}

	// --------------------------------------------------------------------

	public function test_filter_uri_escaping()
	{
		// ensure escaping even if dodgey characters are permitted
		$this->uri->_set_permitted_uri_chars('a-z 0-9~%.:_\-()$');

		$str = $this->uri->filter_uri('$destroy_app(foo)');

		$this->assertEquals($str, '&#36;destroy_app&#40;foo&#41;');
	}

	// --------------------------------------------------------------------

	public function test_filter_uri_throws_error()
	{
		$this->setExpectedException('RuntimeException');

		$this->uri->config->set_item('enable_query_strings', FALSE);
		$this->uri->_set_permitted_uri_chars('a-z 0-9~%.:_\-');
		$this->uri->filter_uri('$this()');
	}

	// --------------------------------------------------------------------

	public function test_segment()
	{
		$this->uri->segments = array(1 => 'controller');
		$this->assertEquals($this->uri->segment(1), 'controller');
		$this->assertEquals($this->uri->segment(2, 'default'), 'default');
	}

	// --------------------------------------------------------------------

	public function test_rsegment()
	{
		$this->uri->rsegments = array(1 => 'method');
		$this->assertEquals($this->uri->rsegment(1), 'method');
		$this->assertEquals($this->uri->rsegment(2, 'default'), 'default');
	}

	// --------------------------------------------------------------------

	public function test_uri_to_assoc()
	{
		$this->uri->segments = array('a', '1', 'b', '2', 'c', '3');

		$this->assertEquals(
			array('a' => '1', 'b' => '2', 'c' => '3'),
			$this->uri->uri_to_assoc(1)
		);

		$this->assertEquals(
			array('b' => '2', 'c' => '3'),
			$this->uri->uri_to_assoc(3)
		);

		$this->uri->keyval = array(); // reset cache
		$this->uri->segments = array('a', '1', 'b', '2', 'c');

		$this->assertEquals(
			array('a' => '1', 'b' => '2', 'c' => FALSE),
			$this->uri->uri_to_assoc(1)
		);

		$this->uri->keyval = array(); // reset cache
		$this->uri->segments = array('a', '1');

		// test default
		$this->assertEquals(
			array('a' => '1', 'b' => FALSE),
			$this->uri->uri_to_assoc(1, array('a', 'b'))
		);
	}

	// --------------------------------------------------------------------

	public function test_ruri_to_assoc()
	{
		$this->uri->rsegments = array('x', '1', 'y', '2', 'z', '3');

		$this->assertEquals(
			array('x' => '1', 'y' => '2', 'z' => '3'),
			$this->uri->ruri_to_assoc(1)
		);

		$this->assertEquals(
			array('y' => '2', 'z' => '3'),
			$this->uri->ruri_to_assoc(3)
		);

		$this->uri->keyval = array(); // reset cache
		$this->uri->rsegments = array('x', '1', 'y', '2', 'z');

		$this->assertEquals(
			array('x' => '1', 'y' => '2', 'z' => FALSE),
			$this->uri->ruri_to_assoc(1)
		);

		$this->uri->keyval = array(); // reset cache
		$this->uri->rsegments = array('x', '1');

		// test default
		$this->assertEquals(
			array('x' => '1', 'y' => FALSE),
			$this->uri->ruri_to_assoc(1, array('x', 'y'))
		);
	}

	// --------------------------------------------------------------------

	public function test_assoc_to_uri()
	{
		$this->uri->config->set_item('uri_string_slashes', 'none');
		$this->assertEquals('a/1/b/2', $this->uri->assoc_to_uri(array('a' => '1', 'b' => '2')));
	}

	// --------------------------------------------------------------------

	public function test_slash_segment()
	{
		$this->uri->segments[1] = 'segment';
		$this->uri->rsegments[1] = 'segment';

		$this->assertEquals('/segment/', $this->uri->slash_segment(1, 'both'));
		$this->assertEquals('/segment/', $this->uri->slash_rsegment(1, 'both'));

		$a = '/segment';
		$this->assertEquals('/segment', $this->uri->slash_segment(1, 'leading'));
		$this->assertEquals('/segment', $this->uri->slash_rsegment(1, 'leading'));

		$this->assertEquals('segment/', $this->uri->slash_segment(1, 'trailing'));
		$this->assertEquals('segment/', $this->uri->slash_rsegment(1, 'trailing'));
	}

}

/* End of file URI_test.php */
/* Location: ./tests/core/URI_test.php */