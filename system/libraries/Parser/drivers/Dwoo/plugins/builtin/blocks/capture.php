<?php

/**
 * Captures all the output within this block and saves it into {$.capture.default} by default,
 * or {$.capture.name} if you provide another name.
 * <pre>
 *  * name : capture name, used to read the value afterwards
 *  * assign : if set, the value is also saved in the given variable
 *  * cat : if true, the value is appended to the previous one (if any) instead of overwriting it
 * </pre>
 * If the cat parameter is true, the content
 * will be appended to the existing content
 *
 * Example :
 *
 * <code>
 * {capture "foo"}
 *   Anything in here won't show, it will be saved for later use..
 * {/capture}
 * Output was : {$.capture.foo}
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
class Dwoo_Plugin_capture extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block
{
	public function init($name = 'default', $assign = null, $cat = false, $trim = false)
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		return Dwoo_Compiler::PHP_OPEN.$prepend.'ob_start();'.$append.Dwoo_Compiler::PHP_CLOSE;
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		$params = $compiler->getCompiledParams($params);

		$out = $content . Dwoo_Compiler::PHP_OPEN.$prepend."\n".'$tmp = ob_get_clean();';
		if ($params['trim'] !== 'false' && $params['trim'] !== 0) {
			$out .= "\n".'$tmp = trim($tmp);';
		}
		if ($params['cat'] === 'true' || $params['cat'] === 1) {
			$out .= "\n".'$tmp = $this->readVar(\'dwoo.capture.\'.'.$params['name'].') . $tmp;';
		}
		if ($params['assign'] !== 'null') {
			$out .= "\n".'$this->scope['.$params['assign'].'] = $tmp;';
		}
		return $out . "\n".'$this->globals[\'capture\']['.$params['name'].'] = $tmp;'.$append.Dwoo_Compiler::PHP_CLOSE;
	}
}
