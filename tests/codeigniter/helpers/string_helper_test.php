<?php

require_once(BASEPATH.'helpers/string_helper.php');

class String_helper_test extends CI_TestCase
{
	public function test_trim_slashes()
	{
		$strs = array(
			'//Slashes//\/'	=> 'Slashes//\\',
			'/var/www/html/'	=> 'var/www/html'
		);
		
		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, trim_slashes($str));
		}
	}
	
	// --------------------------------------------------------------------	

	public function test_strip_quotes()
	{
		$strs = array(
			'"me oh my!"'		=> 'me oh my!',
			"it's a winner!"	=> 'its a winner!',
		);
		
		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, strip_quotes($str));
		}
	}

	// --------------------------------------------------------------------	
	
	public function test_quotes_to_entities()
	{
		$strs = array(
			'"me oh my!"'		=> '&quot;me oh my!&quot;',
			"it's a winner!"	=> 'it&#39;s a winner!',
		);
		
		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, quotes_to_entities($str));
		}		
	}

	// --------------------------------------------------------------------	
	
	public function test_reduce_double_slashes()
	{
		$strs = array(
			'http://codeigniter.com'		=> 'http://codeigniter.com',
			'//var/www/html/example.com/'	=> '/var/www/html/example.com/',
			'/var/www/html//index.php'		=> '/var/www/html/index.php'
		);
		
		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, reduce_double_slashes($str));
		}		
	}

	// --------------------------------------------------------------------	
	
	public function test_reduce_multiples()
	{
		$strs = array(
			'Fred, Bill,, Joe, Jimmy'	=> 'Fred, Bill, Joe, Jimmy',
			'Ringo, John, Paul,,'		=> 'Ringo, John, Paul,'
		);
		
		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, reduce_multiples($str));
		}
		
		$strs = array(
			'Fred, Bill,, Joe, Jimmy'	=> 'Fred, Bill, Joe, Jimmy',
			'Ringo, John, Paul,,'		=> 'Ringo, John, Paul'
		);		

		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, reduce_multiples($str, ',', TRUE));
		}		
	}
	
	// --------------------------------------------------------------------	
	
	public function test_repeater()
	{
		$strs = array(
			'a'			=> 'aaaaaaaaaa',
			'&nbsp;'	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
			'<br>'		=> '<br><br><br><br><br><br><br><br><br><br>'
			
		);
		
		foreach ($strs as $str => $expect)
		{
			$this->assertEquals($expect, repeater($str, 10));
		}
	}	

	// --------------------------------------------------------------------	


	public function test_random_string()
	{
		$this->assertEquals(16, strlen(random_string('alnum', 16)));
		$this->assertEquals(32, strlen(random_string('unique', 16)));
		$this->assertInternalType('string', random_string('numeric', 16));
	}
	
}