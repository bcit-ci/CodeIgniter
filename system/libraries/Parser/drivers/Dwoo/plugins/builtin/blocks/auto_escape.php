<?php

/**
 * Overrides the compiler auto-escape setting within the block
 * <pre>
 *  * enabled : if set to "on", "enable", true or 1 then the compiler autoescaping is enabled inside this block. set to "off", "disable", false or 0 to disable it
 * </pre>
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
class Dwoo_Plugin_auto_escape extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block
{
	protected static $stack = array();

	public function init($enabled)
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		$params = $compiler->getCompiledParams($params);
		switch(strtolower(trim((string) $params['enabled'], '"\''))) {

		case 'on':
		case 'true':
		case 'enabled':
		case 'enable':
		case '1':
			$enable = true;
			break;
		case 'off':
		case 'false':
		case 'disabled':
		case 'disable':
		case '0':
			$enable = false;
			break;
		default:
			throw new Dwoo_Compilation_Exception($compiler, 'Auto_Escape : Invalid parameter ('.$params['enabled'].'), valid parameters are "enable"/true or "disable"/false');

		}

		self::$stack[] = $compiler->getAutoEscape();
		$compiler->setAutoEscape($enable);
		return '';
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		$compiler->setAutoEscape(array_pop(self::$stack));
		return $content;
	}
}
