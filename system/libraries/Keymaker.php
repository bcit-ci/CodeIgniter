<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
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
 * @author		Josh Burns - Github @pluckee
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Keymaker Class
 *
 * Provides generation of security keys and encryption keys
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Josh Burns - Github @pluckee
 */
class CI_KeyGen {

	/**
	 * Generation of Key
	 *
	 * @var Generation
	 */
	protected function KeyGen()
    {
    $keyset  = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$randkey = "";
	for ($i=0; $i<$amount; $i++)
		$randkey .= substr($keyset, rand(0, strlen($keyset)-1), 1);
	return $randkey
    }

	/**
	 * Key Storage
	 *
	 * @var Storage
	 */
	public function KeyData()
    {
    	$MyKeyData = array(
			$myKey 	=> ($randkey);
    		$myHash	=> sha1($myKey);
    		)
    }

    /**
     * Key Usage
     *
     * @var Publish
     */
    public function DisplayKeyHash($CI_KeyHash)
    {
    	echo "$myHash";
    }
    public function DisplayPlainKay($CI_KeyPlain)
    {
    	echo "$myKey";
    }

}

/* End of file Keymaker.php */
/* Location: ./system/libraries/Keymaker.php */