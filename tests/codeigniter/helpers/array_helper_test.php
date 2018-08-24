<?php

class Array_helper_test extends CI_TestCase {

	public $my_array = array(
		'foo'    => 'bar',
		'sally'  => 'jim',
		'maggie' => 'bessie',
		'herb'   => 'cook'
	);

	public function set_up()
	{
		$this->helper('array');
	}

	// ------------------------------------------------------------------------

	public function test_element_with_existing_item()
	{
		$this->assertEquals(FALSE, element('testing', $this->my_array));
		$this->assertEquals('not set', element('testing', $this->my_array, 'not set'));
		$this->assertEquals('bar', element('foo', $this->my_array));
	}

	// ------------------------------------------------------------------------

	public function test_random_element()
	{
		// Send a string, not an array to random_element
		$this->assertEquals('my string', random_element('my string'));

		// Test sending an array
		$this->assertContains(random_element($this->my_array), $this->my_array);
	}

	// ------------------------------------------------------------------------

	public function test_elements()
	{
		$this->assertInternalType('array', elements('test', $this->my_array));
		$this->assertInternalType('array', elements('foo', $this->my_array));
	}

}
