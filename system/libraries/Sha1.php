<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * NOTICE OF LICENSE
 * 
 * Licensed under the Open Software License version 3.0
 * 
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * SHA1 Encoding Class
 *
 * Purpose: Provides 160 bit hashing using The Secure Hash Algorithm
 * developed at the National Institute of Standards and Technology. The 40
 * character SHA1 message hash is computationally infeasible to crack.
 *
 * This class is a fallback for servers that are not running PHP greater than
 * 4.3, or do not have the MHASH library.
 *
 * This class is based on two scripts:
 *
 * Marcus Campbell's PHP implementation (GNU license)
 * http://www.tecknik.net/sha-1/
 *
 * ...which is based on Paul Johnston's JavaScript version
 * (BSD license). http://pajhome.org.uk/
 *
 * I encapsulated the functions and wrote one additional method to fix
 * a hex conversion bug. - Rick Ellis
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_SHA1 {

	public function __construct()
	{
		log_message('debug', "SHA1 Class Initialized");
	}

	/**
	 * Generate the Hash
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function generate($str)
	{
		$n = ((strlen($str) + 8) >> 6) + 1;

		for ($i = 0; $i < $n * 16; $i++)
		{
			$x[$i] = 0;
		}

		for ($i = 0; $i < strlen($str); $i++)
		{
			$x[$i >> 2] |= ord(substr($str, $i, 1)) << (24 - ($i % 4) * 8);
		}

		$x[$i >> 2] |= 0x80 << (24 - ($i % 4) * 8);

		$x[$n * 16 - 1] = strlen($str) * 8;

		$a =  1732584193;
		$b = -271733879;
		$c = -1732584194;
		$d =  271733878;
		$e = -1009589776;

		for ($i = 0; $i < count($x); $i += 16)
		{
			$olda = $a;
			$oldb = $b;
			$oldc = $c;
			$oldd = $d;
			$olde = $e;

			for ($j = 0; $j < 80; $j++)
			{
				if ($j < 16)
				{
					$w[$j] = $x[$i + $j];
				}
				else
				{
					$w[$j] = $this->_rol($w[$j - 3] ^ $w[$j - 8] ^ $w[$j - 14] ^ $w[$j - 16], 1);
				}

				$t = $this->_safe_add($this->_safe_add($this->_rol($a, 5), $this->_ft($j, $b, $c, $d)), $this->_safe_add($this->_safe_add($e, $w[$j]), $this->_kt($j)));

				$e = $d;
				$d = $c;
				$c = $this->_rol($b, 30);
				$b = $a;
				$a = $t;
			}

			$a = $this->_safe_add($a, $olda);
			$b = $this->_safe_add($b, $oldb);
			$c = $this->_safe_add($c, $oldc);
			$d = $this->_safe_add($d, $oldd);
			$e = $this->_safe_add($e, $olde);
		}

		return $this->_hex($a).$this->_hex($b).$this->_hex($c).$this->_hex($d).$this->_hex($e);
	}

	// --------------------------------------------------------------------

	/**
	 * Convert a decimal to hex
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function _hex($str)
	{
		$str = dechex($str);

		if (strlen($str) == 7)
		{
			$str = '0'.$str;
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 *  Return result based on iteration
	 *
	 * @access	private
	 * @return	string
	 */
	function _ft($t, $b, $c, $d)
	{
		if ($t < 20)
			return ($b & $c) | ((~$b) & $d);
		if ($t < 40)
			return $b ^ $c ^ $d;
		if ($t < 60)
			return ($b & $c) | ($b & $d) | ($c & $d);

		return $b ^ $c ^ $d;
	}

	// --------------------------------------------------------------------

	/**
	 * Determine the additive constant
	 *
	 * @access	private
	 * @return	string
	 */
	function _kt($t)
	{
		if ($t < 20)
		{
			return 1518500249;
		}
		else if ($t < 40)
		{
			return 1859775393;
		}
		else if ($t < 60)
		{
			return -1894007588;
		}
		else
		{
			return -899497514;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add integers, wrapping at 2^32
	 *
	 * @access	private
	 * @return	string
	 */
	function _safe_add($x, $y)
	{
		$lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
		$msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);

		return ($msw << 16) | ($lsw & 0xFFFF);
	}

	// --------------------------------------------------------------------

	/**
	 * Bitwise rotate a 32-bit number
	 *
	 * @access	private
	 * @return	integer
	 */
	function _rol($num, $cnt)
	{
		return ($num << $cnt) | $this->_zero_fill($num, 32 - $cnt);
	}

	// --------------------------------------------------------------------

	/**
	 * Pad string with zero
	 *
	 * @access	private
	 * @return	string
	 */
	function _zero_fill($a, $b)
	{
		$bin = decbin($a);

		if (strlen($bin) < $b)
		{
			$bin = 0;
		}
		else
		{
			$bin = substr($bin, 0, strlen($bin) - $b);
		}

		for ($i=0; $i < $b; $i++)
		{
			$bin = "0".$bin;
		}

		return bindec($bin);
	}
}
// END CI_SHA

/* End of file Sha1.php */
/* Location: ./system/libraries/Sha1.php */
