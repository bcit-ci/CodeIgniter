<?php

/**
 * This plugin serves as a {else} block specifically for the {for} plugin.
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
class Dwoo_Plugin_forelse extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block
{
	public function init()
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		$with =& $compiler->findBlock('for', true);

		$params['initialized'] = true;
		$compiler->injectBlock($type, $params);

		return '';
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		if (!isset($params['initialized'])) {
			return '';
		}

		$block =& $compiler->getCurrentBlock();
		$block['params']['hasElse'] = Dwoo_Compiler::PHP_OPEN."else {\n".Dwoo_Compiler::PHP_CLOSE . $content . Dwoo_Compiler::PHP_OPEN."\n}".Dwoo_Compiler::PHP_CLOSE;
		return '';
	}
}
