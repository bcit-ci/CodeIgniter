<?php

/**
 * represents a Dwoo template contained in a string
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
class Dwoo_Template_String implements Dwoo_ITemplate
{
	/**
	 * template name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * template compilation id
	 *
	 * @var string
	 */
	protected $compileId;

	/**
	 * template cache id, if not provided in the constructor, it is set to
	 * the md4 hash of the request_uri. it is however highly recommended to
	 * provide one that will fit your needs.
	 *
	 * in all cases, the compilation id is prepended to the cache id to separate
	 * templates with similar cache ids from one another
	 *
	 * @var string
	 */
	protected $cacheId;

	/**
	 * validity duration of the generated cache file (in seconds)
	 *
	 * set to -1 for infinite cache, 0 to disable and null to inherit the Dwoo instance's cache time
	 *
	 * @var int
	 */
	protected $cacheTime;

	/**
	 * boolean flag that defines whether the compilation should be enforced (once) or
	 * not use this if you have issues with the compiled templates not being updated
	 * but if you do need this it's most likely that you should file a bug report
	 *
	 * @var bool
	 */
	protected $compilationEnforced;

	/**
	 * caches the results of the file checks to save some time when the same
	 * templates is rendered several times
	 *
	 * @var array
	 */
	protected static $cache = array('cached'=>array(), 'compiled'=>array());

	/**
	 * holds the compiler that built this template
	 *
	 * @var Dwoo_ICompiler
	 */
	protected $compiler;

	/**
	 * chmod value for all files written (cached or compiled ones)
	 *
	 * set to null if you don't want any chmod operation to happen
	 *
	 * @var int
	 */
	protected $chmod = 0777;

	/**
	 * creates a template from a string
	 *
	 * @param string $templateString the template to use
	 * @param int $cacheTime duration of the cache validity for this template,
	 * 						 if null it defaults to the Dwoo instance that will
	 * 						 render this template, set to -1 for infinite cache or 0 to disable
	 * @param string $cacheId the unique cache identifier of this page or anything else that
	 * 						  makes this template's content unique, if null it defaults
	 * 						  to the current url
	 * @param string $compileId the unique compiled identifier, which is used to distinguish this
	 * 							template from others, if null it defaults to the md4 hash of the template
	 */
	public function __construct($templateString, $cacheTime = null, $cacheId = null, $compileId = null)
	{
		$this->template = $templateString;
		if (function_exists('hash')) {
			$this->name = hash('md4', $templateString);
		} else {
			$this->name = md5($templateString);
		}
		$this->cacheTime = $cacheTime;

		if ($compileId !== null) {
			$this->compileId = str_replace('../', '__', strtr($compileId, '\\%?=!:;'.PATH_SEPARATOR, '/-------'));
		}

		if ($cacheId !== null) {
			$this->cacheId = str_replace('../', '__', strtr($cacheId, '\\%?=!:;'.PATH_SEPARATOR, '/-------'));
		}
	}

	/**
	 * returns the cache duration for this template
	 *
	 * defaults to null if it was not provided
	 *
	 * @return int|null
	 */
	public function getCacheTime()
	{
		return $this->cacheTime;
	}

	/**
	 * sets the cache duration for this template
	 *
	 * can be used to set it after the object is created if you did not provide
	 * it in the constructor
	 *
	 * @param int $seconds duration of the cache validity for this template, if
	 * null it defaults to the Dwoo instance's cache time. 0 = disable and
	 * -1 = infinite cache
	 */
	public function setCacheTime($seconds = null)
	{
		$this->cacheTime = $seconds;
	}

	/**
	 * returns the chmod value for all files written (cached or compiled ones)
	 *
	 * defaults to 0777
	 *
	 * @return int|null
	 */
	public function getChmod()
	{
		return $this->chmod;
	}

	/**
	 * set the chmod value for all files written (cached or compiled ones)
	 *
	 * set to null if you don't want to do any chmod() operation
	 *
	 * @param int $mask new bitmask to use for all files
	 */
	public function setChmod($mask = null)
	{
		$this->chmod = $mask;
	}

	/**
	 * returns the template name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * returns the resource name for this template class
	 *
	 * @return string
	 */
	public function getResourceName()
	{
		return 'string';
	}

	/**
	 * returns the resource identifier for this template, false here as strings don't have identifiers
	 *
	 * @return false
	 */
	public function getResourceIdentifier()
	{
		return false;
	}

	/**
	 * returns the template source of this template
	 *
	 * @return string
	 */
	public function getSource()
	{
		return $this->template;
	}

	/**
	 * returns an unique value identifying the current version of this template,
	 * in this case it's the md4 hash of the content
	 *
	 * @return string
	 */
	public function getUid()
	{
		return $this->name;
	}

	/**
	 * returns the compiler used by this template, if it was just compiled, or null
	 *
	 * @return Dwoo_ICompiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}

	/**
	 * marks this template as compile-forced, which means it will be recompiled even if it
	 * was already saved and wasn't modified since the last compilation. do not use this in production,
	 * it's only meant to be used in development (and the development of dwoo particularly)
	 */
	public function forceCompilation()
	{
		$this->compilationEnforced = true;
	}

	/**
	 * returns the cached template output file name, true if it's cache-able but not cached
	 * or false if it's not cached
	 *
	 * @param Dwoo $dwoo the dwoo instance that requests it
	 * @return string|bool
	 */
	public function getCachedTemplate(Dwoo $dwoo)
	{
		if ($this->cacheTime !== null) {
			$cacheLength = $this->cacheTime;
		} else {
			$cacheLength = $dwoo->getCacheTime();
		}

		// file is not cacheable
		if ($cacheLength === 0) {
			return false;
		}

		$cachedFile = $this->getCacheFilename($dwoo);

		if (isset(self::$cache['cached'][$this->cacheId]) === true && file_exists($cachedFile)) {
			// already checked, return cache file
			return $cachedFile;
		} elseif ($this->compilationEnforced !== true && file_exists($cachedFile) && ($cacheLength === -1 || filemtime($cachedFile) > ($_SERVER['REQUEST_TIME'] - $cacheLength)) && $this->isValidCompiledFile($this->getCompiledFilename($dwoo))) {
			// cache is still valid and can be loaded
			self::$cache['cached'][$this->cacheId] = true;
			return $cachedFile;
		} else {
			// file is cacheable
			return true;
		}
	}

	/**
	 * caches the provided output into the cache file
	 *
	 * @param Dwoo $dwoo the dwoo instance that requests it
	 * @param string $output the template output
	 * @return mixed full path of the cached file or false upon failure
	 */
	public function cache(Dwoo $dwoo, $output)
	{
		$cacheDir = $dwoo->getCacheDir();
		$cachedFile = $this->getCacheFilename($dwoo);

		// the code below is courtesy of Rasmus Schultz,
		// thanks for his help on avoiding concurency issues
		$temp = tempnam($cacheDir, 'temp');
		if (!($file = @fopen($temp, 'wb'))) {
			$temp = $cacheDir . uniqid('temp');
			if (!($file = @fopen($temp, 'wb'))) {
				trigger_error('Error writing temporary file \''.$temp.'\'', E_USER_WARNING);
				return false;
			}
		}

		fwrite($file, $output);
		fclose($file);

		$this->makeDirectory(dirname($cachedFile), $cacheDir);
		if (!@rename($temp, $cachedFile)) {
			@unlink($cachedFile);
			@rename($temp, $cachedFile);
		}

		if ($this->chmod !== null) {
			chmod($cachedFile, $this->chmod);
		}

		self::$cache['cached'][$this->cacheId] = true;

		return $cachedFile;
	}

	/**
	 * clears the cached template if it's older than the given time
	 *
	 * @param Dwoo $dwoo the dwoo instance that was used to cache that template
	 * @param int $olderThan minimum time (in seconds) required for the cache to be cleared
	 * @return bool true if the cache was not present or if it was deleted, false if it remains there
	 */
	public function clearCache(Dwoo $dwoo, $olderThan = -1)
	{
		$cachedFile = $this->getCacheFilename($dwoo);

		return !file_exists($cachedFile) || (filectime($cachedFile) < (time() - $olderThan) && unlink($cachedFile));
	}

	/**
	 * returns the compiled template file name
	 *
	 * @param Dwoo $dwoo the dwoo instance that requests it
	 * @param Dwoo_ICompiler $compiler the compiler that must be used
	 * @return string
	 */
	public function getCompiledTemplate(Dwoo $dwoo, Dwoo_ICompiler $compiler = null)
	{
		$compiledFile = $this->getCompiledFilename($dwoo);

		if ($this->compilationEnforced !== true && isset(self::$cache['compiled'][$this->compileId]) === true) {
			// already checked, return compiled file
		} elseif ($this->compilationEnforced !== true && $this->isValidCompiledFile($compiledFile)) {
			// template is compiled
			self::$cache['compiled'][$this->compileId] = true;
		} else {
			// compiles the template
			$this->compilationEnforced = false;

			if ($compiler === null) {
				$compiler = $dwoo->getDefaultCompilerFactory($this->getResourceName());

				if ($compiler === null || $compiler === array('Dwoo_Compiler', 'compilerFactory')) {
					if (class_exists('Dwoo_Compiler', false) === false) {
						include DWOO_DIRECTORY . 'Dwoo/Compiler.php';
					}
					$compiler = Dwoo_Compiler::compilerFactory();
				} else {
					$compiler = call_user_func($compiler);
				}
			}

			$this->compiler = $compiler;

			$compiler->setCustomPlugins($dwoo->getCustomPlugins());
			$compiler->setSecurityPolicy($dwoo->getSecurityPolicy());
			$this->makeDirectory(dirname($compiledFile), $dwoo->getCompileDir());
			file_put_contents($compiledFile, $compiler->compile($dwoo, $this));
			if ($this->chmod !== null) {
				chmod($compiledFile, $this->chmod);
			}

			self::$cache['compiled'][$this->compileId] = true;
		}

		return $compiledFile;
	}

	/**
	 * Checks if compiled file is valid (it exists)
	 *
	 * @param string file
	 * @return boolean True cache file existance
	 */
	protected function isValidCompiledFile($file) {
		return file_exists($file);
	}

	/**
	 * returns a new template string object with the resource id being the template source code
	 *
	 * @param Dwoo $dwoo the dwoo instance requiring it
	 * @param mixed $resourceId the filename (relative to this template's dir) of the template to include
	 * @param int $cacheTime duration of the cache validity for this template,
	 * 						 if null it defaults to the Dwoo instance that will
	 * 						 render this template
	 * @param string $cacheId the unique cache identifier of this page or anything else that
	 * 						  makes this template's content unique, if null it defaults
	 * 						  to the current url
	 * @param string $compileId the unique compiled identifier, which is used to distinguish this
	 * 							template from others, if null it defaults to the filename+bits of the path
	 * @param Dwoo_ITemplate $parentTemplate the template that is requesting a new template object (through
	 * 											an include, extends or any other plugin)
	 * @return Dwoo_Template_String
	 */
	public static function templateFactory(Dwoo $dwoo, $resourceId, $cacheTime = null, $cacheId = null, $compileId = null, Dwoo_ITemplate $parentTemplate = null)
	{
		return new self($resourceId, $cacheTime, $cacheId, $compileId);
	}

	/**
	 * returns the full compiled file name and assigns a default value to it if
	 * required
	 *
	 * @param Dwoo $dwoo the dwoo instance that requests the file name
	 * @return string the full path to the compiled file
	 */
	protected function getCompiledFilename(Dwoo $dwoo)
	{
		// no compile id was provided, set default
		if ($this->compileId===null) {
			$this->compileId = $this->name;
		}
		return $dwoo->getCompileDir() . $this->compileId.'.d'.Dwoo::RELEASE_TAG.'.php';
	}

	/**
	 * returns the full cached file name and assigns a default value to it if
	 * required
	 *
	 * @param Dwoo $dwoo the dwoo instance that requests the file name
	 * @return string the full path to the cached file
	 */
	protected function getCacheFilename(Dwoo $dwoo)
	{
		// no cache id provided, use request_uri as default
		if ($this->cacheId === null) {
			if (isset($_SERVER['REQUEST_URI']) === true) {
				$cacheId = $_SERVER['REQUEST_URI'];
			} elseif (isset($_SERVER['SCRIPT_FILENAME']) && isset($_SERVER['argv'])) {
				$cacheId = $_SERVER['SCRIPT_FILENAME'].'-'.implode('-', $_SERVER['argv']);
			} else {
				$cacheId = '';
			}
			// force compiled id generation
			$this->getCompiledFilename($dwoo);

			$this->cacheId = str_replace('../', '__', $this->compileId . strtr($cacheId, '\\%?=!:;'.PATH_SEPARATOR, '/-------'));
		}
		return $dwoo->getCacheDir() . $this->cacheId.'.html';
	}

	/**
	 * returns some php code that will check if this template has been modified or not
	 *
	 * if the function returns null, the template will be instanciated and then the Uid checked
	 *
	 * @return string
	 */
	public function getIsModifiedCode()
	{
		return null;
	}

	/**
	 * ensures the given path exists
	 *
	 * @param string $path any path
	 * @param string $baseDir the base directory where the directory is created
	 *                        ($path must still contain the full path, $baseDir
	 *                        is only used for unix permissions)
	 */
	protected function makeDirectory($path, $baseDir = null)
	{
		if (is_dir($path) === true) {
			return;
		}

		if ($this->chmod === null) {
			$chmod = 0777;
		} else {
			$chmod = $this->chmod;
		}
		mkdir($path, $chmod, true);

		// enforce the correct mode for all directories created
		if (strpos(PHP_OS, 'WIN') !== 0 && $baseDir !== null) {
			$path = strtr(str_replace($baseDir, '', $path), '\\', '/');
			$folders = explode('/', trim($path, '/'));
			foreach ($folders as $folder) {
				$baseDir .= $folder . DIRECTORY_SEPARATOR;
				chmod($baseDir, $chmod);
			}
		}
	}
}
