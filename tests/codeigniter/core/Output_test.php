<?php

class Output_test extends CI_TestCase {

	public $output;
	protected $_output_data = '';

	public function set_up()
	{
		$this->_output_data =<<<HTML
		<html>
			<head>
				<title>Basic HTML</title>
			</head>
			<body>
				Test
			</body>
		</html>
HTML;
		$this->ci_set_config('charset', 'UTF-8');
		$output = $this->ci_core_class('output');
		$this->output = new $output();
	}

	// --------------------------------------------------------------------

	public function test_set_get_append_output()
	{
		$append = "<!-- comment /-->\n";

		$this->assertEquals(
			$this->_output_data.$append,
			$this->output
				->set_output($this->_output_data)
				->append_output("<!-- comment /-->\n")
				->get_output()
		);
	}

	// --------------------------------------------------------------------

	public function test_get_content_type()
	{
		$this->assertEquals('text/html', $this->output->get_content_type());
	}

	// --------------------------------------------------------------------

	public function test_get_header()
	{
		$this->assertNull($this->output->get_header('Non-Existent-Header'));

		// TODO: Find a way to test header() values as well. Currently,
		//	 PHPUnit prevents this by not using output buffering.

		$this->output->set_content_type('text/plain', 'WINDOWS-1251');
		$this->assertEquals(
			'text/plain; charset=WINDOWS-1251',
			$this->output->get_header('content-type')
		);
	}

	public function test_display_profiler()
	{
		$loader_cls = $this->ci_core_class('load');
		$this->ci_instance_var('load', new $loader_cls);
		$lang_cls = $this->ci_core_class('lang');
		$this->ci_instance_var('lang', new $lang_cls);
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$bm_cls = $this->ci_core_class('benchmark');
		$this->ci_instance_var('benchmark', new $bm_cls);

		$this->ci_vfs_clone('system/core/Benchmark.php');
		$this->ci_vfs_clone('system/core/Config.php');
		$this->ci_vfs_clone('system/libraries/Profiler.php');

		$this->ci_vfs_clone('system/core/Controller.php');
		require_once BASEPATH.'core/Controller.php';

		// Mock profiler class
		$profiler = $this->getMock('CI_Profiler', array('run'));
		$profiler->expects($this->once())
			->method('run')
			->will($this->returnValue('[profiler_output]...[/profiler_output]'));
		$this->ci_instance_var('profiler', $profiler);

		// Enable profiler
		$this->output->enable_profiler(TRUE);

		$expected =<<<HTML
		<html>
			<head>
				<title>Basic HTML</title>
			</head>
			<body>
				Test
			[profiler_output]...[/profiler_output]</body></html>
HTML;

		ob_start();
		$this->output->_display($this->_output_data);
		$actual = ob_get_clean();
		$this->assertEquals($expected, $actual);
	}

	public function test_display_profiler_array_output()
	{
		$loader_cls = $this->ci_core_class('load');
		$this->ci_instance_var('load', new $loader_cls);
		$lang_cls = $this->ci_core_class('lang');
		$this->ci_instance_var('lang', new $lang_cls);
		$this->ci_vfs_clone('system/language/english/profiler_lang.php');
		$bm_cls = $this->ci_core_class('benchmark');
		$this->ci_instance_var('benchmark', new $bm_cls);

		$this->ci_vfs_clone('system/core/Benchmark.php');
		$this->ci_vfs_clone('system/core/Config.php');
		$this->ci_vfs_clone('system/libraries/Profiler.php');

		$this->ci_vfs_clone('system/core/Controller.php');
		require_once BASEPATH.'core/Controller.php';

		// Mock profiler class
		$profiler = $this->getMock('CI_Profiler', array('run'));
		$profiler->expects($this->once())
				->method('run')
				->will($this->returnValue(
					array(
						'head' => '[profiler_header]...[/profiler_header]',
						'body' => '[profiler_output]...[/profiler_output]'
					)
				)
			);
		$this->ci_instance_var('profiler', $profiler);

		// Enable profiler
		$this->output->enable_profiler(TRUE);

		$expected =<<<HTML
		<html>
			<head>
				<title>Basic HTML</title>
			[profiler_header]...[/profiler_header]</head>
			<body>
				Test
			[profiler_output]...[/profiler_output]</body></html>
HTML;

		ob_start();
		$this->output->_display($this->_output_data);
		$actual = ob_get_clean();
		$this->assertEquals($expected, $actual);
	}
}
