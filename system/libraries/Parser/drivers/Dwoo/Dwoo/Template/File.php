<?php

/**
 * represents a Dwoo template contained in a file
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
class Dwoo_Template_File extends Dwoo_Template_String
{
	/**
	 * template filename
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * include path(s) to look into to find this template
	 *
	 * @var array
	 */
	protected $includePath = null;

	/**
	 * resolved path cache when looking for a file in multiple include paths
	 *
	 * this is reset when the include path is changed
	 *
	 * @var string
	 */
	protected $resolvedPath = null;

	/**
	 * creates a template from a file
	 *
	 * @param string $file the path to the template file, make sure it exists
	 * @param int $cacheTime duration of the cache validity for this template,
	 * 						 if null it defaults to the Dwoo instance that will
	 * 						 render this template
	 * @param string $cacheId the unique cache identifier of this page or anything else that
	 * 						  makes this template's content unique, if null it defaults
	 * 						  to the current url
	 * @param string $compileId the unique compiled identifier, which is used to distinguish this
	 * 							template from others, if null it defaults to the filename+bits of the path
	 * @param mixed $includePath a string for a single path to look into for the given file, or an array of paths
	 */
	public function __construct($file, $cacheTime = null, $cacheId = null, $compileId = null, $includePath = null)
	{
		$this->file = $file;
		$this->name = basename($file);
		$this->cacheTime = $cacheTime;

		if ($compileId !== null) {
			$this->compileId = str_replace('../', '__', strtr($compileId, '\\%?=!:;'.PATH_SEPARATOR, '/-------'));
		}

		if ($cacheId !== null) {
			$this->cacheId = str_replace('../', '__', strtr($cacheId, '\\%?=!:;'.PATH_SEPARATOR, '/-------'));
		}

		if (is_string($includePath)) {
			$this->includePath = array($includePath);
		} elseif (is_array($includePath)) {
			$this->includePath = $includePath;
		}
	}

	/**
	 * sets the include path(s) to where the given template filename must be looked up
	 *
	 * @param mixed $paths the path to look into, can be string for a single path or an array of paths
	 */
	public function setIncludePath($paths)
	{
		if (is_array($paths) === false) {
			$paths = array($paths);
		}

		$this->includePath = $paths;
		$this->resolvedPath = null;
	}

	/**
	 * return the current include path(s)
	 *
	 * @return array
	 */
	public function getIncludePath()
	{
		return $this->includePath;
	}

	/**
	 * Checks if compiled file is valid (exists and it's the modification is greater or
	 * equal to the modification time of the template file)
	 *
	 * @param string file
	 * @return boolean True cache file existance and it's modification time
	 */
	protected function isValidCompiledFile($file) {
		return parent::isValidCompiledFile($file) && (int)$this->getUid() <= filemtime($file);
	}

	/**
	 * returns the template source of this template
	 *
	 * @return string
	 */
	public function getSource()
	{
		return file_get_contents($this->getResourceIdentifier());
	}

	/**
	 * returns the resource name for this template class
	 *
	 * @return string
	 */
	public function getResourceName()
	{
		return 'file';
	}

	/**
	 * returns this template's source filename
	 *
	 * @return string
	 */
	public function getResourceIdentifier()
	{
		if ($this->resolvedPath !== null) {
			return $this->resolvedPath;
		} elseif ($this->includePath === null) {
			return $this->file;
		} else {
			foreach ($this->includePath as $path) {
				$path = rtrim($path, DIRECTORY_SEPARATOR);
				if (file_exists($path.DIRECTORY_SEPARATOR.$this->file) === true) {
					$this->resolvedPath = $path . DIRECTORY_SEPARATOR . $this->file;
					return $this->resolvedPath;
				}
			}

			throw new Dwoo_Exception('Template "'.$this->file.'" could not be found in any of your include path(s)');
		}
	}

	/**
	 * returns an unique value identifying the current version of this template,
	 * in this case it's the unix timestamp of the last modification
	 *
	 * @return string
	 */
	public function getUid()
	{
		return (string) filemtime($this->getResourceIdentifier());
	}

	/**
	 * returns a new template object from the given include name, null if no include is
	 * possible (resource not found), or false if include is not permitted by this resource type
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
	 * @return Dwoo_Template_File|null
	 */
	public static function templateFactory(Dwoo $dwoo, $resourceId, $cacheTime = null, $cacheId = null, $compileId = null, Dwoo_ITemplate $parentTemplate = null)
	{
		if (DIRECTORY_SEPARATOR === '\\') {
			$resourceId = str_replace(array("\t", "\n", "\r", "\f", "\v"), array('\\t', '\\n', '\\r', '\\f', '\\v'), $resourceId);
		}
		$resourceId = strtr($resourceId, '\\', '/');

		$includePath = null;

		if (file_exists($resourceId) === false) {
			if ($parentTemplate === null) {
				$parentTemplate = $dwoo->getTemplate();
			}
			if ($parentTemplate instanceof Dwoo_Template_File) {
				if ($includePath = $parentTemplate->getIncludePath()) {
					if (strstr($resourceId, '../')) {
						throw new Dwoo_Exception('When using an include path you can not reference a template into a parent directory (using ../)');
					}
				} else {
					$resourceId = dirname($parentTemplate->getResourceIdentifier()).DIRECTORY_SEPARATOR.$resourceId;
					if (file_exists($resourceId) === false) {
						return null;
					}
				}
			} else {
				return null;
			}
		}

		if ($policy = $dwoo->getSecurityPolicy()) {
			while (true) {
				if (preg_match('{^([a-z]+?)://}i', $resourceId)) {
					throw new Dwoo_Security_Exception('The security policy prevents you to read files from external sources : <em>'.$resourceId.'</em>.');
				}

				if ($includePath) {
					break;
				}

				$resourceId = realpath($resourceId);
				$dirs = $policy->getAllowedDirectories();
				foreach ($dirs as $dir=>$dummy) {
					if (strpos($resourceId, $dir) === 0) {
						break 2;
					}
				}
				throw new Dwoo_Security_Exception('The security policy prevents you to read <em>'.$resourceId.'</em>');
			}
		}

		$class = 'Dwoo_Template_File';
		if ($parentTemplate) {
			$class = get_class($parentTemplate);
		}
		return new $class($resourceId, $cacheTime, $cacheId, $compileId, $includePath);
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
			$this->compileId = str_replace('../', '__', strtr($this->getResourceIdentifier(), '\\:', '/-'));
		}
		return $dwoo->getCompileDir() . $this->compileId.'.d'.Dwoo::RELEASE_TAG.'.php';
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
		return '"'.$this->getUid().'" == filemtime('.var_export($this->getResourceIdentifier(), true).')';
	}
}
