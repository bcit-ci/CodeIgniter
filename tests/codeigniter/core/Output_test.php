<?php

class Output_test extends CI_TestCase {
	private $ci;

	/**
	 * Set up for each test
	 */
	public function set_up()
	{
		// Get instance
		$this->ci = $this->ci_instance();

		// Create output object
		$this->output = new Mock_Core_Output();
	}

	/**
	 * Test get/set output and stack operations
	 *
	 * covers	CI_Output::get_output
	 * covers	CI_Output::set_output
	 * covers	CI_Output::append_output
	 * covers	CI_Output::stack_push
	 * covers	CI_Output::stack_pop
	 * covers	CI_Output::stack_level
	 */
	public function test_output()
	{
		// Do we start at level 0?
		$this->assertEquals(1, $this->output->stack_level());

		// Append output and check it
		$out1 = 'First blood ';
		$this->output->append_output($out1);
		$this->assertEquals($out1, $this->output->get_output());

		// Add a new level of output and check it
		$out2 = 'Second level ';
		$this->output->stack_push($out2);
		$this->assertEquals(2, $this->output->stack_level());
		$this->assertEquals($out2, $this->output->get_output());

		// Check the whole stack
		$this->assertEquals($out1.$out2, $this->output->get_output(TRUE));

		// Overwrite current level and check
		$out2 = 'Second date ';
		$this->output->set_output($out2);
		$this->assertEquals($out2, $this->output->get_output());
		$this->assertEquals($out1.$out2, $this->output->get_output(TRUE));

		// Overwrite whole stack
		$out1 = 'First mate ';
		$this->output->set_output($out1, TRUE);
		$this->assertEquals(1, $this->output->stack_level());
		$this->assertEquals($out1, $this->output->get_output(TRUE));

		// Make sure pop empties but doesn't remove first level
		$this->assertEquals($out1, $this->output->stack_pop());
		$this->assertEquals(1, $this->output->stack_level());
		$this->assertEquals('', $this->output->get_output());

		// Add another
		$out2 = 'Second hand ';
		$this->output->stack_push($out2);
		$this->assertEquals(2, $this->output->stack_level());
		$this->assertEquals($out2, $this->output->get_output(TRUE));

		// Restore first level
		$this->output->set_output($out1, 1);
		$this->assertEquals($out1.$out2, $this->output->get_output(TRUE));

		// Remove second level
		$this->assertEquals($out2, $this->output->stack_pop());
		$this->assertEquals(1, $this->output->stack_level());
		$this->assertEquals($out1, $this->output->get_output(TRUE));
	}

	/**
	 * Test setting a header
	 *
	 * covers	CI_Output::set_header
	 */
	public function test_set_header()
	{
		// Set a header
		$header = 'test-header';
		$this->output->set_header($header);

		// Did it get added with the replace flag?
		$expect = array(array($header, TRUE));
		$this->assertEquals($expect, $this->output->headers);

		// Does a second header honor no replace?
		$header2 = 'another-header';
		$this->output->set_header($header2, FALSE);
		$expect[] = array($header2, FALSE);
		$this->assertEquals($expect, $this->output->headers);
	}

	/**
	 * Test setting content-type
	 *
	 * covers	CI_Output::set_content_type
	 * covers	CI_Output::get_content_type
	 */
	public function test_content_type()
	{
		// Are mimes uninitialized?
		$this->assertNull($this->output->mimes);

		// Mock config
		$this->_mock_config();

		// Create mimes config
		$gif_ext = 'gif';
		$gif_type = 'image/gif';
		$pdf_ext = 'pdf';
		$pdf_type = 'application/pdf';
		$mimes = array(
			$gif_ext => $gif_type,
			$pdf_ext => array($pdf_type, 'application/x-download')
		);
		$this->ci->config->to_get($mimes);

		// Set gif type by extension and custom charset
		$ascii = 'US-ASCII';
		$this->output->set_content_type($gif_ext, $ascii);

		// Did mimes get initialized?
		$this->assertEquals($mimes, $this->output->mimes);

		// Did the mime type and content header get set?
		$this->assertEquals($gif_type, $this->output->get_mime_type());
		$cseq = '; charset=';
		$this->assertEquals($gif_type.$cseq.strtolower($ascii), $this->output->get_content_type());

		// Do leading dot, default charset, and multi-definitions work?
		$this->output->headers = array();
		$this->output->set_content_type('.'.$pdf_ext);
		$charset = strtolower($this->ci->config->item('charset'));
		$this->assertEquals($pdf_type.$cseq.$charset, $this->output->get_content_type());

		// Does an unsupported mime type pass through?
		$bad_ext = '.bad';
		$this->output->headers = array();
		$this->output->set_content_type($bad_ext);
		$this->assertEquals($bad_ext.$cseq.$charset, $this->output->get_content_type());

		// Can we set a type directly?
		$my_type = 'text/special';
		$this->output->headers = array();
		$this->output->set_content_type($my_type);
		$this->assertEquals($my_type.$cseq.$charset, $this->output->get_content_type());
	}

	/**
	 * Test enabling/disabling profiler
	 *
	 * covers	CI_Output::enable_profiler
	 */
	public function test_enable_profiler()
	{
		// Enable
		$this->output->enable_profiler();
		$this->assertTrue($this->output->enable_profiler);

		// Disable
		$this->output->enable_profiler(FALSE);
		$this->assertFalse($this->output->enable_profiler);
	}

	/**
	 * Test output display
	 *
	 * covers	CI_Output::_display
	 * covers	CI_Output::minify
	 * covers	CI_Output::cache
	 * covers	CI_Output::_write_cache
	 * covers	CI_Output::_display_cache
	 * covers	CI_Output::set_profiler_sections
	 */
	public function test_display()
	{
		// Mock up Config
		$this->_mock_config();

		// Mock up support objects
		$this->_mock_display();
		$elapsed = '12.3456';
		$profile = '<div>App Profile Stuff</div>';
		$uri_string = '/foo/bar/baz';
		$this->ci->benchmark->elapsed = $elapsed;
		$this->ci->uri->uri_string = $uri_string;
		$this->ci->profiler->run_out = $profile;

		// Set sections
		$sections = array(
			'benchmarks' => TRUE,
			'get' => FALSE,
			'memory_usage' => TRUE,
			'post' => FALSE,
			'uri_string' => TRUE,
			'controller_info' => TRUE,
			'queries' => FALSE,
			'http_headers' => TRUE,
			'session_data' => FALSE,
			'config' => TRUE,
			'query_toggle_count' => 42
		);
		$this->output->set_profiler_sections($sections);

		// Did they get set correctly?
		$this->assertEquals($sections, $this->output->get_profiler_sections());

		// Mock up Controller w/o _output
		$this->_mock_controller();

		// Set output
		$time = '{elapsed_time}';
		$parts = array(
			'<html><head><title>Test</title></head><body><p>Some test content</p><p>',
			'time' => $time,
			'</p></body></html>'
		);
		$preout = implode($parts);
		$this->output->set_output($preout);

		// Cache and capture output
		$this->output->cache(5);
		ob_start();
		$this->output->_display();

		// Did we get the right output?
		$parts['time'] = $elapsed;
		$expect = implode($parts);
		$this->assertEquals($expect, ob_get_clean());

		// Did our cache file get written?
		$path = $this->ci->config->item('cache_path').
			md5($this->ci->config->item('base_url').$this->ci->config->item('index_page').$uri_string);
		$this->assertFileExists($path);

		// Does _display_cache restore the output?
		$this->output->set_output('', TRUE);
		$this->assertEmpty($this->output->get_output(TRUE));
		$this->output->_display_cache();
		$this->assertEquals($preout, $this->output->get_output(TRUE));

		// Remove routed Controller
		unset($this->ci->routed);

		// Do we get the right "cached" output?
		ob_start();
		$this->output->_display();
		$this->assertEquals($expect, ob_get_clean());

		// Mock up Controller w/ _output
		$this->_mock_controller(TRUE);

		// Enable Profiler and Minify
		$this->output->enable_profiler();
		$this->ci_set_config('minify_output', TRUE);

		// Set output
		$pmess = '<p> Some   content </p>';
		$pclean = '<p>Some content</p>';
		$comment = '<!-- Remove this invisible stuff -->';
		$parts = array(
			'<html><head><title>Test</title></head><body>',
			'p' => $pmess,
			'comment' => $comment,
			'<pre>Some  spaced  content</pre>',
			'<code>    Indented code;</code>',
			'<textarea> <!-- Captured Comment --> Text  </textarea>',
			'<script> var foo = " spacy  quoty  stuff "; </script>',
			'profile' => '',
			'</body></html>'
		);
		$out = implode($parts);
		$this->output->set_output($out);

		// Display output via Controller
		$this->output->_display();

		// Did we get the expected output?
		$parts['p'] = $pclean;
		$parts['comment'] = '';
		$parts['profile'] = $profile;
		$this->assertEquals(implode($parts), $this->ci->routed->output);

		// Did the profiler get our sections?
		$this->assertEquals($sections, $this->ci->profiler->sections);
	}

	/**
	 * Test access to final_output
	 *
	 * covers	CI_Output::__get
	 */
	public function test_final_output()
	{
		// Set multiple output levels
		$out1 = 'Prime content ';
		$this->output->set_output($out1);
		$out2 = 'Secondary stuff';
		$this->output->stack_push($out2);

		// Can we get the output string through the property name?
		$this->assertEquals($out1.$out2, $this->output->final_output);
	}

	/**
	 * Mock up Config object
	 */
	private function _mock_config()
	{
		// Set up VFS with cache dir
		$dir = 'cache';
		$this->ci_vfs_setup();
		$this->cache_root = $this->ci_vfs_mkdir($dir, $this->ci_app_root);
		$this->cache_path = $this->ci_vfs_path($dir.'/', $this->ci_app_path);

		// Set up config
		$config = array(
			'charset' => 'UTF-8',
			'minify_output' => FALSE,
			'compress_output' => FALSE,
			'cache_path' => $this->cache_path,
			'base_url' => 'http://localhost/',
			'index_page' => 'index.php',
		);
		$this->ci_set_config($config);
	}

	/**
	 * Mock up Controller object
	 */
	private function _mock_controller($out = FALSE)
	{
		// Mock up Controller class
		// We just need to see what output was passed
		$class = 'Output_Controller'.($out ? '_Out' : '');
		if (!class_exists($class))
		{
			$code = 'class '.$class.' { ';
			if ($out) {
				$code .= 'public $output = \'\'; public function _output($output) { $this->output = $output; } ';
			}
			$code .= '}';
			eval($code);
		}
		$this->ci->routed = new $class();
	}

	/**
	 * Mock up display objects
	 */
	private function _mock_display()
	{
		// Mock up Benchmark object
		// We just need to return a timestamp
		$class = 'Output_Benchmark';
		if (!class_exists($class))
		{
			$code = 'class '.$class.' { public $elapsed = \'\'; '.
				'public function elapsed_time($arg1, $arg2) { return $this->elapsed; } }';
			eval($code);
		}
		$this->ci->benchmark = new $class();

		// Mock up URI class
		// We just need to return a URI string
		$class = 'Output_URI';
		if (!class_exists($class))
		{
			$code = 'class '.$class.' { public $uri_string = \'\'; '.
				'public function uri_string() { return $this->uri_string; } }';
			eval($code);
		}

		// Create URI object
		$this->ci->uri = new $class();

		// Mock up Profiler
		// We just need what sections were set and output to return
		$class = 'Output_Profiler';
		if (!class_exists($class))
		{
			$code = 'class '.$class.' { public $sections = array(); public $run_out = \'\'; '.
				'public function set_sections($sections) { $this->sections = $sections; } '.
				'public function run() { return $this->run_out; } }';
			eval($code);
		}
		$this->ci->profiler = new $class();

		// Mock up Loader
		// We just need a loader method to call, since profiler is already loaded
		$class = 'Output_Loader';
		if (!class_exists($class))
		{
			eval('class '.$class.' { public function library($arg1) { return TRUE; } }');
		}
		$this->ci->load = new $class();
	}
}

