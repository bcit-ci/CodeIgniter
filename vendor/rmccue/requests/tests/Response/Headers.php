<?php

class RequestsTest_Response_Headers extends PHPUnit_Framework_TestCase {
	public function testArrayAccess() {
		$headers = new Requests_Response_Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertEquals('text/plain', $headers['Content-Type']);
	}
	public function testCaseInsensitiveArrayAccess() {
		$headers = new Requests_Response_Headers();
		$headers['Content-Type'] = 'text/plain';

		$this->assertEquals('text/plain', $headers['CONTENT-TYPE']);
		$this->assertEquals('text/plain', $headers['content-type']);
	}

	/**
	 * @depends testArrayAccess
	 */
	public function testIteration() {
		$headers = new Requests_Response_Headers();
		$headers['Content-Type'] = 'text/plain';
		$headers['Content-Length'] = 10;

		foreach ($headers as $name => $value) {
			switch (strtolower($name)) {
				case 'content-type':
					$this->assertEquals('text/plain', $value);
					break;
				case 'content-length':
					$this->assertEquals(10, $value);
					break;
				default:
					throw new Exception('Invalid name: ' . $name);
			}
		}
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testInvalidKey() {
		$headers = new Requests_Response_Headers();
		$headers[] = 'text/plain';
	}

	public function testMultipleHeaders() {
		$headers = new Requests_Response_Headers();
		$headers['Accept'] = 'text/html;q=1.0';
		$headers['Accept'] = '*/*;q=0.1';

		$this->assertEquals('text/html;q=1.0,*/*;q=0.1', $headers['Accept']);
	}
}