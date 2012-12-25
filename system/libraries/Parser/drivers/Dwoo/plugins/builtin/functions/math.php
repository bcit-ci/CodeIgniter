<?php

/**
 * Computes a mathematical equation
 * <pre>
 *  * equation : the equation to compute, it can include normal variables with $foo or special math variables without the dollar sign
 *  * format : output format, see {@link http://php.net/sprintf} for details
 *  * assign : if set, the output is assigned into the given variable name instead of being output
 *  * rest : all math specific variables that you use must be defined, see the example
 * </pre>
 * Example :
 *
 * <code>
 * {$c=2}
 * {math "(a+b)*$c/4" a=3 b=5}
 *
 * output is : 4 ( = (3+5)*2/4)
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
function Dwoo_Plugin_math_compile(Dwoo_Compiler $compiler, $equation, $format='', $assign='', array $rest=array())
{
	/**
	 * Holds the allowed function, characters, operators and constants
	 */
	$allowed = array
	(
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
		'+', '-', '/', '*', '.', ' ', '<<', '>>', '%', '&', '^', '|', '~',
		'abs(', 'ceil(', 'floor(', 'exp(', 'log10(',
		'cos(', 'sin(', 'sqrt(', 'tan(',
		'M_PI', 'INF', 'M_E',
	);

	/**
	 * Holds the functions that can accept multiple arguments
	 */
	$funcs = array
	(
 		'round(', 'log(', 'pow(',
		'max(', 'min(', 'rand(',
 	);

	$equation = $equationSrc = str_ireplace(array('pi', 'M_PI()', 'inf', ' e '), array('M_PI', 'M_PI', 'INF', ' M_E '), $equation);

	$delim = $equation[0];
	$open = $delim.'.';
	$close = '.'.$delim;
	$equation = substr($equation, 1, -1);
	$out = '';
	$ptr = 1;
	$allowcomma = 0;
	while (strlen($equation) > 0) {
		$substr = substr($equation, 0, $ptr);
		if (array_search($substr, $allowed) !== false) {
			// allowed string
			$out.=$substr;
			$equation = substr($equation, $ptr);
			$ptr = 0;
		} elseif (array_search($substr, $funcs) !== false) {
			// allowed func
			$out.=$substr;
			$equation = substr($equation, $ptr);
			$ptr = 0;
			$allowcomma++;
			if ($allowcomma === 1) {
				$allowed[] = ',';
			}
		} elseif (isset($rest[$substr])) {
			// variable
			$out.=$rest[$substr];
			$equation = substr($equation, $ptr);
			$ptr = 0;
		} elseif ($substr === $open) {
			// pre-replaced variable
			preg_match('#.*\((?:[^()]*?|(?R))\)'.str_replace('.', '\\.', $close).'#', substr($equation, 2), $m);
			if (empty($m)) {
				preg_match('#.*?'.str_replace('.', '\\.', $close).'#', substr($equation, 2), $m);
			}
			$out.=substr($m[0], 0, -2);
			$equation = substr($equation, strlen($m[0])+2);
			$ptr = 0;
		} elseif ($substr==='(') {
			// opening parenthesis
			if ($allowcomma>0) {
				$allowcomma++;
			}

			$out.=$substr;
			$equation = substr($equation, $ptr);
			$ptr = 0;
		} elseif ($substr===')') {
			// closing parenthesis
			if ($allowcomma>0) {
				$allowcomma--;
				if ($allowcomma===0) {
					array_pop($allowed);
				}
			}

			$out.=$substr;
			$equation = substr($equation, $ptr);
			$ptr = 0;
		} elseif ($ptr >= strlen($equation)) {
			// parse error if we've consumed the entire equation without finding anything valid
			throw new Dwoo_Compilation_Exception($compiler, 'Math : Syntax error or variable undefined in equation '.$equationSrc.' at '.$substr);
			return;
		} else {
			// nothing special, advance
			$ptr++;
		}
	}
	if ($format !== '\'\'') {
		$out = 'sprintf('.$format.', '.$out.')';
	}
	if ($assign !== '\'\'') {
		return '($this->assignInScope('.$out.', '.$assign.'))';
	}
	return '('.$out.')';
}
