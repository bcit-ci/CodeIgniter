<?php

/**
 * Formats a string to the given format, you can wrap lines at a certain
 * length and indent them
 * <pre>
 *  * wrap : maximum line length
 *  * wrap_char : the character(s) to use to break the line
 *  * wrap_cut : if true, the words that are longer than $wrap are cut instead of overflowing
 *  * indent : amount of $indent_char to insert before every line
 *  * indent_char : character(s) to insert before every line
 *  * indent_first : amount of additional $indent_char to insert before the first line of each paragraphs
 *  * style : some predefined formatting styles that set up every required variables, can be "email" or "html"
 *  * assign : if set, the formatted text is assigned to that variable instead of being output
 * </pre>
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
class Dwoo_Plugin_textformat extends Dwoo_Block_Plugin
{
	protected $wrap;
	protected $wrapChar;
	protected $wrapCut;
	protected $indent;
	protected $indChar;
	protected $indFirst;
	protected $assign;

	public function init($wrap=80, $wrap_char="\r\n", $wrap_cut=false, $indent=0, $indent_char=" ", $indent_first=0, $style="", $assign="")
	{
		if ($indent_char === 'tab') {
			$indent_char = "\t";
		}

		switch($style) {

		case 'email':
			$wrap = 72;
			$indent_first = 0;
			break;
		case 'html':
			$wrap_char = '<br />';
			$indent_char = $indent_char == "\t" ? '&nbsp;&nbsp;&nbsp;&nbsp;':'&nbsp;';
			break;

		}

		$this->wrap = (int) $wrap;
		$this->wrapChar = (string) $wrap_char;
		$this->wrapCut = (bool) $wrap_cut;
		$this->indent = (int) $indent;
		$this->indChar = (string) $indent_char;
		$this->indFirst = (int) $indent_first + $this->indent;
		$this->assign = (string) $assign;
	}

	public function process()
	{
		// gets paragraphs
		$pgs = explode("\n", str_replace(array("\r\n", "\r"), "\n", $this->buffer));

		while (list($i,) = each($pgs)) {
			if (empty($pgs[$i])) {
				continue;
			}

			// removes line breaks and extensive white space
			$pgs[$i] = preg_replace(array('#\s+#', '#^\s*(.+?)\s*$#m'), array(' ', '$1'), str_replace("\n", '', $pgs[$i]));

			// wordwraps + indents lines
			$pgs[$i] = str_repeat($this->indChar, $this->indFirst) .
			   		wordwrap(
							$pgs[$i],
							max($this->wrap - $this->indent, 1),
							$this->wrapChar . str_repeat($this->indChar, $this->indent),
							$this->wrapCut
					);
		}

		if ($this->assign !== '') {
			$this->dwoo->assignInScope(implode($this->wrapChar . $this->wrapChar, $pgs), $this->assign);
		} else {
			return implode($this->wrapChar . $this->wrapChar, $pgs);
		}
	}
}
