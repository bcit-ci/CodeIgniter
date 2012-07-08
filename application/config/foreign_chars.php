<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Academic Free License version 3.0
 *
 * This source file is subject to the Academic Free License (AFL 3.0) that is
 * bundled with this package in the files license_afl.txt / license_afl.rst.
 * It is also available through the world wide web at this URL:
 * http://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Foreign Characters
| -------------------------------------------------------------------
| This file contains an array of foreign characters for transliteration
| conversion used by the Text helper
|
*/
$foreign_characters = array(
	'/Ã¤|Ã¦|Ç½/' => 'ae',
	'/Ã¶|Å“/' => 'oe',
	'/Ã¼/' => 'ue',
	'/Ã„/' => 'Ae',
	'/Ãœ/' => 'Ue',
	'/Ã–/' => 'Oe',
	'/Ã€|Ã�|Ã‚|Ãƒ|Ã„|Ã…|Çº|Ä€|Ä‚|Ä„|Ç�|Î‘|Î†/' => 'A',
	'/Ã |Ã¡|Ã¢|Ã£|Ã¥|Ç»|Ä�|Äƒ|Ä…|ÇŽ|Âª|Î±|Î¬/' => 'a',
	'/Ã‡|Ä†|Äˆ|ÄŠ|ÄŒ/' => 'C',
	'/Ã§|Ä‡|Ä‰|Ä‹|Ä�/' => 'c',
	'/Ã�|ÄŽ|Ä�|Î”/' => 'Dj',
	'/Ã°|Ä�|Ä‘|Î´/' => 'dj',
	'/Ãˆ|Ã‰|ÃŠ|Ã‹|Ä’|Ä”|Ä–|Ä˜|Äš|Î•|Îˆ/' => 'E',
	'/Ã¨|Ã©|Ãª|Ã«|Ä“|Ä•|Ä—|Ä™|Ä›|Î­|Îµ/' => 'e',
	'/Äœ|Äž|Ä |Ä¢|Î“/' => 'G',
	'/Ä�|ÄŸ|Ä¡|Ä£|Î³/' => 'g',
	'/Ä¤|Ä¦/' => 'H',
	'/Ä¥|Ä§/' => 'h',
	'/ÃŒ|Ã�|ÃŽ|Ã�|Ä¨|Äª|Ä¬|Ç�|Ä®|Ä°|Î—|Î‰|ÎŠ|Î™|Îª/' => 'I',
	'/Ã¬|Ã­|Ã®|Ã¯|Ä©|Ä«|Ä­|Ç�|Ä¯|Ä±|Î·|Î®|Î¯|Î¹|ÏŠ/' => 'i',
	'/Ä´/' => 'J',
	'/Äµ/' => 'j',
	'/Ä¶|Îš/' => 'K',
	'/Ä·|Îº/' => 'k',
	'/Ä¹|Ä»|Ä½|Ä¿|Å�|Î›/' => 'L',
	'/Äº|Ä¼|Ä¾|Å€|Å‚|Î»/' => 'l',
	'/Ã‘|Åƒ|Å…|Å‡|Î�/' => 'N',
	'/Ã±|Å„|Å†|Åˆ|Å‰|Î½/' => 'n',
	'/Ã’|Ã“|Ã”|Ã•|ÅŒ|ÅŽ|Ç‘|Å�|Æ |Ã˜|Ç¾|ÎŸ|ÎŒ|Î©|Î�/' => 'O',
	'/Ã²|Ã³|Ã´|Ãµ|Å�|Å�|Ç’|Å‘|Æ¡|Ã¸|Ç¿|Âº|Î¿|ÏŒ|Ï‰|ÏŽ/' => 'o',
	'/Å”|Å–|Å˜|Î¡/' => 'R',
	'/Å•|Å—|Å™|Ï�/' => 'r',
	'/Åš|Åœ|Åž|È˜|Å |Î£/' => 'S',
	'/Å›|Å�|ÅŸ|È™|Å¡|Å¿|Ïƒ|Ï‚/' => 's',
	'/Èš|Å¢|Å¤|Å¦|Ï„/' => 'T',
	'/È›|Å£|Å¥|Å§/' => 't',
	'/Ã™|Ãš|Ã›|Å¨|Åª|Å¬|Å®|Å°|Å²|Æ¯|Ç“|Ç•|Ç—|Ç™|Ç›/' => 'U',
	'/Ã¹|Ãº|Ã»|Å©|Å«|Å­|Å¯|Å±|Å³|Æ°|Ç”|Ç–|Ç˜|Çš|Çœ|Ï…|Ï�|Ï‹/' => 'u',
	'/Ã�|Å¸|Å¶|Î¥|ÎŽ|Î«/' => 'Y',
	'/Ã½|Ã¿|Å·/' => 'y',
	'/Å´/' => 'W',
	'/Åµ/' => 'w',
	'/Å¹|Å»|Å½|Î–/' => 'Z',
	'/Åº|Å¼|Å¾|Î¶/' => 'z',
	'/Ã†|Ç¼/' => 'AE',
	'/ÃŸ/'=> 'ss',
	'/Ä²/' => 'IJ',
	'/Ä³/' => 'ij',
	'/Å’/' => 'OE',
	'/Æ’/' => 'f',
	'/Î¾/' => 'ks',
	'/Ï€/' => 'p',
	'/Î²/' => 'v',
	'/Î¼/' => 'm',
	'/Ïˆ/' => 'ps',
);

/* End of file foreign_chars.php */
/* Location: ./application/config/foreign_chars.php */