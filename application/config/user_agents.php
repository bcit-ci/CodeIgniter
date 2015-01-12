<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| USER AGENT TYPES
| -------------------------------------------------------------------
| This file contains four arrays of user agent data. It is used by the
| User Agent Class to help identify browser, platform, robot, and
| mobile device data. The array keys are used to identify the device
| and the array values are used to set the actual name of the item.
*/

$platforms = array(
	'windows nt 6.3'	=> 'Windows 8.1',
	'windows nt 6.2'	=> 'Windows 8',
	'windows nt 6.1'	=> 'Windows 7',
	'windows nt 6.0'	=> 'Windows Vista',
	'windows nt 5.2'	=> 'Windows 2003',
	'windows nt 5.1'	=> 'Windows XP',
	'windows nt 5.0'	=> 'Windows 2000',
	'windows nt 4.0'	=> 'Windows NT 4.0',
	'winnt4.0'			=> 'Windows NT 4.0',
	'winnt 4.0'			=> 'Windows NT',
	'winnt'				=> 'Windows NT',
	'windows 98'		=> 'Windows 98',
	'win98'				=> 'Windows 98',
	'windows 95'		=> 'Windows 95',
	'win95'				=> 'Windows 95',
	'windows phone'			=> 'Windows Phone',
	'windows'			=> 'Unknown Windows OS',
	'android'			=> 'Android',
	'blackberry'		=> 'BlackBerry',
	'iphone'			=> 'iOS',
	'ipad'				=> 'iOS',
	'ipod'				=> 'iOS',
	'os x'				=> 'Mac OS X',
	'ppc mac'			=> 'Power PC Mac',
	'freebsd'			=> 'FreeBSD',
	'ppc'				=> 'Macintosh',
	'linux'				=> 'Linux',
	'debian'			=> 'Debian',
	'sunos'				=> 'Sun Solaris',
	'beos'				=> 'BeOS',
	'apachebench'		=> 'ApacheBench',
	'aix'				=> 'AIX',
	'irix'				=> 'Irix',
	'osf'				=> 'DEC OSF',
	'hp-ux'				=> 'HP-UX',
	'netbsd'			=> 'NetBSD',
	'bsdi'				=> 'BSDi',
	'openbsd'			=> 'OpenBSD',
	'gnu'				=> 'GNU/Linux',
	'unix'				=> 'Unknown Unix OS',
	'symbian' 			=> 'Symbian OS'
);


// The order of this array should NOT be changed. Many browsers return
// multiple browser types so we want to identify the sub-type first.
$browsers = array(
	'OPR'			=> 'Opera',
	'Flock'			=> 'Flock',
	'Chrome'		=> 'Chrome',
	// Opera 10+ always reports Opera/9.80 and appends Version/<real version> to the user agent string
	'Opera.*?Version'	=> 'Opera',
	'Opera'			=> 'Opera',
	'MSIE'			=> 'Internet Explorer',
	'Internet Explorer'	=> 'Internet Explorer',
	'Trident.* rv'	=> 'Internet Explorer',
	'Shiira'		=> 'Shiira',
	'Firefox'		=> 'Firefox',
	'Chimera'		=> 'Chimera',
	'Phoenix'		=> 'Phoenix',
	'Firebird'		=> 'Firebird',
	'Camino'		=> 'Camino',
	'Netscape'		=> 'Netscape',
	'OmniWeb'		=> 'OmniWeb',
	'Safari'		=> 'Safari',
	'Mozilla'		=> 'Mozilla',
	'Konqueror'		=> 'Konqueror',
	'icab'			=> 'iCab',
	'Lynx'			=> 'Lynx',
	'Links'			=> 'Links',
	'hotjava'		=> 'HotJava',
	'amaya'			=> 'Amaya',
	'IBrowse'		=> 'IBrowse',
	'Maxthon'		=> 'Maxthon',
	'Ubuntu'		=> 'Ubuntu Web Browser'
);

$mobiles = array(
	// legacy array, old values commented out
	'mobileexplorer'	=> 'Mobile Explorer',
//  'openwave'			=> 'Open Wave',
//	'opera mini'		=> 'Opera Mini',
//	'operamini'			=> 'Opera Mini',
//	'elaine'			=> 'Palm',
	'palmsource'		=> 'Palm',
//	'digital paths'		=> 'Palm',
//	'avantgo'			=> 'Avantgo',
//	'xiino'				=> 'Xiino',
	'palmscape'			=> 'Palmscape',
//	'nokia'				=> 'Nokia',
//	'ericsson'			=> 'Ericsson',
//	'blackberry'		=> 'BlackBerry',
//	'motorola'			=> 'Motorola'

	// Phones and Manufacturers
	'motorola'		=> 'Motorola',
	'nokia'			=> 'Nokia',
	'palm'			=> 'Palm',
	'iphone'		=> 'Apple iPhone',
	'ipad'			=> 'iPad',
	'ipod'			=> 'Apple iPod Touch',
	'sony'			=> 'Sony Ericsson',
	'ericsson'		=> 'Sony Ericsson',
	'blackberry'	=> 'BlackBerry',
	'cocoon'		=> 'O2 Cocoon',
	'blazer'		=> 'Treo',
	'lg'			=> 'LG',
	'amoi'			=> 'Amoi',
	'xda'			=> 'XDA',
	'mda'			=> 'MDA',
	'vario'			=> 'Vario',
	'htc'			=> 'HTC',
	'samsung'		=> 'Samsung',
	'sharp'			=> 'Sharp',
	'sie-'			=> 'Siemens',
	'alcatel'		=> 'Alcatel',
	'benq'			=> 'BenQ',
	'ipaq'			=> 'HP iPaq',
	'mot-'			=> 'Motorola',
	'playstation portable'	=> 'PlayStation Portable',
	'playstation 3'		=> 'PlayStation 3',
	'playstation vita'  	=> 'PlayStation Vita',
	'hiptop'		=> 'Danger Hiptop',
	'nec-'			=> 'NEC',
	'panasonic'		=> 'Panasonic',
	'philips'		=> 'Philips',
	'sagem'			=> 'Sagem',
	'sanyo'			=> 'Sanyo',
	'spv'			=> 'SPV',
	'zte'			=> 'ZTE',
	'sendo'			=> 'Sendo',
	'nintendo dsi'	=> 'Nintendo DSi',
	'nintendo ds'	=> 'Nintendo DS',
	'nintendo 3ds'	=> 'Nintendo 3DS',
	'wii'			=> 'Nintendo Wii',
	'open web'		=> 'Open Web',
	'openweb'		=> 'OpenWeb',

	// Operating Systems
	'android'		=> 'Android',
	'symbian'		=> 'Symbian',
	'SymbianOS'		=> 'SymbianOS',
	'elaine'		=> 'Palm',
	'series60'		=> 'Symbian S60',
	'windows ce'	=> 'Windows CE',

	// Browsers
	'obigo'			=> 'Obigo',
	'netfront'		=> 'Netfront Browser',
	'openwave'		=> 'Openwave Browser',
	'mobilexplorer'	=> 'Mobile Explorer',
	'operamini'		=> 'Opera Mini',
	'opera mini'	=> 'Opera Mini',
	'opera mobi'	=> 'Opera Mobile',
	'fennec'		=> 'Firefox Mobile',

	// Other
	'digital paths'	=> 'Digital Paths',
	'avantgo'		=> 'AvantGo',
	'xiino'			=> 'Xiino',
	'novarra'		=> 'Novarra Transcoder',
	'vodafone'		=> 'Vodafone',
	'docomo'		=> 'NTT DoCoMo',
	'o2'			=> 'O2',

	// Fallback
	'mobile'		=> 'Generic Mobile',
	'wireless'		=> 'Generic Mobile',
	'j2me'			=> 'Generic Mobile',
	'midp'			=> 'Generic Mobile',
	'cldc'			=> 'Generic Mobile',
	'up.link'		=> 'Generic Mobile',
	'up.browser'	=> 'Generic Mobile',
	'smartphone'	=> 'Generic Mobile',
	'cellphone'		=> 'Generic Mobile'
);

// There are hundreds of bots but these are the most common.
$robots = array(
	'googlebot'		=> 'Googlebot',
	'msnbot'		=> 'MSNBot',
	'baiduspider'		=> 'Baiduspider',
	'bingbot'		=> 'Bing',
	'slurp'			=> 'Inktomi Slurp',
	'yahoo'			=> 'Yahoo',
	'askjeeves'		=> 'AskJeeves',
	'fastcrawler'		=> 'FastCrawler',
	'infoseek'		=> 'InfoSeek Robot 1.0',
	'lycos'			=> 'Lycos',
	'yandex'		=> 'YandexBot',
	'mediapartners-google'	=> 'MediaPartners Google',
	'CRAZYWEBCRAWLER'	=> 'Crazy Webcrawler',
	'adsbot-google'		=> 'AdsBot Google',
	'feedfetcher-google'	=> 'Feedfetcher Google',
	'curious george'	=> 'Curious George'
);

/* End of file user_agents.php */
/* Location: ./application/config/user_agents.php */