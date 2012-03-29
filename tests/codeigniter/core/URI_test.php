<?php

class URI_test extends CI_TestCase {
	
	public function set_up()
	{
		$this->uri = new Mock_Core_URI();
	}

	// --------------------------------------------------------------------

	public function test_set_uri_string()
	{
		// Slashes get killed
		$this->uri->_set_uri_string('/');
		
		$a = '';
		$b =& $this->uri->uri_string;
		
		$this->assertEquals($a, $b);
		
		$this->uri->_set_uri_string('nice/uri');
		
		$a = 'nice/uri';
		
		$this->assertEquals($a, $b);
	}
	
	// --------------------------------------------------------------------

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
		
		foreach($requests as $request => $expected)
		{
			$_SERVER['SCRIPT_NAME'] = '/index.php';
			$_SERVER['REQUEST_URI'] = $request;
			
			$this->uri->_fetch_uri_string();
			$this->assertEquals($expected, $this->uri->uri_string );
		}
		
		// Test a subfolder
		$_SERVER['SCRIPT_NAME'] = '/subfolder/index.php';
		$_SERVER['REQUEST_URI'] = '/subfolder/index.php/controller/method';
		
		$this->uri->_fetch_uri_string();
		
		$a = 'controller/method';
		$b = $this->uri->uri_string;
		
		$this->assertEquals($a, $b);
		
		// death to request uri
		unset($_SERVER['REQUEST_URI']);
		
		// life to path info
		$_SERVER['PATH_INFO'] = '/controller/method/';
		
		$this->uri->_fetch_uri_string();
		
		$a = '/controller/method/';
		$b =& $this->uri->uri_string;

		$this->assertEquals($a, $b);
		
		// death to path info
		// At this point your server must be seriously drunk
		unset($_SERVER['PATH_INFO']);
		
		$_SERVER['QUERY_STRING'] = '/controller/method/';
		
		$this->uri->_fetch_uri_string();

		$a = '/controller/method/';
		$b = $this->uri->uri_string;
		
		$this->assertEquals($a, $b);
		
		// At this point your server is a labotomy victim
		
		unset($_SERVER['QUERY_STRING']);
		
		$_GET['/controller/method/'] = '';
		
		$this->uri->_fetch_uri_string();
		$this->assertEquals($a, $b);

		// Test coverage implies that these will work
		// uri_protocol: REQUEST_URI
		// uri_protocol: CLI
				
	}
	
	// --------------------------------------------------------------------

	public function test_explode_segments()
	{
		// Lets test the function's ability to clean up this mess
		$uris = array(
			'test/uri' => array('test', 'uri'),
			'/test2/uri2' => array('test2', 'uri2'),
			'//test3/test3///' => array('test3', 'test3')
		);
		
		foreach($uris as $uri => $a)
		{
			$this->uri->segments = array();
			$this->uri->uri_string = $uri;
			$this->uri->_explode_segments();
			
			$b = $this->uri->segments;
			
			$this->assertEquals($a, $b);
		}
		
	}

	// --------------------------------------------------------------------

	public function test_filter_uri()
	{
		$this->uri->config->set_item('enable_query_strings', FALSE);
		$this->uri->config->set_item('permitted_uri_chars', 'a-z 0-9~%.:_\-');
		
		$str_in = 'abc01239~%.:_-';
		$str = $this->uri->_filter_uri($str_in);

		$this->assertEquals($str, $str_in);
	}

	// --------------------------------------------------------------------

	public function test_filter_uri_escaping()
	{
		// ensure escaping even if dodgey characters are permitted
		
		$this->uri->config->set_item('enable_query_strings', FALSE);
		$this->uri->config->set_item('permitted_uri_chars', 'a-z 0-9~%.:_\-()$');

		$str = $this->uri->_filter_uri('$destroy_app(foo)');
		
		$this->assertEquals($str, '&#36;destroy_app&#40;foo&#41;');
	}

	// --------------------------------------------------------------------

    public function test_filter_uri_throws_error()
    {
		$this->setExpectedException('RuntimeException');
		
		$this->uri->config->set_item('enable_query_strings', FALSE);
		$this->uri->config->set_item('permitted_uri_chars', 'a-z 0-9~%.:_\-');
		$this->uri->_filter_uri('$this()');
    }

	// --------------------------------------------------------------------

	public function test_remove_url_suffix()
	{
		$this->uri->config->set_item('url_suffix', '.html');
		
		$this->uri->uri_string = 'controller/method/index.html';
		$this->uri->_remove_url_suffix();
		
		$this->assertEquals($this->uri->uri_string, 'controller/method/index');
		
		$this->uri->uri_string = 'controller/method/index.htmlify.html';
		$this->uri->_remove_url_suffix();
		
		$this->assertEquals($this->uri->uri_string, 'controller/method/index.htmlify');
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
		
		$a = array('a' => '1', 'b' => '2', 'c' => '3');
		$b = $this->uri->uri_to_assoc(1);
		$this->assertEquals($a, $b);
		
		$a = array('b' => '2', 'c' => '3');
		$b = $this->uri->uri_to_assoc(3);
		$this->assertEquals($a, $b);
		
		
		$this->uri->keyval = array(); // reset cache
				
		$this->uri->segments = array('a', '1', 'b', '2', 'c');
		
		$a = array('a' => '1', 'b' => '2', 'c' => FALSE);
		$b = $this->uri->uri_to_assoc(1);
		$this->assertEquals($a, $b);
		
		$this->uri->keyval = array(); // reset cache
		
		$this->uri->segments = array('a', '1');
		
		// test default
		$a = array('a' => '1', 'b' => FALSE);
		$b = $this->uri->uri_to_assoc(1, array('a', 'b'));
		$this->assertEquals($a, $b);
	}

	// --------------------------------------------------------------------

	public function test_ruri_to_assoc()
	{
		$this->uri->rsegments = array('x', '1', 'y', '2', 'z', '3');
		
		$a = array('x' => '1', 'y' => '2', 'z' => '3');
		$b = $this->uri->ruri_to_assoc(1);
		$this->assertEquals($a, $b);
		
		$a = array('y' => '2', 'z' => '3');
		$b = $this->uri->ruri_to_assoc(3);
		$this->assertEquals($a, $b);
		
		
		$this->uri->keyval = array(); // reset cache
				
		$this->uri->rsegments = array('x', '1', 'y', '2', 'z');
		
		$a = array('x' => '1', 'y' => '2', 'z' => FALSE);
		$b = $this->uri->ruri_to_assoc(1);
		$this->assertEquals($a, $b);
		
		$this->uri->keyval = array(); // reset cache
		
		$this->uri->rsegments = array('x', '1');
		
		// test default
		$a = array('x' => '1', 'y' => FALSE);
		$b = $this->uri->ruri_to_assoc(1, array('x', 'y'));
		$this->assertEquals($a, $b);

	}

	// --------------------------------------------------------------------

	public function test_assoc_to_uri()
	{
		$this->uri->config->set_item('uri_string_slashes', 'none');
		
		$arr = array('a' => 1, 'b' => 2);
		$a = 'a/1/b/2';
		$b = $this->uri->assoc_to_uri($arr);
		$this->assertEquals($a, $b);
	}

	// --------------------------------------------------------------------

	public function test_slash_segment()
	{
		$this->uri->segments[1] = 'segment';
		$this->uri->rsegments[1] = 'segment';

		$a = '/segment/';
		$b = $this->uri->slash_segment(1, 'both');
		$this->assertEquals($a, $b);
		$b = $this->uri->slash_rsegment(1, 'both');
		$this->assertEquals($a, $b);
		
		$a = '/segment';
		$b = $this->uri->slash_segment(1, 'leading');
		$this->assertEquals($a, $b);
		$b = $this->uri->slash_rsegment(1, 'leading');
		$this->assertEquals($a, $b);
		
		$a = 'segment/';
		$b = $this->uri->slash_segment(1, 'trailing');
		$this->assertEquals($a, $b);
		$b = $this->uri->slash_rsegment(1, 'trailing');
		$this->assertEquals($a, $b);
	}


}
// END URI_test Class

/* End of file URI_test.php */
/* Location: ./tests/core/URI_test.php */
