<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Plugins Class
 *
 * Enables third-party-applications to work with the installation
 *
 * @author hice3000
 */
class CI_Plugins {

	/*
	 * Are plugins loaded?
	 * 
	 * @var bool
	 */
	var $plugins_loaded;
	
	/*
	 * List of installed version with basic information
	 * 
	 * @var array
	 */
	var $plugins;
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		$CFG =& load_class('Config', 'core');

		// If plugins are not enabled in the config file
		// there is nothing else to do
		if ($CFG->item('enable_plugin_system') === FALSE)
		{
			return;
		}
		
		$this->_load_plugins();
		$this->_register_hooks();
		
		log_message('debug', 'Plugin System initialized');

	}

	/*
	 * Triggers a hook
	 * 
	 * @param string $type Type of the hook (for example: "system.pre_controller")
	 * @param array $params Parameters of the Hook than can be edited (!) by a Plugin
	 * @return array The modified version of $params
	 */
	public function fire($type, $params = array())
	{
		$return = $params;
		foreach ($this->plugins as $plugin)
		{
			$hooks = $plugin['registered_hooks'];
			if (array_key_exists($type, $hooks))
			{
				$tmp =& $plugin['instance'];
				$tmp_return = $tmp->$hooks[$type]($return);
				if (is_array($tmp_return))
				{
					// If return is a array, merge it
					$return = array_merge($return, $tmp_return);
				} else
				{
					// If not, throw an error to the log
					log_message('error', 'Invalid Hook (\''.$type.'\') in Plugin \''.$plugin['name'].'\'');
				}
			}
		}
		return $return;
	}
	
	protected function _load_plugins()
	{
		$plugin_folders = array();
		foreach (new DirectoryIterator('plugins/') as $file)
		{
			if($file->isDot()) continue;
			if($file->isDir())
			{
				$plugin_folders[] = $file->getFilename();
			}
		}
		
		// Search for Plugin file in every Plugin Directory
		$plugins = array();
		$i = 0;
		foreach ($plugin_folders as $folder)
		{
			// Require the Plugin file
			require 'plugins/'.$folder.'/'.$folder.'.php';
			$tmp = new $folder();
			$plugins[$i] = $tmp->get_information();
			$plugins[$i]['directory'] = $folder;
			$plugins[$i]['instance'] = $tmp;
			$tmp = NULL;
			$i++;
		}
		
		
		// I can't save it in a field, because we use different instances of this class!
		$this->plugins = $plugins;
	}

	protected function _register_hooks()
	{
		foreach ($this->plugins as $key=>$plugin)
		{
			$dir = $plugin['directory'];
			$tmp =& $plugin['instance'];
			$hooks = $tmp->register_hooks();
			$this->plugins[$key]['registered_hooks'] = $hooks;
		}
	}
	
	/*
	 * Getter method
	 * 
	 * @return array list of plugins with basic information
	 */
	public function get_plugins()
	{
		return $this->plugins;
	}
	
}

/* End of file Plugins.php */
/* Location: ./system/core/Plugins.php */