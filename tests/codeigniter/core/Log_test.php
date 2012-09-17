<?php

class Log_test extends CI_TestCase {
	/**
	 * Set up each test
	 */
	public function set_up()
	{
		// Setup VFS
		$this->ci_vfs_setup();

		// Create log object
		$this->log = new Mock_Core_Log();
	}

	/**
	 * Test configuration
	 *
	 * @covers	CI_Log::configure
	 */
	public function test_configure()
	{
		// Configure log with array
		$threshold = array(2, 4);
		$this->_configure($threshold);

		// Did all properties get set correctly?
		$this->assertEquals($this->log_path, $this->log->get_config('_log_path'));
		$this->assertEquals($this->date_fmt, $this->log->get_config('_date_fmt'));
		$this->assertEquals($this->log->get_config('_threshold_max'), $this->log->get_config('_threshold'));
		$this->assertEquals(array_flip($threshold), $this->log->get_config('_threshold_array'));
	}

	/**
	 * Write a log message
	 *
	 * @covers	CI_Log::write_log
	 */
	public function test_write_log()
	{
		// Configure log with debug threshold
		$this->_configure(2);

		// Log debug message
		$level = 'debug';
		$msg = 'Testing debug output';
		$date = date($this->date_fmt);
		$this->assertTrue($this->log->write_log($level, $msg));

		// Was the file written?
		$file = $this->log_path.'log-'.date('Y-m-d').'.php';
		$this->assertFileExists($file);

		// Collect log contents without PHP safety code
		ob_start();
		include($file);
		$log = ob_get_clean();

		// Was the message written?
		$expect = "\n".strtoupper($level).' - '.$date.' --> '.$msg."\n";
		$this->assertEquals($expect, $log);

		// Does an info message get skipped?
		$this->assertFalse($this->log->write_log('info', 'Do not write this'));
	}

	/**
	 * Configure log class
	 *
	 * @param	mixed	Log threshold
	 */
	private function _configure($threshold)
	{
		// Set params
		$dir = 'mylogs';
		$this->log_root = $this->ci_vfs_mkdir($dir, $this->ci_app_root);
		$this->log_path = $this->ci_app_path.$dir.'/';
		$this->date_fmt = 'H:i:s';

		// Configure log
		$config = array(
			'log_path' => $this->log_path,
			'log_threshold' => $threshold,
			'log_date_format' => $this->date_fmt
		);
		$this->log->configure($config);
	}
}

