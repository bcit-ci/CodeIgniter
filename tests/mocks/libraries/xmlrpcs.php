<?php

class Mock_Libraries_Xmlrpcs extends CI_Xmlrpcs {
	public $mock_payload = '';

	/**
	 * Act as a XML-RPC server, but save the response into $mock_public property instead of making output of it
	 */
	public function serve()
	{
		$r = $this->parseRequest();

		$payload = '<?xml version="1.0" encoding="'.$this->xmlrpc_defencoding.'"?'.'>'."\n".$this->debug_msg.$r->prepare_response();

		$this->mock_payload = "HTTP/1.1 200 OK\r\n";
		$this->mock_payload .= "Content-Type: text/xml\r\n";
		$this->mock_payload .= 'Content-Length: '.strlen($payload)."\r\n";

		$this->mock_payload .= "\r\n";

		$this->mock_payload .= $payload;
	}
}
