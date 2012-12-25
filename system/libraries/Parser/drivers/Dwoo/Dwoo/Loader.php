<?php

/**
 * handles plugin loading and caching of plugins names/paths relationships
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
class Dwoo_Loader implements Dwoo_ILoader
{
	/**
	 * stores the plugin directories
	 *
	 * @see addDirectory
	 * @var array
	 */
	protected $paths = array();

	/**
	 * stores the plugins names/paths relationships
	 * don't edit this on your own, use addDirectory
	 *
	 * @see addDirectory
	 * @var array
	 */
	protected $classPath = array();

	/**
	 * path where class paths cache files are written
	 *
	 * @var string
	 */
	protected $cacheDir;

	protected $corePluginDir;

	public function __construct($cacheDir)
	{
		$this->corePluginDir = DWOO_DIRECTORY . 'plugins';
		$this->cacheDir = rtrim($cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		// include class paths or rebuild paths if the cache file isn't there
		$cacheFile = $this->cacheDir.'classpath.cache.d'.Dwoo::RELEASE_TAG.'.php';
		if (file_exists($cacheFile)) {
			$classpath = file_get_contents($cacheFile);
			$this->classPath = unserialize($classpath) + $this->classPath;
		} else {
			$this->rebuildClassPathCache($this->corePluginDir, $cacheFile);
		}
	}

	/**
	 * rebuilds class paths, scans the given directory recursively and saves all paths in the given file
	 *
	 * @param string $path the plugin path to scan
	 * @param string $cacheFile the file where to store the plugin paths cache, it will be overwritten
	 */
	protected function rebuildClassPathCache($path, $cacheFile)
	{
		if ($cacheFile!==false) {
			$tmp = $this->classPath;
			$this->classPath = array();
		}

		// iterates over all files/folders
		$list = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');
		if (is_array($list)) {
			foreach ($list as $f) {
				if (is_dir($f)) {
					$this->rebuildClassPathCache($f, false);
				} else {
					$this->classPath[str_replace(array('function.','block.','modifier.','outputfilter.','filter.','prefilter.','postfilter.','pre.','post.','output.','shared.','helper.'), '', basename($f, '.php'))] = $f;
				}
			}
		}

		// save in file if it's the first call (not recursed)
		if ($cacheFile!==false) {
			if (!file_put_contents($cacheFile, serialize($this->classPath))) {
				throw new Dwoo_Exception('Could not write into '.$cacheFile.', either because the folder is not there (create it) or because of the chmod configuration (please ensure this directory is writable by php), alternatively you can change the directory used with $dwoo->setCompileDir() or provide a custom loader object with $dwoo->setLoader()');
			}
			$this->classPath += $tmp;
		}
	}

	/**
	 * loads a plugin file
	 *
	 * @param string $class the plugin name, without the Dwoo_Plugin_ prefix
	 * @param bool $forceRehash if true, the class path caches will be rebuilt if the plugin is not found, in case it has just been added, defaults to true
	 */
	public function loadPlugin($class, $forceRehash = true)
	{
		// a new class was added or the include failed so we rebuild the cache
		if (!isset($this->classPath[$class]) || !(include $this->classPath[$class])) {
			if ($forceRehash) {
				$this->rebuildClassPathCache($this->corePluginDir, $this->cacheDir . 'classpath.cache.d'.Dwoo::RELEASE_TAG.'.php');
				foreach ($this->paths as $path=>$file) {
					$this->rebuildClassPathCache($path, $file);
				}
				if (isset($this->classPath[$class])) {
					include $this->classPath[$class];
				} else {
					throw new Dwoo_Exception('Plugin <em>'.$class.'</em> can not be found, maybe you forgot to bind it if it\'s a custom plugin ?', E_USER_NOTICE);
				}
			} else {
				throw new Dwoo_Exception('Plugin <em>'.$class.'</em> can not be found, maybe you forgot to bind it if it\'s a custom plugin ?', E_USER_NOTICE);
			}
		}
	}

	/**
	 * adds a plugin directory, the plugins found in the new plugin directory
	 * will take precedence over the other directories (including the default
	 * dwoo plugin directory), you can use this for example to override plugins
	 * in a specific directory for a specific application while keeping all your
	 * usual plugins in the same place for all applications.
	 *
	 * TOCOM don't forget that php functions overrides are not rehashed so you
	 * need to clear the classpath caches by hand when adding those
	 *
	 * @param string $pluginDirectory the plugin path to scan
	 */
	public function addDirectory($pluginDirectory)
	{
		$pluginDir = realpath($pluginDirectory);
		if (!$pluginDir) {
			throw new Dwoo_Exception('Plugin directory does not exist or can not be read : '.$pluginDirectory);
		}
		$cacheFile = $this->cacheDir . 'classpath-'.substr(strtr($pluginDir, '/\\:'.PATH_SEPARATOR, '----'), strlen($pluginDir) > 80 ? -80 : 0).'.d'.Dwoo::RELEASE_TAG.'.php';
		$this->paths[$pluginDir] = $cacheFile;
		if (file_exists($cacheFile)) {
			$classpath = file_get_contents($cacheFile);
			$this->classPath = unserialize($classpath) + $this->classPath;
		} else {
			$this->rebuildClassPathCache($pluginDir, $cacheFile);
		}
	}
}
