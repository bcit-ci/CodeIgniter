<?php

/**
 * Marks the contents of the block as dynamic. Which means that it will not be cached.
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
class Dwoo_Plugin_dynamic extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block
{
	public function init()
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		return '';
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		$output = Dwoo_Compiler::PHP_OPEN .
			'if($doCache) {'."\n\t".
				'echo \'<dwoo:dynamic_\'.$dynamicId.\'>'.
				str_replace('\'', '\\\'', $content) .
				'</dwoo:dynamic_\'.$dynamicId.\'>\';'.
			"\n} else {\n\t";
				if(substr($content, 0, strlen(Dwoo_Compiler::PHP_OPEN)) == Dwoo_Compiler::PHP_OPEN) {
					$output .= substr($content, strlen(Dwoo_Compiler::PHP_OPEN));
				} else {
					$output .= Dwoo_Compiler::PHP_CLOSE . $content;
				}
				if(substr($output, -strlen(Dwoo_Compiler::PHP_CLOSE)) == Dwoo_Compiler::PHP_CLOSE) {
					$output = substr($output, 0, -strlen(Dwoo_Compiler::PHP_CLOSE));
				} else {
					$output .= Dwoo_Compiler::PHP_OPEN;
				}
			$output .= "\n}". Dwoo_Compiler::PHP_CLOSE;

		return $output;
	}

	public static function unescape($output, $dynamicId, $compiledFile)
	{
		$output = preg_replace_callback('/<dwoo:dynamic_('.$dynamicId.')>(.+?)<\/dwoo:dynamic_'.$dynamicId.'>/s', array('self', 'unescapePhp'), $output, -1, $count);
		// re-add the includes on top of the file
		if ($count && preg_match('#/\* template head \*/(.+?)/\* end template head \*/#s', file_get_contents($compiledFile), $m)) {
			$output = '<?php '.$m[1].' ?>'.$output;
		}
		return $output;
	}

	public static function unescapePhp($match)
	{
		return preg_replace('{<\?php /\*'.$match[1].'\*/ echo \'(.+?)\'; \?>}s', '$1', $match[2]);
	}
}
