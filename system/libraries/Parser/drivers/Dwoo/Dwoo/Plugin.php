<?php

/**
 * base plugin class
 *
 * you have to implement the <em>process()</em> method, it will receive the parameters that
 * are in the template code
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.0.0
 * @date       2008-10-23
 * @package    Dwoo
 */
abstract class Dwoo_Plugin
{
	/**
	 * the dwoo instance that runs this plugin
	 *
	 * @var Dwoo
	 */
	protected $dwoo;

	/**
	 * constructor, if you override it, call parent::__construct($dwoo); or assign
	 * the dwoo instance yourself if you need it
	 *
	 * @param Dwoo $dwoo the dwoo instance that runs this plugin
	 */
	public function __construct(Dwoo $dwoo)
	{
		$this->dwoo = $dwoo;
	}

	// plugins should always implement :
	// public function process($arg, $arg, ...)
	// or for block plugins :
	// public function init($arg, $arg, ...)

	// this could be enforced with :
	// abstract public function process(...);
	// if my feature request gets enough interest one day
	// see => http://bugs.php.net/bug.php?id=44043

	/**
	 * utility function that converts an array of compiled parameters (or rest array) to a string of xml/html tag attributes
	 *
	 * this is to be used in preProcessing or postProcessing functions, example :
	 *  $p = $compiler->getCompiledParams($params);
	 *  // get only the rest array as attributes
	 *  $attributes = Dwoo_Plugin::paramsToAttributes($p['*']);
	 *  // get all the parameters as attributes (if there is a rest array, it will be included)
	 *  $attributes = Dwoo_Plugin::paramsToAttributes($p);
	 *
	 * @param array $params an array of attributeName=>value items that will be compiled to be ready for inclusion in a php string
	 * @param string $delim the string delimiter you want to use (defaults to ')
	 * @return string
	 */
	public static function paramsToAttributes(array $params, $delim = '\'')
	{
		if (isset($params['*'])) {
			$params = array_merge($params, $params['*']);
			unset($params['*']);
		}

		$out = '';
		foreach ($params as $attr=>$val) {
			$out .= ' '.$attr.'=';
			if (trim($val, '"\'')=='' || $val=='null') {
				$out .= str_replace($delim, '\\'.$delim, '""');
			} elseif (substr($val, 0, 1) === $delim && substr($val, -1) === $delim) {
				$out .= str_replace($delim, '\\'.$delim, '"'.substr($val, 1, -1).'"');
			} else {
				$out .= str_replace($delim, '\\'.$delim, '"') . $delim . '.'.$val.'.' . $delim . str_replace($delim, '\\'.$delim, '"');
			}
		}

		return ltrim($out);
	}
}
