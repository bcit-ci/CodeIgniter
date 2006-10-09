<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html 
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * User Agent Class
 * 
 * Identifies the platform, browser, robot, or mobile devise of the browsing agent
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	User Agent
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/uri.html
 */
class CI_User_agent {

	var $agent		= NULL;
	
	var $is_browser	= FALSE;
	var $is_robot	= FALSE;
	var $is_mobile	= FALSE;

	var $languages	= array();
	var $charsets	= array();
	
	var $platform	= '';
	var $browser	= '';
	var $version	= '';
	var $moble		= '';
	var $robot		= '';

	var $platforms = array (
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

	var $browsers = array(
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

	var $mobiles = array(
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
							'blackBerry'		=> 'BlackBerry',
							'motorola'			=> 'Motorola'
						);	

	var $robots = array(
			
							'googlebot'					=> 'Googlebot',
							'msnbot'					=> 'MSNBot',
							'slurp'						=> 'Inktomi Slurp',
							'yahoo'						=> 'Yahoo',
							'askjeeves'					=> 'AskJeeves',
							'fastcrawler'				=> 'FastCrawler',
							'infoseek'					=> 'InfoSeek Robot 1.0',
							'lycos'						=> 'Lycos',
							'abcdatos'					=> 'ABCdatos BotLink',
							'Acme.Spider'				=> 'Acme.Spider',
							'ahoythehomepagefinder'		=> 'Ahoy! The Homepage Finder',
							'Alkaline'					=> 'Alkaline',
							'anthill'					=> 'Anthill',
							'appie'						=> 'Walhello appie',
							'arachnophilia'				=> 'Arachnophilia',
							'arale'						=> 'Arale',
							'araneo'					=> 'Araneo',
							'araybot'					=> 'AraybOt',
							'architext'					=> 'ArchitextSpider',
							'aretha'					=> 'Aretha',
							'ariadne'					=> 'ARIADNE',
							'arks'						=> 'arks',
							'aspider'					=> 'ASpider (Associative Spider)',
							'atn.txt'					=> 'ATN Worldwide',
							'atomz'						=> 'Atomz.com Search Robot',
							'auresys'					=> 'AURESYS',
							'backrub'					=> 'BackRub',
							'bbot'						=> 'BBot',
							'bigbrother'				=> 'Big Brother',
							'bjaaland'					=> 'Bjaaland',
							'blackwidow'				=> 'BlackWidow',
							'blindekuh'					=> 'Die Blinde Kuh',
							'Bloodhound'				=> 'Bloodhound',
							'borg-bot'					=> 'Borg-Bot',
							'boxseabot'					=> 'BoxSeaBot',
							'brightnet'					=> 'bright.net caching robot',
							'bspider'					=> 'BSpider',
							'cactvschemistryspider'		=> 'CACTVS Chemistry Spider',
							'calif'						=> 'Calif',
							'cassandra'					=> 'Cassandra',
							'cgireader'					=> 'Digimarc Marcspider/CGI',
							'checkbot'					=> 'Checkbot',
							'christcrawler'				=> 'ChristCrawler.com',
							'churl'						=> 'churl',
							'cienciaficcion'			=> 'cIeNcIaFiCcIoN.nEt',
							'cmc'						=> 'CMC/0.01',
							'Collective'				=> 'Collective',
							'combine'					=> 'Combine System',
							'confuzzledbot'				=> 'ConfuzzledBot',
							'coolbot'					=> 'CoolBot',
							'core'						=> 'Web Core / Roots',
							'cosmos'					=> 'XYLEME Robot',
							'cruiser'					=> 'Internet Cruiser Robot',
							'cusco'						=> 'Cusco',
							'cyberspyder'				=> 'CyberSpyder Link Test',
							'cydralspider'				=> 'CydralSpider',
							'desertrealm'				=> 'Desert Realm Spider',
							'deweb'						=> 'DeWeb(c) Katalog/Index',
							'dienstspider'				=> 'DienstSpider',
							'digger'					=> 'Digger',
							'diibot'					=> 'Digital Integrity Robot',
							'directhit'					=> 'Direct Hit Grabber',
							'dnabot'					=> 'DNAbot',
							'download_express'			=> 'DownLoad Express',
							'dragonbot'					=> 'DragonBot',
							'e-collector'				=> 'e-collector',
							'ebiness'					=> 'EbiNess',
							'eit'						=> 'EIT Link Verifier Robot',
							'elfinbot'					=> 'ELFINBOT',
							'emacs'						=> 'Emacs-w3 Search Engine',
							'emcspider'					=> 'ananzi',
							'esculapio'					=> 'esculapio',
							'esther'					=> 'Esther',
							'evliyacelebi'				=> 'Evliya Celebi',
							'nzexplorer'				=> 'nzexplorer',
							'fdse'						=> 'Fluid Dynamics Search Engine robot',
							'felix'						=> 'Felix IDE',
							'ferret'					=> 'Wild Ferret Web Hopper #1, #2, #3',
							'fetchrover'				=> 'FetchRover',
							'fido'						=> 'fido',
							'finnish'					=> 'Hämähäkki',
							'fireball'					=> 'KIT-Fireball',
							'fish'						=> 'Fish search',
							'fouineur'					=> 'Fouineur',
							'francoroute'				=> 'Robot Francoroute',
							'freecrawl'					=> 'Freecrawl',
							'funnelweb'					=> 'FunnelWeb',
							'gama'						=> 'gammaSpider, FocusedCrawler',
							'gazz'						=> 'gazz',
							'gcreep'					=> 'GCreep',
							'getbot'					=> 'GetBot',
							'geturl'					=> 'GetURL',
							'golem'						=> 'Golem',
							'grapnel'					=> 'Grapnel/0.01 Experiment',
							'griffon'					=> 'Griffon                                                               ',
							'gromit'					=> 'Gromit',
							'gulliver'					=> 'Northern Light Gulliver',
							'gulperbot'					=> 'Gulper Bot',
							'hambot'					=> 'HamBot',
							'harvest'					=> 'Harvest',
							'havindex'					=> 'havIndex',
							'hi'						=> 'HI (HTML Index) Search',
							'hometown'					=> 'Hometown Spider Pro',
							'wired-digital'				=> 'Wired Digital',
							'htdig'						=> 'ht://Dig',
							'htmlgobble'				=> 'HTMLgobble',
							'hyperdecontextualizer'		=> 'Hyper-Decontextualizer',
							'iajabot'					=> 'iajaBot',
							'ibm'						=> 'IBM_Planetwide',
							'iconoclast'				=> 'Popular Iconoclast',
							'Ilse'						=> 'Ingrid',
							'imagelock'					=> 'Imagelock ',
							'incywincy'					=> 'IncyWincy',
							'informant'					=> 'Informant',
							'infoseeksidewinder'		=> 'Infoseek Sidewinder',
							'infospider'				=> 'InfoSpiders',
							'inspectorwww'				=> 'Inspector Web',
							'intelliagent'				=> 'IntelliAgent',
							'irobot'					=> 'I, Robot',
							'iron33'					=> 'Iron33',
							'israelisearch'				=> 'Israeli-search',
							'javabee'					=> 'JavaBee',
							'JBot'						=> 'JBot Java Web Robot',
							'jcrawler'					=> 'JCrawler',
							'jobo'						=> 'JoBo Java Web Robot',
							'jobot'						=> 'Jobot',
							'joebot'					=> 'JoeBot',
							'jubii'						=> 'The Jubii Indexing Robot',
							'jumpstation'				=> 'JumpStation',
							'kapsi'						=> 'image.kapsi.net',
							'katipo'					=> 'Katipo',
							'kdd'						=> 'KDD-Explorer',
							'kilroy'					=> 'Kilroy',
							'ko_yappo_robot'			=> 'KO_Yappo_Robot',
							'labelgrabber.txt'			=> 'LabelGrabber',
							'larbin'					=> 'larbin',
							'legs'						=> 'legs',
							'linkidator'				=> 'Link Validator',
							'linkscan'					=> 'LinkScan',
							'linkwalker'				=> 'LinkWalker',
							'lockon'					=> 'Lockon',
							'logo_gif'					=> 'logo.gif Crawler',
							'magpie'					=> 'Magpie',
							'marvin'					=> 'marvin/infoseek',
							'mattie'					=> 'Mattie',
							'mediafox'					=> 'MediaFox',
							'merzscope'					=> 'MerzScope',
							'meshexplorer'				=> 'NEC-MeshExplorer',
							'MindCrawler'				=> 'MindCrawler',
							'mnogosearch'				=> 'mnoGoSearch search engine software',
							'moget'						=> 'moget',
							'momspider'					=> 'MOMspider',
							'monster'					=> 'Monster',
							'motor'						=> 'Motor',
							'muncher'					=> 'Muncher',
							'muninn'					=> 'Muninn',
							'muscatferret'				=> 'Muscat Ferret',
							'mwdsearch'					=> 'Mwd.Search',
							'myweb'						=> 'Internet Shinchakubin',
							'NDSpider'					=> 'NDSpider',
							'netcarta'					=> 'NetCarta WebMap Engine',
							'netmechanic'				=> 'NetMechanic',
							'netscoop'					=> 'NetScoop',
							'newscan-online'			=> 'newscan-online',
							'nhse'						=> 'NHSE Web Forager',
							'nomad'						=> 'Nomad',
							'northstar'					=> 'The NorthStar Robot',
							'objectssearch'				=> 'ObjectsSearch',
							'occam'						=> 'Occam',
							'octopus'					=> 'HKU WWW Octopus',
							'OntoSpider'				=> 'OntoSpider',
							'openfind'					=> 'Openfind data gatherer',
							'orb_search'				=> 'Orb Search',
							'packrat'					=> 'Pack Rat',
							'pageboy'					=> 'PageBoy',
							'parasite'					=> 'ParaSite',
							'patric'					=> 'Patric',
							'pegasus'					=> 'pegasus',
							'perignator'				=> 'The Peregrinator',
							'perlcrawler'				=> 'PerlCrawler 1.0',
							'phantom'					=> 'Phantom',
							'phpdig'					=> 'PhpDig',
							'pitkow'					=> 'html_analyzer',
							'pjspider'					=> 'Portal Juice Spider',
							'pka'						=> 'PGP Key Agent',
							'poppi'						=> 'Poppi',
							'portalb'					=> 'PortalB Spider',
							'psbot'						=> 'psbot',
							'Puu'						=> 'GetterroboPlus Puu',
							'python'					=> 'The Python Robot',
							'raven '					=> 'Raven Search',
							'rbse'						=> 'RBSE Spider',
							'resumerobot'				=> 'Resume Robot',
							'rhcs'						=> 'RoadHouse Crawling System',
							'rixbot'					=> 'RixBot',
							'roadrunner'				=> 'Road Runner: The ImageScape Robot',
							'robbie'					=> 'Robbie the Robot',
							'robi'						=> 'ComputingSite Robi/1.0',
							'robocrawl'					=> 'RoboCrawl Spider',
							'robofox'					=> 'RoboFox',
							'robozilla'					=> 'Robozilla',
							'roverbot'					=> 'Roverbot',
							'rules'						=> 'RuLeS',
							'safetynetrobot'			=> 'SafetyNet Robot',
							'scooter'					=> 'Scooter',
							'search_au'					=> 'Search.Aus-AU.COM',
							'search-info'				=> 'Sleek',
							'searchprocess'				=> 'SearchProcess',
							'senrigan'					=> 'Senrigan',
							'sgscout'					=> 'SG-Scout',
							'shaggy'					=> 'ShagSeeker',
							'sift'						=> 'Sift',
							'simbot'					=> 'Simmany Robot Ver1.0',
							'site-valet'				=> 'Site Valet',
							'sitetech'					=> 'SiteTech-Rover',
							'skymob'					=> 'Skymob.com',
							'slcrawler'					=> 'SLCrawler',
							'smartspider'				=> 'Smart Spider',
							'snooper'					=> 'Snooper',
							'solbot'					=> 'Solbot',
							'speedy'					=> 'Speedy Spider',
							'spider_monkey'				=> 'spider_monkey',
							'spiderbot'					=> 'SpiderBot',
							'spiderline'				=> 'Spiderline Crawler',
							'spiderman'					=> 'SpiderMan',
							'spiderview'				=> 'SpiderView(tm)',
							'spry'						=> 'Spry Wizard Robot',
							'ssearcher'					=> 'Site Searcher',
							'suke'						=> 'Suke',
							'suntek'					=> 'suntek search engine',
							'sven'						=> 'Sven',
							'sygol'						=> 'Sygol ',
							'tach_bw'					=> 'TACH Black Widow',
							'tarantula'					=> 'Tarantula',
							'tarspider'					=> 'tarspider',
							'tcl'						=> 'Tcl W3 Robot',
							'techbot'					=> 'TechBOT',
							'templeton'					=> 'Templeton',
							'titin'						=> 'TitIn',
							'titan'						=> 'TITAN',
							'tkwww'						=> 'The TkWWW Robot',
							'tlspider'					=> 'TLSpider',
							'ucsd'						=> 'UCSD Crawl',
							'udmsearch'					=> 'UdmSearch',
							'ultraseek'					=> 'Ultraseek',
							'uptimebot'					=> 'UptimeBot',
							'urlck'						=> 'URL Check',
							'valkyrie'					=> 'Valkyrie',
							'verticrawl'				=> 'Verticrawl',
							'victoria'					=> 'Victoria',
							'visionsearch'				=> 'vision-search',
							'voidbot'					=> 'void-bot',
							'voyager'					=> 'Voyager',
							'vwbot'						=> 'VWbot',
							'w3index'					=> 'The NWI Robot',
							'w3m2'						=> 'W3M2',
							'wallpaper'					=> 'WallPaper (alias crawlpaper)',
							'wanderer'					=> 'the World Wide Web Wanderer',
							'wapspider'					=> 'w@pSpider by wap4.com',
							'webbandit'					=> 'WebBandit Web Spider',
							'webcatcher'				=> 'WebCatcher',
							'webcopy'					=> 'WebCopy',
							'webfetcher'				=> 'webfetcher',
							'webfoot'					=> 'The Webfoot Robot',
							'webinator'					=> 'Webinator',
							'weblayers'					=> 'weblayers',
							'weblinker'					=> 'WebLinker',
							'webmirror'					=> 'WebMirror',
							'webmoose'					=> 'The Web Moose',
							'webquest'					=> 'WebQuest',
							'webreader'					=> 'Digimarc MarcSpider',
							'webreaper'					=> 'WebReaper',
							'webs'						=> 'webs',
							'websnarf'					=> 'Websnarf',
							'webspider'					=> 'WebSpider',
							'webvac'					=> 'WebVac',
							'webwalk'					=> 'webwalk',
							'webwalker'					=> 'WebWalker',
							'webwatch'					=> 'WebWatch',
							'wget'						=> 'Wget',
							'whatuseek'					=> 'whatUseek Winona',
							'whowhere'					=> 'WhoWhere Robot',
							'wlm'						=> 'Weblog Monitor',
							'wmir'						=> 'w3mir',
							'wolp'						=> 'WebStolperer',
							'wombat'					=> 'The Web Wombat ',
							'worm'						=> 'The World Wide Web Worm',
							'wwwc'						=> 'WWWC Ver 0.2.5',
							'wz101'						=> 'WebZinger',
							'xget'						=> 'XGET',				
						);	


	
	/**
	 * Constructor
	 *
	 * Sets the User Agent and runs the compilation routine
	 *
	 * @access	public
	 * @return	void
	 */		
	function CI_User_agent()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
		}
		
		if ( ! is_null($this->agent))
		{
			$this->_compile_data();
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Compile the User Agent Data
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _compile_data()
	{
		$this->_set_platform();
	
		foreach (array('_set_browser', '_set_robot', '_set_mobile') as $function)
		{
			if ($this->$function() === TRUE)
			{
				break;
			}
		}	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the Platform
	 *
	 * @access	private
	 * @return	mixed
	 */		
	function _set_platform()
	{
		foreach ($this->platforms as $key => $val) 
		{
			if (preg_match("|$key|i", $this->agent)) 
			{
				$this->platform = $val;
				return TRUE;
			}
		}
	
		$this->platform = 'Unknown Platform';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set the Browser
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _set_browser()
	{
		foreach ($this->browsers as $key => $val) 
		{		
			if (preg_match("|".$key.".*?([0-9\.]+)|i", $this->agent, $match)) 
			{
				$this->is_browser = TRUE;
				$this->version = $match[1];
				$this->browser = $val;
				return TRUE;
			}
		}
		
		return FALSE;
	}
			
	// --------------------------------------------------------------------
	
	/**
	 * Set the Robot
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _set_robot()
	{
		foreach ($this->robots as $key => $val) 
		{
			if (preg_match("|$key|i", $this->agent)) 
			{
				$this->is_robot = TRUE;
				$this->robot = $val;
				return TRUE;
			}
		}
	
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set the Mobile Devise
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _set_mobile()
	{
		foreach ($this->mobiles as $key => $val) 
		{
			if (FALSE !== (strpos(strtolower($this->agent), $key)))
			{
				$this->is_mobile = TRUE;
				$this->mobile = $val;
				return TRUE;
			}
		}
	
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the accepted languages
	 *
	 * @access	private
	 * @return	void
	 */			
	function _set_languages()
	{
		if ((count($this->languages) == 0) AND isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) AND $_SERVER['HTTP_ACCEPT_LANGUAGE'] != '')
		{
			$languages = preg_replace('/(;q=.+)/i', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			
			$this->languages = explode(',', $languages);
		}
		
		if (count($this->languages) == 0)
		{
			$this->languages = array('Undefined');
		}	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the accepted character sets
	 *
	 * @access	private
	 * @return	void
	 */			
	function _set_charsets()
	{	
		if ((count($this->charsets) == 0) AND isset($_SERVER['HTTP_ACCEPT_CHARSET']) AND $_SERVER['HTTP_ACCEPT_CHARSET'] != '')
		{
			$charsets = preg_replace('/(;q=.+)/i', '', $_SERVER['HTTP_ACCEPT_CHARSET']);
			
			$this->charsets = explode(',', $charsets);
		}
		
		if (count($this->charsets) == 0)
		{
			$this->charsets = array('Undefined');
		}	
	}

	// --------------------------------------------------------------------
	
	/**
	 * Is Browser
	 *
	 * @access	public
	 * @return	bool
	 */		
	function is_browser()
	{
		return $this->is_browser;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Is Robot
	 *
	 * @access	public
	 * @return	bool
	 */		
	function is_robot()
	{
		return $this->is_robot;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Is Mobile
	 *
	 * @access	public
	 * @return	bool
	 */		
	function is_mobile()
	{
		return $this->is_mobile;
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Is this a referral from another site?
	 *
	 * @access	public
	 * @return	bool
	 */			
	function is_referral()
	{
		return ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Agent String
	 *
	 * @access	public
	 * @return	string
	 */			
	function agent()
	{
		return $this->agent;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Platform
	 *
	 * @access	public
	 * @return	string
	 */			
	function platform()
	{
		return $this->platform;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Browser Name
	 *
	 * @access	public
	 * @return	string
	 */			
	function browser()
	{
		return $this->browser;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the Browser Version
	 *
	 * @access	public
	 * @return	string
	 */			
	function version()
	{
		return $this->version;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get The Robot Name
	 *
	 * @access	public
	 * @return	string
	 */				
	function robot()
	{
		return $this->robot;
	}
	// --------------------------------------------------------------------
	
	/**
	 * Get the Mobile Devise
	 *
	 * @access	public
	 * @return	string
	 */			
	function mobile()
	{
		return $this->mobile;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the referrer
	 *
	 * @access	public
	 * @return	bool
	 */			
	function referrer()
	{
		return ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : $_SERVER['HTTP_REFERER'];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the accepted languages
	 *
	 * @access	public
	 * @return	array
	 */			
	function languages()
	{
		if (count($this->languages) == 0)
		{
			$this->_set_languages();
		}
	
		return $this->languages;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the accepted Character Sets
	 *
	 * @access	public
	 * @return	array
	 */			
	function charsets()
	{
		if (count($this->charsets) == 0)
		{
			$this->_set_charsets();
		}
	
		return $this->charsets;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Test for a particular language
	 *
	 * @access	public
	 * @return	bool
	 */			
	function accept_lang($lang = 'en')
	{
		return (in_array(strtolower($lang), $this->languages(), TRUE)) ? TRUE : FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Test for a particular character set
	 *
	 * @access	public
	 * @return	bool
	 */			
	function accept_charset($charset = 'utf-8')
	{
		return (in_array(strtolower($charset), $this->charsets(), TRUE)) ? TRUE : FALSE;
	}
	
	
}

?>