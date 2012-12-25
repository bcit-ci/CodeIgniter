<?php

/**
 * Performs some template conversions to allow smarty templates to be used by
 * the Dwoo compiler.
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
class Dwoo_Processor_smarty_compat extends Dwoo_Processor
{
	public function process($input)
	{
		list($l, $r) = $this->compiler->getDelimiters();

		$rl = preg_quote($l,'/');
		$rr = preg_quote($r,'/');
		$sectionParam = '(?:(name|loop|start|step|max|show)\s*=\s*(\S+))?\s*';
		$input = preg_replace_callback('/'.$rl.'\s*section '.str_repeat($sectionParam, 6).'\s*'.$rr.'(.+?)(?:'.$rl.'\s*sectionelse\s*'.$rr.'(.+?))?'.$rl.'\s*\/section\s*'.$rr.'/is', array($this, 'convertSection'), $input);
		$input = str_replace('$smarty.section.', '$smarty.for.', $input);

		$smarty = array
		(
			'/'.$rl.'\s*ldelim\s*'.$rr.'/',
			'/'.$rl.'\s*rdelim\s*'.$rr.'/',
			'/'.$rl.'\s*\$smarty\.ldelim\s*'.$rr.'/',
			'/'.$rl.'\s*\$smarty\.rdelim\s*'.$rr.'/',
			'/\$smarty\./',
			'/'.$rl.'\s*php\s*'.$rr.'/',
			'/'.$rl.'\s*\/php\s*'.$rr.'/',
			'/\|(@?)strip(\||'.$rr.')/',
			'/'.$rl.'\s*sectionelse\s*'.$rr.'/',
		);

		$dwoo = array
		(
			'\\'.$l,
			$r,
			'\\'.$l,
			$r,
			'$dwoo.',
			'<?php ',
			' ?>',
			'|$1whitespace$2',
			$l.'else'.$r,
		);

		if (preg_match('{\|@([a-z][a-z0-9_]*)}i', $input, $matches)) {
			trigger_error('The Smarty Compatibility Module has detected that you use |@'.$matches[1].' in your template, this might lead to problems as Dwoo interprets the @ operator differently than Smarty, see http://wiki.dwoo.org/index.php/Syntax#The_.40_Operator', E_USER_NOTICE);
		}

		return preg_replace($smarty, $dwoo, $input);
	}

	protected function convertSection(array $matches)
	{
		$params = array();
		$index = 1;
		while (!empty($matches[$index]) && $index < 13) {
			$params[$matches[$index]] = $matches[$index+1];
			$index += 2;
		}
		return str_replace('['.trim($params['name'], '"\'').']', '[$'.trim($params['name'], '"\'').']', $matches[0]);
	}
}
