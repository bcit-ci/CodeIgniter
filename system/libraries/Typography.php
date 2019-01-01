<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Typography Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/typography.html
 */
class CI_Typography {

	/**
	 * Block level elements that should not be wrapped inside <p> tags
	 *
	 * @var string
	 */
	public $block_elements = 'address|blockquote|div|dl|fieldset|form|h\d|hr|noscript|object|ol|p|pre|script|table|ul';

	/**
	 * Elements that should not have <p> and <br /> tags within them.
	 *
	 * @var string
	 */
	public $skip_elements	= 'p|pre|ol|ul|dl|object|table|h\d';

	/**
	 * Tags we want the parser to completely ignore when splitting the string.
	 *
	 * @var string
	 */
	public $inline_elements = 'a|abbr|acronym|b|bdo|big|br|button|cite|code|del|dfn|em|i|img|ins|input|label|map|kbd|q|samp|select|small|span|strong|sub|sup|textarea|tt|var';

	/**
	 * array of block level elements that require inner content to be within another block level element
	 *
	 * @var array
	 */
	public $inner_block_required = array('blockquote');

	/**
	 * the last block element parsed
	 *
	 * @var string
	 */
	public $last_block_element = '';

	/**
	 * whether or not to protect quotes within { curly braces }
	 *
	 * @var bool
	 */
	public $protect_braced_quotes = FALSE;

	/**
	 * Auto Typography
	 *
	 * This function converts text, making it typographically correct:
	 *	- Converts double spaces into paragraphs.
	 *	- Converts single line breaks into <br /> tags
	 *	- Converts single and double quotes into correctly facing curly quote entities.
	 *	- Converts three dots into ellipsis.
	 *	- Converts double dashes into em-dashes.
	 *  - Converts two spaces into entities
	 *
	 * @param	string
	 * @param	bool	whether to reduce more then two consecutive newlines to two
	 * @return	string
	 */
	public function auto_typography($str, $reduce_linebreaks = FALSE)
	{
		if ($str === '')
		{
			return '';
		}

		// Standardize Newlines to make matching easier
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// Reduce line breaks.  If there are more than two consecutive linebreaks
		// we'll compress them down to a maximum of two since there's no benefit to more.
		if ($reduce_linebreaks === TRUE)
		{
			$str = preg_replace("/\n\n+/", "\n\n", $str);
		}

		// HTML comment tags don't conform to patterns of normal tags, so pull them out separately, only if needed
		$html_comments = array();
		if (strpos($str, '<!--') !== FALSE && preg_match_all('#(<!\-\-.*?\-\->)#s', $str, $matches))
		{
			for ($i = 0, $total = count($matches[0]); $i < $total; $i++)
			{
				$html_comments[] = $matches[0][$i];
				$str = str_replace($matches[0][$i], '{@HC'.$i.'}', $str);
			}
		}

		// match and yank <pre> tags if they exist.  It's cheaper to do this separately since most content will
		// not contain <pre> tags, and it keeps the PCRE patterns below simpler and faster
		if (strpos($str, '<pre') !== FALSE)
		{
			$str = preg_replace_callback('#<pre.*?>.*?</pre>#si', array($this, '_protect_characters'), $str);
		}

		// Convert quotes within tags to temporary markers.
		$str = preg_replace_callback('#<.+?>#si', array($this, '_protect_characters'), $str);

		// Do the same with braces if necessary
		if ($this->protect_braced_quotes === TRUE)
		{
			$str = preg_replace_callback('#\{.+?\}#si', array($this, '_protect_characters'), $str);
		}

		// Convert "ignore" tags to temporary marker.  The parser splits out the string at every tag
		// it encounters.  Certain inline tags, like image tags, links, span tags, etc. will be
		// adversely affected if they are split out so we'll convert the opening bracket < temporarily to: {@TAG}
		$str = preg_replace('#<(/*)('.$this->inline_elements.')([ >])#i', '{@TAG}\\1\\2\\3', $str);

		/* Split the string at every tag. This expression creates an array with this prototype:
		 *
		 *	[array]
		 *	{
		 *		[0] = <opening tag>
		 *		[1] = Content...
		 *		[2] = <closing tag>
		 *		Etc...
		 *	}
		 */
		$chunks = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

		// Build our finalized string.  We cycle through the array, skipping tags, and processing the contained text
		$str = '';
		$process = TRUE;

		for ($i = 0, $c = count($chunks) - 1; $i <= $c; $i++)
		{
			// Are we dealing with a tag? If so, we'll skip the processing for this cycle.
			// Well also set the "process" flag which allows us to skip <pre> tags and a few other things.
			if (preg_match('#<(/*)('.$this->block_elements.').*?>#', $chunks[$i], $match))
			{
				if (preg_match('#'.$this->skip_elements.'#', $match[2]))
				{
					$process = ($match[1] === '/');
				}

				if ($match[1] === '')
				{
					$this->last_block_element = $match[2];
				}

				$str .= $chunks[$i];
				continue;
			}

			if ($process === FALSE)
			{
				$str .= $chunks[$i];
				continue;
			}

			//  Force a newline to make sure end tags get processed by _format_newlines()
			if ($i === $c)
			{
				$chunks[$i] .= "\n";
			}

			//  Convert Newlines into <p> and <br /> tags
			$str .= $this->_format_newlines($chunks[$i]);
		}

		// No opening block level tag? Add it if needed.
		if ( ! preg_match('/^\s*<(?:'.$this->block_elements.')/i', $str))
		{
			$str = preg_replace('/^(.*?)<('.$this->block_elements.')/i', '<p>$1</p><$2', $str);
		}

		// Convert quotes, elipsis, em-dashes, non-breaking spaces, and ampersands
		$str = $this->format_characters($str);

		// restore HTML comments
		for ($i = 0, $total = count($html_comments); $i < $total; $i++)
		{
			// remove surrounding paragraph tags, but only if there's an opening paragraph tag
			// otherwise HTML comments at the ends of paragraphs will have the closing tag removed
			// if '<p>{@HC1}' then replace <p>{@HC1}</p> with the comment, else replace only {@HC1} with the comment
			$str = preg_replace('#(?(?=<p>\{@HC'.$i.'\})<p>\{@HC'.$i.'\}(\s*</p>)|\{@HC'.$i.'\})#s', $html_comments[$i], $str);
		}

		// Final clean up
		$table = array(

						// If the user submitted their own paragraph tags within the text
						// we will retain them instead of using our tags.
						'/(<p[^>*?]>)<p>/'	=> '$1', // <?php BBEdit syntax coloring bug fix

						// Reduce multiple instances of opening/closing paragraph tags to a single one
						'#(</p>)+#'			=> '</p>',
						'/(<p>\W*<p>)+/'	=> '<p>',

						// Clean up stray paragraph tags that appear before block level elements
						'#<p></p><('.$this->block_elements.')#'	=> '<$1',

						// Clean up stray non-breaking spaces preceding block elements
						'#(&nbsp;\s*)+<('.$this->block_elements.')#'	=> '  <$2',

						// Replace the temporary markers we added earlier
						'/\{@TAG\}/'		=> '<',
						'/\{@DQ\}/'			=> '"',
						'/\{@SQ\}/'			=> "'",
						'/\{@DD\}/'			=> '--',
						'/\{@NBS\}/'		=> '  ',

						// An unintended consequence of the _format_newlines function is that
						// some of the newlines get truncated, resulting in <p> tags
						// starting immediately after <block> tags on the same line.
						// This forces a newline after such occurrences, which looks much nicer.
						"/><p>\n/"			=> ">\n<p>",

						// Similarly, there might be cases where a closing </block> will follow
						// a closing </p> tag, so we'll correct it by adding a newline in between
						'#</p></#'			=> "</p>\n</"
						);

		// Do we need to reduce empty lines?
		if ($reduce_linebreaks === TRUE)
		{
			$table['#<p>\n*</p>#'] = '';
		}
		else
		{
			// If we have empty paragraph tags we add a non-breaking space
			// otherwise most browsers won't treat them as true paragraphs
			$table['#<p></p>#'] = '<p>&nbsp;</p>';
		}

		return preg_replace(array_keys($table), $table, $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Format Characters
	 *
	 * This function mainly converts double and single quotes
	 * to curly entities, but it also converts em-dashes,
	 * double spaces, and ampersands
	 *
	 * @param	string
	 * @return	string
	 */
	public function format_characters($str)
	{
		static $table;

		if ( ! isset($table))
		{
			$table = array(
							// nested smart quotes, opening and closing
							// note that rules for grammar (English) allow only for two levels deep
							// and that single quotes are _supposed_ to always be on the outside
							// but we'll accommodate both
							// Note that in all cases, whitespace is the primary determining factor
							// on which direction to curl, with non-word characters like punctuation
							// being a secondary factor only after whitespace is addressed.
							'/\'"(\s|$)/'					=> '&#8217;&#8221;$1',
							'/(^|\s|<p>)\'"/'				=> '$1&#8216;&#8220;',
							'/\'"(\W)/'						=> '&#8217;&#8221;$1',
							'/(\W)\'"/'						=> '$1&#8216;&#8220;',
							'/"\'(\s|$)/'					=> '&#8221;&#8217;$1',
							'/(^|\s|<p>)"\'/'				=> '$1&#8220;&#8216;',
							'/"\'(\W)/'						=> '&#8221;&#8217;$1',
							'/(\W)"\'/'						=> '$1&#8220;&#8216;',

							// single quote smart quotes
							'/\'(\s|$)/'					=> '&#8217;$1',
							'/(^|\s|<p>)\'/'				=> '$1&#8216;',
							'/\'(\W)/'						=> '&#8217;$1',
							'/(\W)\'/'						=> '$1&#8216;',

							// double quote smart quotes
							'/"(\s|$)/'						=> '&#8221;$1',
							'/(^|\s|<p>)"/'					=> '$1&#8220;',
							'/"(\W)/'						=> '&#8221;$1',
							'/(\W)"/'						=> '$1&#8220;',

							// apostrophes
							"/(\w)'(\w)/"					=> '$1&#8217;$2',

							// Em dash and ellipses dots
							'/\s?\-\-\s?/'					=> '&#8212;',
							'/(\w)\.{3}/'					=> '$1&#8230;',

							// double space after sentences
							'/(\W)  /'						=> '$1&nbsp; ',

							// ampersands, if not a character entity
							'/&(?!#?[a-zA-Z0-9]{2,};)/'		=> '&amp;'
						);
		}

		return preg_replace(array_keys($table), $table, $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Format Newlines
	 *
	 * Converts newline characters into either <p> tags or <br />
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _format_newlines($str)
	{
		if ($str === '' OR (strpos($str, "\n") === FALSE && ! in_array($this->last_block_element, $this->inner_block_required)))
		{
			return $str;
		}

		// Convert two consecutive newlines to paragraphs
		$str = str_replace("\n\n", "</p>\n\n<p>", $str);

		// Convert single spaces to <br /> tags
		$str = preg_replace("/([^\n])(\n)([^\n])/", '\\1<br />\\2\\3', $str);

		// Wrap the whole enchilada in enclosing paragraphs
		if ($str !== "\n")
		{
			// We trim off the right-side new line so that the closing </p> tag
			// will be positioned immediately following the string, matching
			// the behavior of the opening <p> tag
			$str =  '<p>'.rtrim($str).'</p>';
		}

		// Remove empty paragraphs if they are on the first line, as this
		// is a potential unintended consequence of the previous code
		return preg_replace('/<p><\/p>(.*)/', '\\1', $str, 1);
	}

	// ------------------------------------------------------------------------

	/**
	 * Protect Characters
	 *
	 * Protects special characters from being formatted later
	 * We don't want quotes converted within tags so we'll temporarily convert them to {@DQ} and {@SQ}
	 * and we don't want double dashes converted to emdash entities, so they are marked with {@DD}
	 * likewise double spaces are converted to {@NBS} to prevent entity conversion
	 *
	 * @param	array
	 * @return	string
	 */
	protected function _protect_characters($match)
	{
		return str_replace(array("'",'"','--','  '), array('{@SQ}', '{@DQ}', '{@DD}', '{@NBS}'), $match[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Convert newlines to HTML line breaks except within PRE tags
	 *
	 * @param	string
	 * @return	string
	 */
	public function nl2br_except_pre($str)
	{
		$newstr = '';
		for ($ex = explode('pre>', $str), $ct = count($ex), $i = 0; $i < $ct; $i++)
		{
			$newstr .= (($i % 2) === 0) ? nl2br($ex[$i]) : $ex[$i];
			if ($ct - 1 !== $i)
			{
				$newstr .= 'pre>';
			}
		}

		return $newstr;
	}

}
