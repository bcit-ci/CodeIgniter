<?php
class Log_test extends CI_TestCase {

	public function test_configuration()
	{
		$path       = new ReflectionProperty('CI_Log', '_log_path');
		$path->setAccessible(TRUE);
		$threshold  = new ReflectionProperty('CI_Log', '_threshold');
		$threshold->setAccessible(TRUE);
		$date_fmt   = new ReflectionProperty('CI_Log', '_date_fmt');
		$date_fmt->setAccessible(TRUE);
		$filename   = new ReflectionProperty('CI_Log', '_log_filename');
		$filename->setAccessible(TRUE);
		$file_perms = new ReflectionProperty('CI_Log', '_file_permissions');
		$file_perms->setAccessible(TRUE);
		$enabled    = new ReflectionProperty('CI_Log', '_enabled');
		$enabled->setAccessible(TRUE);

		$this->ci_set_config('log_path', $this->ci_readonly_dir->url());
		$this->ci_set_config('log_threshold', 'z');
		$this->ci_set_config('log_date_format', 'd.m.Y');
		$this->ci_set_config('log_filename', '');
		$this->ci_set_config('log_file_permissions', '');
		$instance = new CI_Log();

		$this->assertNotFalse(strpos($path->getValue($instance), 'application/readonly'));
		$this->assertEquals($threshold->getValue($instance), 1);
		$this->assertEquals($date_fmt->getValue($instance), 'd.m.Y');
		$this->assertEquals($filename->getValue($instance), 'log-'.date('Y-m-d').'.php');
		$this->assertEquals($file_perms->getValue($instance), 0644);
		$this->assertFalse($enabled->getValue($instance));

		$this->ci_set_config('log_path', '');
		$this->ci_set_config('log_threshold', '0');
		$this->ci_set_config('log_date_format', '');
		$this->ci_set_config('log_filename', 'testname.log');
		$this->ci_set_config('log_file_permissions', 0600);
		$instance = new CI_Log();

		$this->assertEquals($path->getValue($instance), $this->ci_vfs_root->url().'application/logs'.DIRECTORY_SEPARATOR);
		$this->assertEquals($threshold->getValue($instance), 0);
		$this->assertEquals($date_fmt->getValue($instance), 'Y-m-d H:i:s');
		$this->assertEquals($filename->getValue($instance), 'testname.log');
		$this->assertEquals($file_perms->getValue($instance), 0600);
		$this->assertEquals($enabled->getValue($instance), TRUE);
	}

	// --------------------------------------------------------------------

	public function test_format_line()
	{
		$this->ci_set_config('log_path', '');
		$this->ci_set_config('log_threshold', 0);
		$instance = new CI_Log();

		$format_line = new ReflectionMethod($instance, '_format_line');
		$format_line->setAccessible(TRUE);
		$this->assertEquals(
			$format_line->invoke($instance, 'LEVEL', 'Timestamp', 'Message'),
			"LEVEL - Timestamp --> Message".PHP_EOL
		);
	}
}
