<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OldSessionWrapper
 *
 * PHP 8 Session handler compatibility wrapper, pre-PHP8 version
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/userguide3/libraries/sessions.html
 */
class CI_SessionWrapper implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface {

	protected $driver;

	public function __construct(CI_Session_driver_interface $driver)
	{
		$this->driver = $driver;
	}

	public function open($save_path, $name)
	{
		return $this->driver->open($save_path, $name);
	}

	public function close()
	{
		return $this->driver->close();
	}

	public function read($id)
	{
		return $this->driver->read($id);
	}

	public function write($id, $data)
	{
		return $this->driver->write($id, $data);
	}

	public function destroy($id)
	{
		return $this->driver->destroy($id);
	}

	public function gc($maxlifetime)
	{
		return $this->driver->gc($maxlifetime);
	}

	public function updateTimestamp($id, $data)
	{
		return $this->driver->updateTimestamp($id, $data);
	}

	public function validateId($id)
	{
		return $this->driver->validateId($id);
	}
}
