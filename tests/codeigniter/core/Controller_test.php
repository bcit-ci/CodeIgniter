<?php

class Controller_test extends CI_TestCase {

	public function set_up()
	{
		// Get instance
		$this->ci = $this->ci_instance();

		// Create controller
		$this->ctlr = new CI_Controller();
	}

	/**
	 * Test __get magic method
	 *
	 * Make sure $this->core_object gets objects attached to the root instance
	 *
	 * covers	CI_Controller::__get
	 */
	public function test_get()
	{
		// Do we get NULL for a property on neither CI nor Controller?
		$noprop = 'absent';
		$this->assertNull($this->ctlr->$noprop);

		// Attach something to CI
		$prop = 'attached';
		$value = 'to_parent';
		$this->ci->$prop = $value;

		// Can we get it through the controller?
		$this->assertEquals($value, $this->ctlr->$prop);

		// Set a property on both
		$prop2 = 'duplicate';
		$cval = 'on_child';
		$pval = 'on_parent';
		$this->ctlr->$prop2 = $cval;
		$this->ci->$prop2 = $pval;

		// Do we get the child version?
		$this->assertEquals($cval, $this->ctlr->$prop2);
	}

	/**
	 * Test __isset magic method
	 *
	 * covers	CI_Controller::__isset
	 */
	public function test_isset()
	{
		// Do we get FALSE for a property on neither CI nor Controller?
		$noprop = 'none';
		$this->assertFalse(isset($this->ctlr->$noprop));

		// Set something on CI
		$prop = 'answer';
		$this->ci->$prop = 42;

		// Does the controller say it's set?
		$this->assertTrue(isset($this->ctlr->$prop));
	}

	/**
	 * Test instance method
	 *
	 * covers	CI_Controller::instance
	 */
	public function test_instance()
	{
		// Does instance return the root instance?
		// Since we override get_instance for unit testing, we should get our mock root
		$this->assertEquals($this->ci, $this->ctlr->instance());

		// What if we make a static call?
		$this->assertEquals($this->ci, CI_Controller::instance());
	}

}
