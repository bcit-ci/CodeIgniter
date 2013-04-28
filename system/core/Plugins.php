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
		$CFG->load('plugins');

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
		$CFG =& load_class('Config', 'core');
		$CFG->load('plugins');
		if ($CFG->item('enable_plugin_system') === FALSE)
		{
			return;
		}
		$return = $params;
		foreach ($this->plugins as $plugin)
		{
			if (!$plugin['disabled'])
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
						log_message('error', 'Invalid Hook Result (\''.$type.'\') in Plugin \''.$plugin['name'].'\'');
					}
				}
			}
		}
		return $return;
	}
	
	protected function _load_plugins()
	{
		$BM =& load_class('Benchmark', 'core');
		$BM->mark('load_plugins_start');
		
		$CFG =& load_class('Config', 'core');
		
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
			
			$config = $this->_convert_name($plugins[$i]['name']).'_enabled';
			if ($CFG->item($config) === FALSE)
			{
				$new_value = ($CFG->item('plugin_default_status') === TRUE) ? 'Yes' : 'No';
				$filename = APPPATH.'config/plugins.php';
				$content = file_get_contents($filename);
				$content .= "\n\$config['".$config."'] = '".$new_value."';\n";
				file_put_contents($filename, $content);
			} else
			{
				$plugins[$i]['disabled'] = ($CFG->item($config) === 'Yes') ? TRUE : FALSE;
			}
			
			$tmp = NULL;
			$i++;
		}
		
		
		$this->plugins = $plugins;
		$BM->mark('load_plugins_end');
	}

	/*
	 * Internal function to create a plugin's singular from its name
	 * 
	 * @param string $name Name of the Plugin
	 * @return string singular of the Plugin
	 */
	protected function _convert_name($name)
	{
		$tmp =  str_replace(array(
		    ' ', ':', '/', ',', '\'', ';', '\\'
		), '_', $name);
		return strtolower($tmp);
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
	 * Set's the status of a plugin
	 * 
	 * @param string $name Singular of the Plugin
	 * @param string $status 'Yes' or 'No'
	 * @return void
	 */
	public function set_plugin_disabled($name, $status)
	{
		$value = ($status == TRUE) ? 'No' : 'Yes';
		$config = file_get_contents(APPPATH.'config/plugins.php');
		$config = preg_replace('/\[(?:\'|\")'.$name.'_enabled(?:\'|\")\]\s*=\s*(\'|\")(.*)\\1;/', "['".$name."_enabled'] = '".$value."';", $config);
		file_put_contents(APPPATH.'config/plugins.php', $config);
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