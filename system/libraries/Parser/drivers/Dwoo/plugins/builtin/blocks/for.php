<?php

/**
 * Similar to the php for block
 * <pre>
 *  * name : for name to access it's iterator variables through {$.for.name.var} see {@link http://wiki.dwoo.org/index.php/IteratorVariables} for details
 *  * from : array to iterate from (which equals 0) or a number as a start value
 *  * to : value to stop iterating at (equals count($array) by default if you set an array in from)
 *  * step : defines the incrementation of the pointer at each iteration
 * </pre>
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
class Dwoo_Plugin_for extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block, Dwoo_IElseable
{
	public static $cnt=0;

	public function init($name, $from, $to=null, $step=1, $skip=0)
	{
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		// get block params and save the current template pointer to use it in the postProcessing method
		$currentBlock =& $compiler->getCurrentBlock();
		$currentBlock['params']['tplPointer'] = $compiler->getPointer();

		return '';
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		$params = $compiler->getCompiledParams($params);
		$tpl = $compiler->getTemplateSource($params['tplPointer']);

		// assigns params
		$from = $params['from'];
		$name = $params['name'];
		$step = $params['step'];
		$to = $params['to'];

 		// evaluates which global variables have to be computed
		$varName = '$dwoo.for.'.trim($name, '"\'').'.';
		$shortVarName = '$.for.'.trim($name, '"\'').'.';
		$usesAny = strpos($tpl, $varName) !== false || strpos($tpl, $shortVarName) !== false;
		$usesFirst = strpos($tpl, $varName.'first') !== false || strpos($tpl, $shortVarName.'first') !== false;
		$usesLast = strpos($tpl, $varName.'last') !== false || strpos($tpl, $shortVarName.'last') !== false;
		$usesIndex = strpos($tpl, $varName.'index') !== false || strpos($tpl, $shortVarName.'index') !== false;
		$usesIteration = $usesFirst || $usesLast || strpos($tpl, $varName.'iteration') !== false || strpos($tpl, $shortVarName.'iteration') !== false;
		$usesShow = strpos($tpl, $varName.'show') !== false || strpos($tpl, $shortVarName.'show') !== false;
		$usesTotal = $usesLast || strpos($tpl, $varName.'total') !== false || strpos($tpl, $shortVarName.'total') !== false;

		if (strpos($name, '$this->scope[') !== false) {
			$usesAny = $usesFirst = $usesLast = $usesIndex = $usesIteration = $usesShow = $usesTotal = true;
		}

		// gets foreach id
		$cnt = self::$cnt++;

		// builds pre processing output for
		$out = Dwoo_Compiler::PHP_OPEN . "\n".'$_for'.$cnt.'_from = '.$from.';'.
										"\n".'$_for'.$cnt.'_to = '.$to.';'.
										"\n".'$_for'.$cnt.'_step = abs('.$step.');'.
										"\n".'if (is_numeric($_for'.$cnt.'_from) && !is_numeric($_for'.$cnt.'_to)) { $this->triggerError(\'For requires the <em>to</em> parameter when using a numerical <em>from</em>\'); }'.
										"\n".'$tmp_shows = $this->isArray($_for'.$cnt.'_from, true) || (is_numeric($_for'.$cnt.'_from) && (abs(($_for'.$cnt.'_from - $_for'.$cnt.'_to)/$_for'.$cnt.'_step) !== 0 || $_for'.$cnt.'_from == $_for'.$cnt.'_to));';
		// adds foreach properties
		if ($usesAny) {
			$out .= "\n".'$this->globals["for"]['.$name.'] = array'."\n(";
			if ($usesIndex) $out .="\n\t".'"index"		=> 0,';
			if ($usesIteration) $out .="\n\t".'"iteration"		=> 1,';
			if ($usesFirst) $out .="\n\t".'"first"		=> null,';
			if ($usesLast) $out .="\n\t".'"last"		=> null,';
			if ($usesShow) $out .="\n\t".'"show"		=> $tmp_shows,';
			if ($usesTotal) $out .="\n\t".'"total"		=> $this->isArray($_for'.$cnt.'_from) ? floor(count($_for'.$cnt.'_from) / $_for'.$cnt.'_step) : (is_numeric($_for'.$cnt.'_from) ? abs(($_for'.$cnt.'_to + 1 - $_for'.$cnt.'_from)/$_for'.$cnt.'_step) : 0),';
			$out.="\n);\n".'$_for'.$cnt.'_glob =& $this->globals["for"]['.$name.'];';
		}
		// checks if for must be looped
		$out .= "\n".'if ($tmp_shows)'."\n{";
		// set from/to to correct values if an array was given
		$out .= "\n\t".'if ($this->isArray($_for'.$cnt.'_from, true)) {
		$_for'.$cnt.'_to = is_numeric($_for'.$cnt.'_to) ? $_for'.$cnt.'_to - $_for'.$cnt.'_step : count($_for'.$cnt.'_from) - 1;
		$_for'.$cnt.'_from = 0;
	}';

		// if input are pure numbers it shouldn't reorder them, if it's variables it gets too messy though so in that case a counter should be used
		$reverse = false;
		$condition = '<=';
		$incrementer = '+';

		if (preg_match('{^(["\']?)([0-9]+)\1$}', $from, $mN1) && preg_match('{^(["\']?)([0-9]+)\1$}', $to, $mN2)) {
			$from = (int) $mN1[2];
			$to = (int) $mN2[2];
			if ($from > $to) {
				$reverse = true;
				$condition = '>=';
				$incrementer = '-';
			}
		}

		// reverse from and to if needed
		if (!$reverse) {
			$out .= "\n\t".'if ($_for'.$cnt.'_from > $_for'.$cnt.'_to) {
				$tmp = $_for'.$cnt.'_from;
				$_for'.$cnt.'_from = $_for'.$cnt.'_to;
				$_for'.$cnt.'_to = $tmp;
			}';
		}

		$out .= '
	for ($this->scope['.$name.'] = $_for'.$cnt.'_from; $this->scope['.$name.'] '.$condition.' $_for'.$cnt.'_to; $this->scope['.$name.'] '.$incrementer.'= $_for'.$cnt.'_step)'."\n\t{";
		// updates properties
		if ($usesIndex) {
			$out .="\n\t\t".'$_for'.$cnt.'_glob["index"] = $this->scope['.$name.'];';
		}
		if ($usesFirst) {
			$out .= "\n\t\t".'$_for'.$cnt.'_glob["first"] = (string) ($_for'.$cnt.'_glob["iteration"] === 1);';
		}
		if ($usesLast) {
			$out .= "\n\t\t".'$_for'.$cnt.'_glob["last"] = (string) ($_for'.$cnt.'_glob["iteration"] === $_for'.$cnt.'_glob["total"]);';
		}
		$out .= "\n/* -- for start output */\n".Dwoo_Compiler::PHP_CLOSE;


		// build post processing output and cache it
		$postOut = Dwoo_Compiler::PHP_OPEN . '/* -- for end output */';
		// update properties
		if ($usesIteration) {
			$postOut .= "\n\t\t".'$_for'.$cnt.'_glob["iteration"]+=1;';
		}
		// end loop
		$postOut .= "\n\t}\n}\n".Dwoo_Compiler::PHP_CLOSE;

		if (isset($params['hasElse'])) {
			$postOut .= $params['hasElse'];
		}

		return $out . $content . $postOut;
	}
}
