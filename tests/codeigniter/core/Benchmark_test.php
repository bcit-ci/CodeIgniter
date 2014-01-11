<?php

class Benchmark_test extends CI_TestCase {

	public function set_up()
	{
		$this->benchmark = new Mock_Core_Benchmark();
	}

	// --------------------------------------------------------------------

	public function test_mark()
	{
		$this->assertEmpty($this->benchmark->marker);

		$this->benchmark->mark('code_start');

		$this->assertEquals(1, count($this->benchmark->marker));
		$this->assertArrayHasKey('code_start', $this->benchmark->marker);
	}

	// --------------------------------------------------------------------

	public function test_elapsed_time()
	{
		$this->assertEquals('{elapsed_time}', $this->benchmark->elapsed_time());
		$this->assertEmpty($this->benchmark->elapsed_time('undefined_point'));

		$this->benchmark->mark('code_start');
		sleep(1);
		$this->benchmark->mark('code_end');

		$this->assertEquals('1', $this->benchmark->elapsed_time('code_start', 'code_end', 0));
	}

	// --------------------------------------------------------------------

	public function test_memory_usage()
	{
		$this->assertEquals('{memory_usage}', $this->benchmark->memory_usage());
	}

}