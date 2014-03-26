<?php

class Benchmark_test extends CI_TestCase {

	public function set_up()
	{
		$this->benchmark = new CI_Benchmark();
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
		$this->benchmark->mark('code_end');

		// Override values, because time isn't testable, but make sure the markers were set
		if (isset($this->benchmark->marker['code_start']) && is_float($this->benchmark->marker['code_start']))
		{
			$this->benchmark->marker['code_start'] = 1389956144.1944;
		}

		if (isset($this->benchmark->marker['code_end']) && is_float($this->benchmark->marker['code_end']))
		{
			$this->benchmark->marker['code_end'] = 1389956145.1946;
		}

		$this->assertEquals('1', $this->benchmark->elapsed_time('code_start', 'code_end', 0));
		$this->assertEquals('1.0', $this->benchmark->elapsed_time('code_start', 'code_end', 1));
		$this->assertEquals('1.00', $this->benchmark->elapsed_time('code_start', 'code_end', 2));
		$this->assertEquals('1.000', $this->benchmark->elapsed_time('code_start', 'code_end', 3));
		$this->assertEquals('1.0002', $this->benchmark->elapsed_time('code_start', 'code_end', 4));
		$this->assertEquals('1.0002', $this->benchmark->elapsed_time('code_start', 'code_end'));

		// Test with non-existing 2nd marker, but again - we need to override the value
		$this->benchmark->elapsed_time('code_start', 'code_end2');
		if (isset($this->benchmark->marker['code_end2']) && is_float($this->benchmark->marker['code_end2']))
		{
			$this->benchmark->marker['code_end2'] = 1389956146.2046;
		}

		$this->assertEquals('2.0102', $this->benchmark->elapsed_time('code_start', 'code_end2'));
	}

	// --------------------------------------------------------------------

	public function test_memory_usage()
	{
		$this->assertEquals('{memory_usage}', $this->benchmark->memory_usage());
	}

}