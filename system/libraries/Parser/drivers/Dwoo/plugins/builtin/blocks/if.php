<?php

/**
 * Conditional block, the syntax is very similar to the php one, allowing () || && and
 * other php operators. Additional operators and their equivalent php syntax are as follow :
 *
 * eq -> ==
 * neq or ne -> !=
 * gte or ge -> >=
 * lte or le -> <=
 * gt -> >
 * lt -> <
 * mod -> %
 * not -> !
 * X is [not] div by Y -> (X % Y) == 0
 * X is [not] even [by Y] -> (X % 2) == 0 or ((X/Y) % 2) == 0
 * X is [not] odd [by Y] -> (X % 2) != 0 or ((X/Y) % 2) != 0
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
class Dwoo_Plugin_if extends Dwoo_Block_Plugin implements Dwoo_ICompilable_Block, Dwoo_IElseable
{
	public function init(array $rest)
	{
	}

	public static function replaceKeywords(array $params, Dwoo_Compiler $compiler)
	{
		$p = array();

		reset($params);
		while (list($k,$v) = each($params)) {
			$v = (string) $v;
			if(substr($v, 0, 1) === '"' || substr($v, 0, 1) === '\'') {
				$vmod = strtolower(substr($v, 1, -1));
			} else {
				$vmod = strtolower($v);
			}
			switch($vmod) {

			case 'and':
				$p[] = '&&';
				break;
			case 'or':
				$p[] = '||';
				break;
			case '==':
			case 'eq':
				$p[] = '==';
				break;
			case '<>':
			case '!=':
			case 'ne':
			case 'neq':
				$p[] = '!=';
				break;
			case '>=':
			case 'gte':
			case 'ge':
				$p[] = '>=';
				break;
			case '<=':
			case 'lte':
			case 'le':
				$p[] = '<=';
				break;
			case '>':
			case 'gt':
				$p[] = '>';
				break;
			case '<':
			case 'lt':
				$p[] = '<';
				break;
			case '===':
				$p[] = '===';
				break;
			case '!==':
				$p[] = '!==';
				break;
			case 'is':
				if (isset($params[$k+1]) && strtolower(trim($params[$k+1], '"\'')) === 'not') {
					$negate = true;
					next($params);
				} else {
					$negate = false;
				}
				$ptr = 1+(int)$negate;
				if (!isset($params[$k+$ptr])) {
					$params[$k+$ptr] = '';
				} else {
					$params[$k+$ptr] = trim($params[$k+$ptr], '"\'');
				}
				switch($params[$k+$ptr]) {

				case 'div':
					if (isset($params[$k+$ptr+1]) && strtolower(trim($params[$k+$ptr+1], '"\'')) === 'by') {
						$p[] = ' % '.$params[$k+$ptr+2].' '.($negate?'!':'=').'== 0';
						next($params);
						next($params);
						next($params);
					} else {
						throw new Dwoo_Compilation_Exception($compiler, 'If : Syntax error : syntax should be "if $a is [not] div by $b", found '.$params[$k-1].' is '.($negate?'not ':'').'div '.$params[$k+$ptr+1].' '.$params[$k+$ptr+2]);
					}
					break;
				case 'even':
					$a = array_pop($p);
					if (isset($params[$k+$ptr+1]) && strtolower(trim($params[$k+$ptr+1], '"\'')) === 'by') {
						$b = $params[$k+$ptr+2];
						$p[] = '('.$a .' / '.$b.') % 2 '.($negate?'!':'=').'== 0';
						next($params);
						next($params);
					} else {
						$p[] = $a.' % 2 '.($negate?'!':'=').'== 0';
					}
					next($params);
					break;
				case 'odd':
					$a = array_pop($p);
					if (isset($params[$k+$ptr+1]) && strtolower(trim($params[$k+$ptr+1], '"\'')) === 'by') {
						$b = $params[$k+$ptr+2];
						$p[] = '('.$a .' / '.$b.') % 2 '.($negate?'=':'!').'== 0';
						next($params);
						next($params);
					} else {
						$p[] = $a.' % 2 '.($negate?'=':'!').'== 0';
					}
					next($params);
					break;
				default:
					throw new Dwoo_Compilation_Exception($compiler, 'If : Syntax error : syntax should be "if $a is [not] (div|even|odd) [by $b]", found '.$params[$k-1].' is '.$params[$k+$ptr+1]);

				}
				break;
			case '%':
			case 'mod':
				$p[] = '%';
				break;
			case '!':
			case 'not':
				$p[] = '!';
				break;
			default:
				$p[] = $v;

			}
		}

		return $p;
	}

	public static function preProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $type)
	{
		return '';
	}

	public static function postProcessing(Dwoo_Compiler $compiler, array $params, $prepend, $append, $content)
	{
		$params = $compiler->getCompiledParams($params);

		$pre = Dwoo_Compiler::PHP_OPEN.'if ('.implode(' ', self::replaceKeywords($params['*'], $compiler)).") {\n".Dwoo_Compiler::PHP_CLOSE;

		$post = Dwoo_Compiler::PHP_OPEN."\n}".Dwoo_Compiler::PHP_CLOSE;

		if (isset($params['hasElse'])) {
			$post .= $params['hasElse'];
		}

		return $pre . $content . $post;
	}
}
