<?php

/**
 * Moves the scope down into the provided variable, allowing you to use shorter
 * variable names if you repeatedly access values into a single array
 *
 * The with block won't display anything at all if the provided scope is empty,
 * so in effect it acts as {if $var}*content*{/if}
 * <pre>
 *  * var : the variable name to move into
 * </pre>
 * Example :
 *
 * instead of the following :
 *
 * <code>
 * {if $long.boring.prefix}
 *   {$long.boring.prefix.val} - {$long.boring.prefix.secondVal} - {$long.boring.prefix.thirdVal}
 * {/if}
 * </code>
 *
 * you can use :
 *
 * <code>
 * {with $long.boring.prefix}
 *   {$val} - {$secondVal} - {$thirdVal}
 * {/with}
 * </code>
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
class Dwoo_Plugin_with extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block, Dwoo_IElseable
{
	protected static $cnt=0;

	public function init($var)
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		return '';
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		$rparams = $compiler->getRealParams($params);
		$cparams = $compiler->getCompiledParams($params);

		$compiler->setScope($rparams['var']);


		$pre = Dwoo_Compiler::PHP_OPEN. 'if ('.$cparams['var'].')'."\n{\n".
			'$_with'.(self::$cnt).' = $this->setScope("'.$rparams['var'].'");'.
			"\n/* -- start with output */\n".Dwoo_Compiler::PHP_CLOSE;

		$post = Dwoo_Compiler::PHP_OPEN. "\n/* -- end with output */\n".
			'$this->setScope($_with'.(self::$cnt++).', true);'.
			"\n}\n".Dwoo_Compiler::PHP_CLOSE;

		if (isset($params['hasElse'])) {
			$post .= $params['hasElse'];
		}

		return $pre . $content . $post;
	}
}
