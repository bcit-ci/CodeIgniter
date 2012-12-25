<?php

/**
 * Formats any html output (must be valid xml where every tag opened is closed)
 * using a single tab for indenting. 'pre' and other whitespace sensitive
 * tags should not be affected.
 *
 * It is not recommended to use this on every template if you render multiple
 * templates per page, you should only use it once on the main page template so that
 * everything is formatted in one pass.
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
class Dwoo_Filter_html_format extends Dwoo_Filter
{
	/**
	 * tab count to auto-indent the source
	 *
	 * @var int
	 */
	protected static $tabCount = -1;

	/**
	 * stores the additional data (following a tag) of the last call to open/close/singleTag
	 *
	 * @var string
	 */
	protected static $lastCallAdd = '';

	/**
	 * formats the input using the singleTag/closeTag/openTag functions
	 *
	 * It is auto indenting the whole code, excluding <textarea>, <code> and <pre> tags that must be kept intact.
	 * Those tags must however contain only htmlentities-escaped text for everything to work properly.
	 * Inline tags are presented on a single line with their content
	 *
	 * @param Dwoo $dwoo the dwoo instance rendering this
	 * @param string $input the xhtml to format
	 * @return string formatted xhtml
	 */
	public function process($input)
	{
		self::$tabCount = -1;

		// auto indent all but textareas & pre (or we have weird tabs inside)
		$input = preg_replace_callback("#(<[^>]+>)(\s*)([^<]*)#", array('self', 'tagDispatcher'), $input);

		return $input;
	}

	/**
	 * helper function for format()'s preg_replace call
	 *
	 * @param array	$input	array of matches (1=>tag, 2=>whitespace(optional), 3=>additional non-html content)
	 * @return string the indented tag
	 */
	protected static function tagDispatcher($input)
	{
		// textarea, pre, code tags and comments are to be left alone to avoid any non-wanted whitespace inside them so it just outputs them as they were
		if (substr($input[1],0,9) == "<textarea" || substr($input[1],0,4) == "<pre" || substr($input[1],0,5) == "<code" || substr($input[1],0,4) == "<!--" || substr($input[1],0,9) == "<![CDATA[") {
			return $input[1] . $input[3];
		}
		// closing textarea, code and pre tags and self-closed tags (i.e. <br />) are printed as singleTags because we didn't use openTag for the formers and the latter is a single tag
		if (substr($input[1],0,10) == "</textarea" || substr($input[1],0,5) == "</pre" || substr($input[1],0,6) == "</code" || substr($input[1],-2) == "/>") {
			return self::singleTag($input[1],$input[3],$input[2]);
		}
		// it's the closing tag
		if ($input[0][1]=="/"){
			return self::closeTag($input[1],$input[3],$input[2]);
		}
		// opening tag
		return self::openTag($input[1],$input[3],$input[2]);
	}

	/**
	 * returns an open tag and adds a tab into the auto indenting
	 *
	 * @param string $tag content of the tag
	 * @param string $add additional data (anything before the following tag)
	 * @param string $whitespace white space between the tag and the additional data
	 * @return string
	 */
	protected static function openTag($tag,$add,$whitespace)
	{
		$tabs = str_pad('',self::$tabCount++,"\t");

		if (preg_match('#^<(a|label|option|textarea|h1|h2|h3|h4|h5|h6|strong|b|em|i|abbr|acronym|cite|span|sub|sup|u|s|title)(?: [^>]*|)>#', $tag)) {
			// if it's one of those tag it's inline so it does not require a leading line break
			$result = $tag . $whitespace . str_replace("\n","\n".$tabs,$add);
		} elseif (substr($tag,0,9) == '<!DOCTYPE') {
			// it's the doctype declaration so no line break here either
			$result = $tabs . $tag;
		} else {
			// normal block tag
			$result = "\n".$tabs . $tag;

			if (!empty($add)) {
				$result .= "\n".$tabs."\t".str_replace("\n","\n\t".$tabs,$add);
			}
		}

		self::$lastCallAdd = $add;

		return $result;
	}

	/**
	 * returns a closing tag and removes a tab from the auto indenting
	 *
	 * @param string $tag content of the tag
	 * @param string $add additional data (anything before the following tag)
	 * @param string $whitespace white space between the tag and the additional data
	 * @return string
	 */
	protected static function closeTag($tag,$add,$whitespace)
	{
		$tabs = str_pad('',--self::$tabCount,"\t");

		// if it's one of those tag it's inline so it does not require a leading line break
		if (preg_match('#^</(a|label|option|textarea|h1|h2|h3|h4|h5|h6|strong|b|em|i|abbr|acronym|cite|span|sub|sup|u|s|title)>#', $tag)) {
			$result = $tag . $whitespace . str_replace("\n","\n".$tabs,$add);
		} else {
			$result = "\n".$tabs.$tag;

			if (!empty($add)) {
				$result .= "\n".$tabs."\t".str_replace("\n","\n\t".$tabs,$add);
			}
		}

		self::$lastCallAdd = $add;

		return $result;
	}

	/**
	 * returns a single tag with auto indenting
	 *
	 * @param string $tag content of the tag
	 * @param string $add additional data (anything before the following tag)
	 * @return string
	 */
	protected static function singleTag($tag,$add,$whitespace)
	{
		$tabs = str_pad('',self::$tabCount,"\t");

		// if it's img, br it's inline so it does not require a leading line break
		// if it's a closing textarea, code or pre tag, it does not require a leading line break either or it creates whitespace at the end of those blocks
		if (preg_match('#^<(img|br|/textarea|/pre|/code)(?: [^>]*|)>#', $tag)) {
			$result = $tag.$whitespace;

			if (!empty($add)) {
				$result .= str_replace("\n","\n".$tabs,$add);
			}
		} else {
			$result = "\n".$tabs.$tag;

			if (!empty($add)) {
				$result .= "\n".$tabs.str_replace("\n","\n".$tabs,$add);
			}
		}

		self::$lastCallAdd = $add;

		return $result;
	}
}
