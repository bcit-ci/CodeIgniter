<?php

/**
 * Smarty compatibility layer for block plugins, this is used internally and you should not call it
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
class Dwoo_Plugin_smartyinterface extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block
{
	public function init($__funcname, $__functype, array $rest=array()) {}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		$params = $compiler->getCompiledParams($params);
		$func = $params['__funcname'];
		$pluginType = $params['__functype'];
		$params = $params['*'];

		if ($pluginType & Dwoo::CUSTOM_PLUGIN) {
			$customPlugins = $compiler->getDwoo()->getCustomPlugins();
			$callback = $customPlugins[$func]['callback'];
			if (is_array($callback)) {
				if (is_object($callback[0])) {
					$callback = '$this->customPlugins[\''.$func.'\'][0]->'.$callback[1].'(';
				} else {
					$callback = ''.$callback[0].'::'.$callback[1].'(';
				}
			} else {
				$callback = $callback.'(';
			}
		} else {
			$callback = 'smarty_block_'.$func.'(';
		}

		$paramsOut = '';
		foreach ($params as $i=>$p) {
			$paramsOut .= var_export($i, true).' => '.$p.',';
		}

		$curBlock =& $compiler->getCurrentBlock();
		$curBlock['params']['postOut'] = Dwoo_Compiler::PHP_OPEN.' $_block_content = ob_get_clean(); $_block_repeat=false; echo '.$callback.'$_tag_stack[count($_tag_stack)-1], $_block_content, $this, $_block_repeat); } array_pop($_tag_stack);'.Dwoo_Compiler::PHP_CLOSE;

		return Dwoo_Compiler::PHP_OPEN.$prepend.' if (!isset($_tag_stack)){ $_tag_stack = array(); } $_tag_stack[] = array('.$paramsOut.'); $_block_repeat=true; '.$callback.'$_tag_stack[count($_tag_stack)-1], null, $this, $_block_repeat); while ($_block_repeat) { ob_start();'.Dwoo_Compiler::PHP_CLOSE;
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		return $content . $params['postOut'];
	}
}
