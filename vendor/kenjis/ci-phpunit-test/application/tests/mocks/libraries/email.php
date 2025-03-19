<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Mock_Libraries_Email
{
	private $data = [];

	/**
	 * @var bool return value of send()
	 */
	public $return_send = TRUE;

	public function initialize()
	{
		
	}

	public function from($from)
	{
		$this->data['from'] = $from;
	}

	public function to($to)
	{
		$this->data['to'] = $to;
	}

	public function bcc($bcc)
	{
		$this->data['bcc'] = $bcc;
	}

	public function subject($subject)
	{
		$this->data['subject'] = $subject;
	}

	public function message($message)
	{
		$this->data['message'] = $message;
	}

	public function send()
	{
		return $this->return_send;
	}

	public function _get_data()
	{
		return $this->data;
	}
}
