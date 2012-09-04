<?php
/**
 * Mock library testing extending Session
 * driver/library with prefixed subclass
 */
class Prefix_Session extends CI_Session {
	/**
	 * Simulate new page load
	 */
	public function reload()
	{
		$this->_flashdata_sweep();
		$this->_flashdata_mark();
		$this->_tempdata_sweep();
	}
}

