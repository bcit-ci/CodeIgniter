<?php

/**
 * Similar to the php foreach block, loops over an array
 *
 * Note that if you don't provide the item parameter, the key will act as item
 * <pre>
 *  * from : the array that you want to iterate over
 *  * key : variable name for the key (or for the item if item is not defined)
 *  * item : variable name for each item
 *  * name : foreach name to access it's iterator variables through {$.foreach.name.var} see {@link http://wiki.dwoo.org/index.php/IteratorVariables} for details
 * </pre>
 * Example :
 *
 * <code>
 * {foreach $array val}
 *   {$val.something}
 * {/foreach}
 * </code>
 *
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
class Dwoo_Plugin_foreach extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block, Dwoo_IElseable
{
	public static $cnt=0;

	public function init($from, $key=null, $item=null, $name='default', $implode=null)
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
		$src = $params['from'];

		if ($params['item'] !== 'null') {
			if ($params['key'] !== 'null') {
				$key = $params['key'];
			}
			$val = $params['item'];
		} elseif ($params['key'] !== 'null') {
			$val = $params['key'];
		} else {
			throw new Dwoo_Compilation_Exception($compiler, 'Foreach <em>item</em> parameter missing');
		}
		$name = $params['name'];

		if (substr($val, 0, 1) !== '"' && substr($val, 0, 1) !== '\'') {
			throw new Dwoo_Compilation_Exception($compiler, 'Foreach <em>item</em> parameter must be of type string');
		}
		if (isset($key) && substr($val, 0, 1) !== '"' && substr($val, 0, 1) !== '\'') {
			throw new Dwoo_Compilation_Exception($compiler, 'Foreach <em>key</em> parameter must be of type string');
		}

		// evaluates which global variables have to be computed
		$varName = '$dwoo.foreach.'.trim($name, '"\'').'.';
		$shortVarName = '$.foreach.'.trim($name, '"\'').'.';
		$usesAny = strpos($tpl, $varName) !== false || strpos($tpl, $shortVarName) !== false;
		$usesFirst = strpos($tpl, $varName.'first') !== false || strpos($tpl, $shortVarName.'first') !== false;
		$usesLast = strpos($tpl, $varName.'last') !== false || strpos($tpl, $shortVarName.'last') !== false;
		$usesIndex = $usesFirst || strpos($tpl, $varName.'index') !== false || strpos($tpl, $shortVarName.'index') !== false;
		$usesIteration = $usesLast || strpos($tpl, $varName.'iteration') !== false || strpos($tpl, $shortVarName.'iteration') !== false;
		$usesShow = strpos($tpl, $varName.'show') !== false || strpos($tpl, $shortVarName.'show') !== false;
		$usesTotal = $usesLast || strpos($tpl, $varName.'total') !== false || strpos($tpl, $shortVarName.'total') !== false;

		if (strpos($name, '$this->scope[') !== false) {
			$usesAny = $usesFirst = $usesLast = $usesIndex = $usesIteration = $usesShow = $usesTotal = true;
		}

		// override globals vars if implode is used
		if ($params['implode'] !== 'null') {
			$implode = $params['implode'];
			$usesAny = true;
			$usesLast = true;
			$usesIteration = true;
			$usesTotal = true;
		}

		// gets foreach id
		$cnt = self::$cnt++;

		// build pre content output
		$pre = Dwoo_Compiler::PHP_OPEN . "\n".'$_fh'.$cnt.'_data = '.$src.';';
		// adds foreach properties
		if ($usesAny) {
			$pre .= "\n".'$this->globals["foreach"]['.$name.'] = array'."\n(";
			if ($usesIndex) $pre .="\n\t".'"index"		=> 0,';
			if ($usesIteration) $pre .="\n\t".'"iteration"		=> 1,';
			if ($usesFirst) $pre .="\n\t".'"first"		=> null,';
			if ($usesLast) $pre .="\n\t".'"last"		=> null,';
			if ($usesShow) $pre .="\n\t".'"show"		=> $this->isArray($_fh'.$cnt.'_data, true),';
			if ($usesTotal) $pre .="\n\t".'"total"		=> $this->isArray($_fh'.$cnt.'_data) ? count($_fh'.$cnt.'_data) : 0,';
			$pre.="\n);\n".'$_fh'.$cnt.'_glob =& $this->globals["foreach"]['.$name.'];';
		}
		// checks if foreach must be looped
		$pre .= "\n".'if ($this->isArray($_fh'.$cnt.'_data'.(isset($params['hasElse']) ? ', true' : '').') === true)'."\n{";
		// iterates over keys
		$pre .= "\n\t".'foreach ($_fh'.$cnt.'_data as '.(isset($key)?'$this->scope['.$key.']=>':'').'$this->scope['.$val.'])'."\n\t{";
		// updates properties
		if ($usesFirst) {
			$pre .= "\n\t\t".'$_fh'.$cnt.'_glob["first"] = (string) ($_fh'.$cnt.'_glob["index"] === 0);';
		}
		if ($usesLast) {
			$pre .= "\n\t\t".'$_fh'.$cnt.'_glob["last"] = (string) ($_fh'.$cnt.'_glob["iteration"] === $_fh'.$cnt.'_glob["total"]);';
		}
		$pre .= "\n/* -- foreach start output */\n".Dwoo_Compiler::PHP_CLOSE;

		// build post content output
		$post = Dwoo_Compiler::PHP_OPEN . "\n";

		if (isset($implode)) {
			$post .= '/* -- implode */'."\n".'if (!$_fh'.$cnt.'_glob["last"]) {'.
				"\n\t".'echo '.$implode.";\n}\n";
		}
		$post .= '/* -- foreach end output */';
		// update properties
		if ($usesIndex) {
			$post.="\n\t\t".'$_fh'.$cnt.'_glob["index"]+=1;';
		}
		if ($usesIteration) {
			$post.="\n\t\t".'$_fh'.$cnt.'_glob["iteration"]+=1;';
		}
		// end loop
		$post .= "\n\t}\n}".Dwoo_Compiler::PHP_CLOSE;
		if (isset($params['hasElse'])) {
			$post .= $params['hasElse'];
		}

		return $pre . $content . $post;
	}
}
