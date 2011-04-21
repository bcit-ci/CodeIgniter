<?php

// OLD TEST FORMAT: DO NOT COPY

require_once(BASEPATH.'helpers/array_helper.php');

class Array_helper_test extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->my_array = array(
			'foo'		=> 'bar',
			'sally'		=> 'jim',
			'maggie'	=> 'bessie',
			'herb'		=> 'cook'
		);
	}
	
	// ------------------------------------------------------------------------
	
	public function testElementWithExistingItem()
	{	
		$this->assertEquals(FALSE, element('testing', $this->my_array));
		
		$this->assertEquals('not set', element('testing', $this->my_array, 'not set'));
		
		$this->assertEquals('bar', element('foo', $this->my_array));
	}
	
	// ------------------------------------------------------------------------	

	public function testRandomElement()
	{
		// Send a string, not an array to random_element
		$this->assertEquals('my string', random_element('my string'));
		
		// Test sending an array
		$this->assertEquals(TRUE, in_array(random_element($this->my_array), $this->my_array));
	}

	// ------------------------------------------------------------------------	
	
	public function testElements()
	{
		$this->assertEquals(TRUE, is_array(elements('test', $this->my_array)));
		$this->assertEquals(TRUE, is_array(elements('foo', $this->my_array)));
	}

}