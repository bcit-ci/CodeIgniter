<?php

/**
 * Outputs a html &lt;a&gt; tag
 * <pre>
 *  * href : the target URI where the link must point
 *  * rest : any other attributes you want to add to the tag can be added as named parameters
 * </pre>
 *
 * Example :
 *
 * <code>
 * {* Create a simple link out of an url variable and add a special class attribute: *}
 *
 * {a $url class="external" /}
 *
 * {* Mark a link as active depending on some other variable : *}
 *
 * {a $link.url class=tif($link.active "active"); $link.title /}
 *
 * {* This is similar to: <a href="{$link.url}" class="{if $link.active}active{/if}">{$link.title}</a> *}
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
class Dwoo_Plugin_a extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block
{
	public function init($href, array $rest=array())
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		$p = $compiler->getCompiledParams($params);

		$out = Dwoo_Compiler::PHP_OPEN . 'echo \'<a '.self::paramsToAttributes($p);

		return $out.'>\';' . Dwoo_Compiler::PHP_CLOSE;
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		$p = $compiler->getCompiledParams($params);

		// no content was provided so use the url as display text
		if ($content == "") {
			// merge </a> into the href if href is a string
			if (substr($p['href'], -1) === '"' || substr($p['href'], -1) === '\'') {
				return Dwoo_Compiler::PHP_OPEN . 'echo '.substr($p['href'], 0, -1).'</a>'.substr($p['href'], -1).';'.Dwoo_Compiler::PHP_CLOSE;
			}
			// otherwise append
			return Dwoo_Compiler::PHP_OPEN . 'echo '.$p['href'].'.\'</a>\';'.Dwoo_Compiler::PHP_CLOSE;
		}

		// return content
		return $content . '</a>';
	}
}
