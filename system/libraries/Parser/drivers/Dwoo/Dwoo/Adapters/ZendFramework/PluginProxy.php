<?php

/**
 * PluginProxy class for Zend View
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the
 * use of this software.
 *
 * @author	   Denis Arh <denis@arh.cc>
 * @author	   Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Denis Arh, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.0.0
 * @date       2008-10-23
 * @package    Dwoo
 */
class Dwoo_Adapters_ZendFramework_PluginProxy implements Dwoo_IPluginProxy
{
	/**
	 * reference to the zend view owning this proxy
	 *
	 * @var Zend_View_Interface
	 */
	public $view;

	/**
	 * Dwoo_Adapters_ZendFramework_PluginProxy's constructor.
	 *
	 * @param Zend_View_Interface $view
	 */
	public function __construct(Zend_View_Interface $view) {
		$this->view = $view;
	}

	/**
	 * Called from Dwoo_Compiler to check if the requested plugin is available
	 *
	 * @param string $name
	 * @return bool
	 */
	public function handles($name) {
		try {
			$this->view->getHelper($name);
		} catch (Zend_Loader_PluginLoader_Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * returns the code (as a string) to call the plugin
	 * (this will be executed at runtime inside the Dwoo class)
	 *
	 * @param string $name the plugin name
	 * @param array $params a parameter array, array key "*" is the rest array
	 * @return string
	 */
	public function getCode($name, $params) {
		return '$this->getPluginProxy()->view->'. $name .'('.Dwoo_Compiler::implode_r($params).')';
	}

	/**
	 * returns a callback to the plugin, this is used with the reflection API to
	 * find out about the plugin's parameter names etc.
	 *
	 * should you need a rest array (i.e. for ZendFramework helpers) without the
	 * possibility to edit the plugin's code, you can provide a callback to some
	 * other function with the correct parameter signature, i.e. :
	 * <code>
	 * return array($this, "callbackHelper");
	 * // and callbackHelper would be as such:
	 * public function callbackHelper(array $rest=array()){}
	 * </code>
	 *
	 * @param string $name the plugin name
	 * @return callback
	 */
	public function getCallback($name) {
		return array($this->view->getHelper($name), $name);
	}

	/**
	 * returns some code that will check if the plugin is loaded and if not load it
	 * this is optional, if your plugins are autoloaded or whatever, just return an
	 * empty string
	 *
	 * @param string $name the plugin name
	 * @return string
	 */
	public function getLoader($name) {
		return '';
	}
}