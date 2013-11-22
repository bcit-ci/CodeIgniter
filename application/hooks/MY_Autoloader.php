<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_Autoloader Class
 *
 * @package		CodeIgniter
 * @subpackage	Hooks
 * @category	Hooks
 * @author		Shane Pearson <shane@highermedia.com>
 */
class MY_Autoloader {

	private $_include_paths = array();

	/**
	 * Register the autoloader function.
	 *
	 * @access public
	 * @param array include paths
	 * @return void
	 */
	public function register(array $paths = array())
	{
		$this->_include_paths = $paths;
		
		spl_autoload_register(array($this, 'autoloader'));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Autoload base classes.
	 *
	 * @access public
	 * @param string class to load
	 * @return void
	 */
	public function autoloader($class)
	{
		foreach($this->_include_paths as $path)
		{
			$filepath = $path . $class . EXT;
			
			if(! class_exists($class, FALSE) AND is_file($filepath))
			{
				include_once($filepath);

				break;
			}		
		}
			
	}

	// --------------------------------------------------------------------
	
} // end class MY_Autoloader

/* End of file MY_Autoloader.php */
/* Location: ./application/hooks/MY_Autoloader.php */