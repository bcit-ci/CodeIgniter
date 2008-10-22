<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Typography Class
 *
 *
 * @access		private
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/
 */
class CI_Typography {

	// Block level elements that should not be wrapped inside <p> tags
	var $block_elements = 'address|blockquote|div|dl|fieldset|form|h\d|hr|noscript|object|ol|p|pre|script|table|ul';
	
	// Elements that should not have <p> and <br /> tags within them.
	var $skip_elements	= 'p|pre|ol|ul|dl|object|table';
	
	// Tags we want the parser to completely ignore when splitting the string.
	var $inline_elements = 'a|abbr|acronym|b|bdo|br|button|cite|code|del|dfn|em|i|img|ins|input|label|map|kbd|samp|select|span|strong|sub|sup|textarea|var';

	// whether or not to protect quotes within { curly braces }
	var $protect_braced_quotes = FALSE;
	
	/**
	 * Nothing to do here...
	 *
	 */
	function CI_Typography()
	{
	}

	/**
	 * Auto Typography
	 *
	 * This function converts text, making it typographically correct:
	 * 	- Converts double spaces into paragraphs.
	 * 	- Converts single line breaks into <br /> tags
	 * 	- Converts single and double quotes into correctly facing curly quote entities.
	 * 	- Converts three dots into ellipsis.
	 * 	- Converts double dashes into em-dashes.
	 *  - Converts two spaces into entities
	 *
	 * @access	public
	 * @param	string
	 * @param	bool	whether to strip javascript event handlers for security
	 * @param	bool	whether to reduce more then two consecutive newlines to two
	 * @return	string
	 */
	function auto_typography($str, $strip_js_event_handlers = TRUE, $reduce_linebreaks = FALSE)
	{
		if ($str == '')
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
		
		 // Do we allow JavaScript event handlers? If not, we strip them from within all tags
		if ($strip_js_event_handlers === TRUE)
		{
			$str = preg_replace("#<([^><]+?)([^a-z_\-]on\w*|xmlns)(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);
		}       

		// Convert quotes within tags to temporary markers. We don't want quotes converted 
		// within tags so we'll temporarily convert them to {@DQ} and {@SQ}
		if (preg_match_all("#\<.+?>#si", $str, $matches))
		{
			for ($i = 0; $i < count($matches['0']); $i++)
			{
				$str = str_replace($matches['0'][$i],
									str_replace(array("'",'"'), array('{@SQ}', '{@DQ}'), $matches['0'][$i]),
									$str);
			}
		}

		if ($this->protect_braced_quotes === TRUE)
		{
			if (preg_match_all("#\{.+?}#si", $str, $matches))
			{
				for ($i = 0; $i < count($matches['0']); $i++)
				{
					$str = str_replace($matches['0'][$i],
										str_replace(array("'",'"'), array('{@SQ}', '{@DQ}'), $matches['0'][$i]),
										$str);
				}
			}			
		}
			
		// Convert "ignore" tags to temporary marker.  The parser splits out the string at every tag 
		// it encounters.  Certain inline tags, like image tags, links, span tags, etc. will be 
		// adversely affected if they are split out so we'll convert the opening bracket < temporarily to: {@TAG}
		$str = preg_replace("#<(/*)(".$this->inline_elements.")([ >])#i", "{@TAG}\\1\\2\\3", $str);

		// Split the string at every tag.  This expression creates an array with this prototype:
		// 
		// 	[array]
		// 	{
		// 		[0] = <opening tag>
		// 		[1] = Content...
		// 		[2] = <closing tag>
		// 		Etc...
		// 	}	
		$chunks = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		
		// Build our finalized string.  We cycle through the array, skipping tags, and processing the contained text	
		$str = '';
		$process = TRUE;
		$paragraph = FALSE;
		foreach ($chunks as $chunk)
		{
			// Are we dealing with a tag? If so, we'll skip the processing for this cycle.
			// Well also set the "process" flag which allows us to skip <pre> tags and a few other things.
			if (preg_match("#<(/*)(".$this->block_elements.").*?\>#", $chunk, $match))
			{
				if (preg_match("#".$this->skip_elements."#", $match[2]))
				{
					$process =  ($match[1] == '/') ? TRUE : FALSE;
				}
				
				$str .= $chunk;
				continue;
			}

			if ($process == FALSE)
			{
				$str .= $chunk;
				continue;
			}
			
			//  Convert Newlines into <p> and <br /> tags
			$str .= $this->_format_newlines($chunk);
		}

		// is the whole of the content inside a block level element?
		if ( ! preg_match("/^<(?:".$this->block_elements.")/i", $str, $match))
		{
			$str = "<p>{$str}</p>";
		}

		// Convert quotes, elipsis, and em-dashes
		$str = $this->format_characters($str);
	
		// Final clean up
		$table = array(
		
						// If the user submitted their own paragraph tags within the text
						// we will retain them instead of using our tags.
						'/(<p.*?>)<p>/'		=> '$1', // <?php BBEdit syntax coloring bug fix
						
						// Reduce multiple instances of opening/closing paragraph tags to a single one
						'#(</p>)+#'			=> '</p>',
						'/(<p><p>)+/'		=> '<p>',
						
						// Clean up stray paragraph tags that appear before block level elements
						'#<p></p><('.$this->block_elements.')#'	=> '<$1',
			
						// Replace the temporary markers we added earlier
						'/\{@TAG\}/'		=> '<',
						'/\{@DQ\}/'			=> '"',
						'/\{@SQ\}/'			=> "'"

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
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function format_characters($str)
	{
		static $table;
		
		if ( ! isset($table))
		{
	        $table = array(					
							// nested smart quotes, opening and closing
							// note that rules for grammar (English) allow only for two levels deep
							// and that single quotes are _supposed_ to always be on the outside
							// but we'll accommodate both
							'/(^|\W|\s)\'"/'				=> '$1&#8216;&#8220;',
							'/\'"(\s|\W|$)/'				=> '&#8217;&#8221;$1',
							'/(^|\W|\s)"\'/'				=> '$1&#8220;&#8216;',
							'/"\'(\s|\W|$)/'				=> '&#8221;&#8217;$1',

							// single quote smart quotes
							'/\'(\s|\W|$)/'					=> '&#8217;$1',
							'/(^|\W|\s)\'/'					=> '$1&#8216;',

							// double quote smart quotes
							'/"(\s|\W|$)/'					=> '&#8221;$1',
							'/(^|\W|\s)"/'					=> '$1&#8220;',

							// apostrophes
							"/(\w)'(\w)/"       	    	=> '$1&#8217;$2',

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
	 * @access	public
	 * @param	string
	 * @return	string
	 */	
	function _format_newlines($str)
	{
		if ($str == '')
		{
			return $str;
		}

		if (strpos($str, "\n") === FALSE)
		{
			return $str;
		}
		
		// Convert two consecutive newlines to paragraphs
		$str = str_replace("\n\n", "</p>\n\n<p>", $str);
		
		// Convert single spaces to <br /> tags
		$str = preg_replace("/([^\n])(\n)([^\n])/", "\\1<br />\\2\\3", $str);
		
		// Wrap the whole enchilada in enclosing paragraphs
		if ($str != "\n")
		{
			$str =  '<p>'.$str.'</p>';
		}

		// Remove empty paragraphs if they are on the first line, as this
		// is a potential unintended consequence of the previous code
		$str = preg_replace("/<p><\/p>(.*)/", "\\1", $str, 1);
		
		return $str;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * Convert newlines to HTML line breaks except within PRE tags
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */		
	function nl2br_except_pre($str)
	{
		$ex = explode("pre>",$str);
		$ct = count($ex);
	
		$newstr = "";
		for ($i = 0; $i < $ct; $i++)
		{
			if (($i % 2) == 0)
			{
				$newstr .= nl2br($ex[$i]);
			}
			else
			{
				$newstr .= $ex[$i];
			}
		
			if ($ct - 1 != $i)
				$newstr .= "pre>";
		}
	
		return $newstr;
	}
	
}
// END Typography Class

/* End of file Typography.php */
/* Location: ./system/libraries/Typography.php */