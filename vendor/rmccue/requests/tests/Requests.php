<?php

class RequestsTest_Requests extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Requests_Exception
	 */
	public function testInvalidProtocol() {
		$request = Requests::request('ftp://128.0.0.1/');
	}

	public function testDefaultTransport() {
		$request = Requests::get('http://httpbin.org/get');
		$this->assertEquals(200, $request->status_code);
	}

	/**
	 * Standard response header parsing
	 */
	public function testHeaderParsing() {
		$transport = new RawTransport();
		$transport->data =
			"HTTP/1.0 200 OK\r\n".
			"Host: localhost\r\n".
			"Host: ambiguous\r\n".
			"Nospace:here\r\n".
			"Muchspace:  there   \r\n".
			"Empty:\r\n".
			"Empty2: \r\n".
			"Folded: one\r\n".
			"\ttwo\r\n".
			"  three\r\n\r\n".
			"stop\r\n";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$expected = new Requests_Response_Headers();
		$expected['host'] = 'localhost,ambiguous';
		$expected['nospace'] = 'here';
		$expected['muchspace'] = 'there';
		$expected['empty'] = '';
		$expected['empty2'] = '';
		$expected['folded'] = 'one two  three';
		foreach ($expected as $key => $value) {
			$this->assertEquals($value, $response->headers[$key]);
		}

		foreach ($response->headers as $key => $value) {
			$this->assertEquals($value, $expected[$key]);
		}
	}

	public function testRawAccess() {
		$transport = new RawTransport();
		$transport->data =
			"HTTP/1.0 200 OK\r\n".
			"Host: localhost\r\n\r\n".
			"Test";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertEquals($transport->data, $response->raw);
	}

	/**
	 * Headers with only \n delimiting should be treated as if they're \r\n
	 */
	public function testHeaderOnlyLF() {
		$transport = new RawTransport();
		$transport->data = "HTTP/1.0 200 OK\r\nTest: value\nAnother-Test: value\r\n\r\n";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertEquals('value', $response->headers['test']);
		$this->assertEquals('value', $response->headers['another-test']);
	}

	/**
	 * Check that invalid protocols are not accepted
	 *
	 * We do not support HTTP/0.9. If this is really an issue for you, file a
	 * new issue, and update your server/proxy to support a proper protocol.
	 *
	 * @expectedException Requests_Exception
	 */
	public function testInvalidProtocolVersion() {
		$transport = new RawTransport();
		$transport->data = "HTTP/0.9 200 OK\r\n\r\n<p>Test";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
	}

	/**
	 * HTTP/0.9 also appears to use a single CRLF instead of two
	 *
	 * @expectedException Requests_Exception
	 */
	public function testSingleCRLFSeparator() {
		$transport = new RawTransport();
		$transport->data = "HTTP/0.9 200 OK\r\n<p>Test";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
	}

	/**
	 * @expectedException Requests_Exception
	 */
	public function testInvalidStatus() {
		$transport = new RawTransport();
		$transport->data = "HTTP/1.1 OK\r\nTest: value\nAnother-Test: value\r\n\r\nTest";

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
	}

	public function test30xWithoutLocation() {
		$transport = new MockTransport();
		$transport->code = 302;

		$options = array(
			'transport' => $transport
		);
		$response = Requests::get('http://example.com/', array(), $options);
		$this->assertEquals(302, $response->status_code);
		$this->assertEquals(0, $response->redirects);
	}
}