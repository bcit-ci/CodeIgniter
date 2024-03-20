<?php

class Xmlrpc_test extends CI_TestCase {

	protected $input;
	protected $input_lib_raw_stream;
	protected $method_param = '';

	public function set_up()
	{
		$security = new Mock_Core_Security('UTF-8');
		$this->input = new CI_Input($security);

		$this->input_lib_raw_stream = new ReflectionProperty($this->input, '_raw_input_stream');
		$this->input_lib_raw_stream->setAccessible(TRUE);

		$this->ci_instance_var('input', $this->input);
		$this->ci_instance_var('security', $security);
	}

	// --------------------------------------------------------------------

	public function test_xmlrpc_client()
	{
		$xmlrpc = new Mock_Libraries_Xmlrpc();
		$xmlrpc->server('http://rpc.test/');
		$xmlrpc->method('testcontroller.test');

		$request = array('My Blog', 'http://www.myrpc.com/test/');
		$message = 'test'.time();
		$xml_response = $this->xml_response($message);
		$xmlrpc->client->mock_response = "HTTP/1.1 200 OK\r\nContent-Type: text/xml\r\nContent-Length: ".strlen($xml_response)."\r\n\r\n$xml_response";

		// Perform in the same request multiple calls
		for ($attempt = 1; $attempt <= 2; $attempt++)
		{
			$xmlrpc->request($request);

			$this->assertTrue($xmlrpc->send_request());

			$response = $xmlrpc->display_response();

			$this->assertEquals('theuser', $response['name']);
			$this->assertEquals(123435, $response['member_id']);
			$this->assertEquals($message, $response['request']);
		}
	}

	// --------------------------------------------------------------------

	public function test_xmlrpc_server()
	{
		$xmlrpcs = new Mock_Libraries_Xmlrpcs();

		$config['functions']['Testmethod'] = array('function' => __CLASS__.'.mock_method_new_entry');
		$config['object'] = $this;

		$xmlrpcs->initialize($config);

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$this->input_lib_raw_stream->setValue($this->input, $this->xml_request());

		$xmlrpcs->serve();

		$this->assertEquals('Test', $this->method_param);
	}

	// --------------------------------------------------------------------

	/**
	 * @param XML_RPC_Message $param
	 */
	public function mock_method_new_entry($param)
	{
		$this->method_param = $param->params[0]->scalarval();

		return new XML_RPC_Response(new XML_RPC_Values(true, 'boolean'));
	}

	// --------------------------------------------------------------------

	private function xml_response($message)
	{
		return '<?xml version="1.0" encoding="UTF-8"?>
<methodResponse>
<params>
<param>
<value>
<struct>
<member>
<name>name</name>
<value>
<string>theuser</string>
</value>
</member>
<member>
<name>member_id</name>
<value>
<int>123435</int>
</value>
</member>
<member>
<name>request</name>
<value>
<string>'.$message.'</string>
</value>
</member>
</struct></value>
</param>
</params>
</methodResponse>';
	}

	// --------------------------------------------------------------------

	public function xml_request()
	{
		return '<?xml version="1.0"?>
<methodCall>
<methodName>Testmethod</methodName>
<params>
<param>
<value>
<string>Test</string>
</value>
</param>
</params>
</methodCall>';
	}
}
