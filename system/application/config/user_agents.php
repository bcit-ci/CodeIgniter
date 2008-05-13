<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| USER AGENT TYPES
| -------------------------------------------------------------------
| This file contains four arrays of user agent data.  It is used by the
| User Agent Class to help identify browser, platform, robot, and
| mobile device data.  The array keys are used to identify the device
| and the array values are used to set the actual name of the item.
|
*/

$platforms = array (
					'windows nt 6.0'	=> 'Windows Longhorn',
					'windows nt 5.2'	=> 'Windows 2003',
					'windows nt 5.0'	=> 'Windows 2000',
					'windows nt 5.1'	=> 'Windows XP',
					'windows nt 4.0'	=> 'Windows NT 4.0',
					'winnt4.0'			=> 'Windows NT 4.0',
					'winnt 4.0'			=> 'Windows NT',
					'winnt'				=> 'Windows NT',
					'windows 98'		=> 'Windows 98',
					'win98'				=> 'Windows 98',
					'windows 95'		=> 'Windows 95',
					'win95'				=> 'Windows 95',
					'windows'			=> 'Unknown Windows OS',
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
					'unix'				=> 'Unknown Unix OS'
				);


// The order of this array should NOT be changed. Many browsers return
// multiple browser types so we want to identify the sub-type first.
$browsers = array(
					'Opera'				=> 'Opera',
					'MSIE'				=> 'Internet Explorer',
					'Internet Explorer'	=> 'Internet Explorer',
					'Shiira'			=> 'Shiira',
					'Firefox'			=> 'Firefox',
					'Chimera'			=> 'Chimera',
					'Phoenix'			=> 'Phoenix',
					'Firebird'			=> 'Firebird',
					'Camino'			=> 'Camino',
					'Netscape'			=> 'Netscape',
					'OmniWeb'			=> 'OmniWeb',
					'Mozilla'			=> 'Mozilla',
					'Safari'			=> 'Safari',
					'Konqueror'			=> 'Konqueror',
					'icab'				=> 'iCab',
					'Lynx'				=> 'Lynx',
					'Links'				=> 'Links',
					'hotjava'			=> 'HotJava',
					'amaya'				=> 'Amaya',
					'IBrowse'			=> 'IBrowse'
				);

$mobiles = array(
					'mobileexplorer'	=> 'Mobile Explorer',
					'openwave'			=> 'Open Wave',
					'opera mini'		=> 'Opera Mini',
					'operamini'			=> 'Opera Mini',
					'elaine'			=> 'Palm',
					'palmsource'		=> 'Palm',
					'digital paths'		=> 'Palm',
					'avantgo'			=> 'Avantgo',
					'xiino'				=> 'Xiino',
					'palmscape'			=> 'Palmscape',
					'nokia'				=> 'Nokia',
					'ericsson'			=> 'Ericsson',
					'blackberry'		=> 'BlackBerry',
					'motorola'			=> 'Motorola'
				);	

// There are hundreds of bots but these are the most common.
$robots = array(
					'googlebot'			=> 'Googlebot',
					'msnbot'			=> 'MSNBot',
					'slurp'				=> 'Inktomi Slurp',
					'yahoo'				=> 'Yahoo',
					'askjeeves'			=> 'AskJeeves',
					'fastcrawler'		=> 'FastCrawler',
					'infoseek'			=> 'InfoSeek Robot 1.0',
					'lycos'				=> 'Lycos'
				);	



/* End of file user_agents.php */
/* Location: ./system/application/config/user_agents.php */