<?php

/**
 * Dwoo adapter for Agavi
 *
 * Install instructions :
 *  - download dwoo from dwoo.org and unzip it somewhere in your agavi app
 *  - add a renderer to your output_types.xml as such :
 *     <renderer name="dwoo" class="DwooRenderer">
 *        <parameter name="assigns">
 *           <parameter name="routing">ro</parameter>
 *           <parameter name="request">rq</parameter>
 *           <parameter name="controller">ct</parameter>
 *           <parameter name="user">us</parameter>
 *           <parameter name="translation_manager">tm</parameter>
 *           <parameter name="request_data">rd</parameter>
 *        </parameter>
 *        <parameter name="extract_vars">true</parameter>
 *        <parameter name="plugin_dir">%core.lib_dir%/dwoo_plugins</parameter>
 *     </renderer>
 *
 *  - add dwoo's directory to your include path or include dwooAutoload.php yourself
 *    either through agavi's autoload.xml (with name="Dwoo") or through your index.php
 *
 * Notes:
 *  - you can copy the /Dwoo/Adapters/Agavi/dwoo_plugins directory to your agavi app's
 *    lib directory, or change the plugin_dir parameter in the output_types.xml file.
 *    these plugins are agavi-specific helpers that shortens the syntax to call common
 *    agavi helpers (i18n, routing, ..)
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the
 * use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
class DwooRenderer extends AgaviRenderer implements AgaviIReusableRenderer
{
	/**
	 * @constant   string The directory inside the cache dir where templates will
	 *                    be stored in compiled form.
	 */
	const COMPILE_DIR = 'templates';

	/**
	 * @constant   string The subdirectory inside the compile dir where templates
	 *                    will be stored in compiled form.
	 */
	const COMPILE_SUBDIR = 'dwoo';

	/**
	 * @constant   string The directory inside the cache dir where cached content
	 *                    will be stored.
	 */
	const CACHE_DIR = 'dwoo';

	/**
	 * @var        Dwoo Dwoo template engine.
	 */
	protected $dwoo = null;

	/**
	 * @var        string A string with the default template file extension,
	 *                    including the dot.
	 */
	protected $defaultExtension = '.html';

	/**
	 * stores the (optional) plugin directory to add to the Dwoo_Loader
	 */
	protected $plugin_dir = null;

	/**
	 * Pre-serialization callback.
	 *
	 * Excludes the Dwoo instance to prevent excessive serialization load.
	 */
	public function __sleep()
	{
		$keys = parent::__sleep();
		unset($keys[array_search('dwoo', $keys)]);
		return $keys;
	}

	/**
	 * Initialize this Renderer.
	 *
	 * @param      AgaviContext The current application context.
	 * @param      array        An associative array of initialization parameters.
	 */
	public function initialize(AgaviContext $context, array $parameters = array())
	{
		parent::initialize($context, $parameters);

		$this->plugin_dir = $this->getParameter('plugin_dir', $this->plugin_dir);
	}

	/**
	 * provides a custom compiler to the dwoo renderer with optional settings
	 * you can set in the agavi output_types.xml config file
	 *
	 * @return Dwoo_Compiler
	 */
	public function compilerFactory()
	{
		if (class_exists('Dwoo_Compiler', false) === false) {
			include DWOO_DIRECTORY . 'Dwoo/Compiler.php';
		}
		$compiler = Dwoo_Compiler::compilerFactory();
		$compiler->setAutoEscape((bool) $this->getParameter('auto_escape', false));
		return $compiler;
	}

	/**
	 * Grab a cleaned up dwoo instance.
	 *
	 * @return     Dwoo A Dwoo instance.
	 */
	protected function getEngine()
	{
		if($this->dwoo) {
			return $this->dwoo;
		}

		if(!class_exists('Dwoo')) {
			if (file_exists(dirname(__FILE__).'/../../../dwooAutoload.php')) {
				// file was dropped with the entire dwoo package
				require dirname(__FILE__).'/../../../dwooAutoload.php';
			} else {
				// assume the dwoo package is in the include path
				require 'dwooAutoload.php';
			}
		}

		$parentMode = fileperms(AgaviConfig::get('core.cache_dir'));

		$compileDir = AgaviConfig::get('core.cache_dir') . DIRECTORY_SEPARATOR . self::COMPILE_DIR . DIRECTORY_SEPARATOR . self::COMPILE_SUBDIR;
		AgaviToolkit::mkdir($compileDir, $parentMode, true);

		$cacheDir = AgaviConfig::get('core.cache_dir') . DIRECTORY_SEPARATOR . self::CACHE_DIR;
		AgaviToolkit::mkdir($cacheDir, $parentMode, true);

		$this->dwoo = new Dwoo($compileDir, $cacheDir);

		if (!empty($this->plugin_dir)) {
			$this->dwoo->getLoader()->addDirectory($this->plugin_dir);
		}

		$this->dwoo->setDefaultCompilerFactory('file', array($this, 'compilerFactory'));

		return $this->dwoo;
	}

	/**
	 * Render the presentation and return the result.
	 *
	 * @param      AgaviTemplateLayer The template layer to render.
	 * @param      array              The template variables.
	 * @param      array              The slots.
	 * @param      array              Associative array of additional assigns.
	 *
	 * @return     string A rendered result.
	 */
	public function render(AgaviTemplateLayer $layer, array &$attributes = array(), array &$slots = array(), array &$moreAssigns = array())
	{
		$engine = $this->getEngine();

		$data = array();
		if($this->extractVars) {
			$data = $attributes;
		} else {
			$data[$this->varName] = &$attributes;
		}

		$data[$this->slotsVarName] =& $slots;

		foreach($this->assigns as $key => $getter) {
			$data[$key] = $this->context->$getter();
		}

		foreach($moreAssigns as $key => &$value) {
			if(isset($this->moreAssignNames[$key])) {
				$key = $this->moreAssignNames[$key];
			}
			$data[$key] =& $value;
		}

		return $engine->get($layer->getResourceStreamIdentifier(), $data);
	}
}