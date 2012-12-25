<?php

/**
 * Cycles between several values and returns one of them on each call
 * <pre>
 *  * name : the cycler name, specify if you need to have multiple concurrent cycles running
 *  * values : an array of values or a string of values delimited by $delimiter
 *  * print : if false, the pointer will go to the next one but not print anything
 *  * advance : if false, the pointer will not advance to the next value
 *  * delimiter : the delimiter used to split values if they are provided as a string
 *  * assign : if set, the value is saved in that variable instead of being output
 *  * reset : if true, the pointer is reset to the first value
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
class Dwoo_Plugin_cycle extends Dwoo_Plugin
{
	protected $cycles = array();

	public function process($name = 'default', $values = null, $print = true, $advance = true, $delimiter = ',', $assign = null, $reset = false)
	{
		if ($values !== null) {
			if (is_string($values)) {
				$values = explode($delimiter, $values);
			}

			if (!isset($this->cycles[$name]) || $this->cycles[$name]['values'] !== $values) {
				$this->cycles[$name]['index'] = 0;
			}

			$this->cycles[$name]['values'] = array_values($values);
		} elseif (isset($this->cycles[$name])) {
			$values = $this->cycles[$name]['values'];
		}

		if ($reset) {
			$this->cycles[$name]['index'] = 0;
		}

		if ($print) {
			$out = $values[$this->cycles[$name]['index']];
		} else {
			$out = null;
		}

		if ($advance) {
			if ($this->cycles[$name]['index'] >= count($values)-1) {
				$this->cycles[$name]['index'] = 0;
			} else {
				$this->cycles[$name]['index']++;
			}
		}

		if ($assign === null) {
			return $out;
		}
		$this->dwoo->assignInScope($out, $assign);
	}
}
