<?php

class Mock_Libraries_Xmlrpc extends CI_Xmlrpc {
	public function server($url, $port = 80, $proxy = FALSE, $proxy_port = 8080)
	{
		$this->client = new Mock_Libraries_XML_RPC_Client('/', $url, $port, $proxy, $proxy_port);
	}
}

class Mock_Libraries_XML_RPC_Client extends XML_RPC_Client {
	public $mock_response = '';

	/**
	 * @param XML_RPC_Message $msg
	 */
	public function sendPayload($msg)
	{
		if (empty($msg->payload))
		{
			$msg->createPayload();
		}

		$fp = fopen('php://memory', 'rw+');
		fwrite($fp, $this->mock_response);
		fseek($fp, 0);

		$parsed = $msg->parseResponse($fp);
		fclose($fp);

		return $parsed;
	}
}

