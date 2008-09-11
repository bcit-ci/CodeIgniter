<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
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
	var $block_elements = 'div|blockquote|pre|code|h\d|script|ol|ul';
	
	// Elements that should not have <p> and <br /> tags within them.
	var $skip_elements	= 'pre|ol|ul';
	
	// Tags we want the parser to completely ignore when splitting the string.
	var $ignore_elements = 'a|b|i|em|strong|span|img|li';	

	// Whether to allow Javascript event handlers to be sumitted inside tags
	var $allow_js_event_handlers = FALSE;

	/**
	 * Main Processing Function
	 *
	 */
	function convert($str)
	{
		if ($str == '')
		{
			return '';
		}
		
		$str = ' '.$str.' ';
		
		// Standardize Newlines to make matching easier
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);			
		}
		
		/*
		 * Reduce line breaks
		 *
		 * If there are more than two consecutive line
		 * breaks we'll compress them down to a maximum
		 * of two since there's no benefit to more.
		 *
		 */
		$str = preg_replace("/\n\n+/", "\n\n", $str);

		/*
		 * Do we allow JavaScript event handlers?
		 *
		 * If not, we strip them from within all tags
		 */
		if ($this->allow_js_event_handlers == FALSE)
		{
			$str = preg_replace("#<([^><]+?)([^a-z_\-]on\w*|xmlns)(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);
 		}       

		/*
		 * Convert quotes within tags to temporary marker
		 *
		 * We don't want quotes converted within
		 * tags so we'll temporarily convert them to
		 * {@DQ} and {@SQ}
		 *
		 */			
		if (preg_match_all("#\<.+?>#si", $str, $matches))
		{
			for ($i = 0; $i < count($matches['0']); $i++)
			{
				$str = str_replace($matches['0'][$i],
									str_replace(array("'",'"'), array('{@SQ}', '{@DQ}'), $matches['0'][$i]),
									$str);
			}
		}

        /*
		 * Add closing/opening paragraph tags before/after "block" elements
		 *
		 * Since block elements (like <blockquotes>, <pre>, etc.) do not get
		 * wrapped in paragraph tags we will add a closing </p> tag just before
		 * each block element starts and an opening <p> tag right after the block element
		 * ends.  Later on we'll do some further clean up.
		 *
		 */
		$str = preg_replace("#(<)(".$this->block_elements.")(.*?>)#", "</p>\\1\\2\\3", $str);
		$str = preg_replace("#(</)(".$this->block_elements.")(.*?>)#", "\\1\\2\\3<p>", $str);
	
		/*
		 * Convert "ignore" tags to temporary marker
		 *
		 * The parser splits out the string at every tag
		 * it encounters.  Certain inline tags, like image
		 * tags, links, span tags, etc. will be adversely
		 * affected if they are split out so we'll convert
		 * the opening < temporarily to: {@TAG}
		 *
		 */		
		$str = preg_replace("#<(/*)(".$this->ignore_elements.")#i", "{@TAG}\\1\\2", $str);	
		
		/*
		 * Split the string at every tag
		 *
		 * This creates an array with this prototype:
		 *
		 *	[array]
		 *	{
		 *		[0] = <opening tag>
		 *		[1] = Content contained between the tags
		 *		[2] = <closing tag>
		 *		Etc...
		 *	}
		 *
		 */			
		$chunks = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		
		/*
		 * Build our finalized string
		 *
		 * We'll cycle through the array, skipping tags,
		 * and processing the contained text
		 *
		 */			
		$str = '';
		$process = TRUE;
		foreach ($chunks as $chunk)
		{
			/*
			 * Are we dealing with a tag?
			 *
			 * If so, we'll skip the processing for this cycle.
			 * Well also set the "process" flag which allows us
			 * to skip <pre> tags and a few other things.
			 *
			 */
			if (preg_match("#<(/*)(".$this->block_elements.").*?\>#", $chunk, $match))
			{
				if (preg_match("#".$this->skip_elements."#", $match['2']))
				{
					$process =  ($match['1'] == '/') ? TRUE : FALSE;		
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
			$str .= $this->format_newlines($chunk);
		}

		// FINAL CLEAN UP
		// IMPORTANT:  DO NOT ALTER THE ORDER OF THE ITEMS BELOW!
		
		/*
		 * Clean up paragraph tags before/after "block" elements
		 *
		 * Earlier we added <p></p> tags before/after block level elements.
		 * Then, we added paragraph tags around double line breaks.  This
		 * potentially created incorrectly formatted paragraphs so we'll
		 * clean it up here.
		 *
		 */
		$str = preg_replace("#<p>({@TAG}.*?)(".$this->block_elements.")(.*?>)#", "\\1\\2\\3", $str);
		$str = preg_replace("#({@TAG}/.*?)(".$this->block_elements.")(.*?>)</p>#", "\\1\\2\\3", $str);

		// Convert Quotes and other characters
		$str = $this->format_characters($str);
		
		// Fix an artifact that happens during the paragraph replacement
		$str = preg_replace('#(<p>\n*</p>)#', '', $str);

		// If the user submitted their own paragraph tags with class data
		// in them we will retain them instead of using our tags.
		$str = preg_replace('#(<p.*?>)<p>#', "\\1", $str);  // <?php BBEdit syntax coloring fix

		// Final clean up
		$str = str_replace(
							array(
									'</p></p>',
									'</p><p>',
									'<p> ',
									' </p>',
									'{@TAG}',
									'{@DQ}',
									'{@SQ}',
									'<p></p>'
								),
							array(
									'</p>',
									'<p>',
									'<p>',
									'</p>',
									'<',
									'"',
									"'",
									''
								),
							$str
						);
		
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Format Characters
	 *
	 * This function mainly converts double and single quotes
	 * to curly entities, but it also converts em-dashes,
	 * double spaces, and ampersands
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
							'/\w\.{3}/'						=> '&#8230;',

							// double space after sentences
							'/(\W)\s{2}/'					=> '$1&nbsp; ',

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
	 */	
	function format_newlines($str)
	{
		if ($str == '')
		{
			return $str;
		}

		if (strpos($str, "\n") === FALSE)
		{
			return '<p>'.$str.'</p>';
		}
			
		$str = str_replace("\n\n", "</p>\n\n<p>", $str);
		$str = preg_replace("/([^\n])(\n)([^\n])/", "\\1<br />\\2\\3", $str);
		
		return '<p>'.$str.'</p>';
	}
	
	// --------------------------------------------------------------------

	/**
	 * Allow JavaScript Event Handlers?
	 *
	 * For security reasons, by default we disallow JS event handlers
	 *
	 */	
	function allow_js_event_handlers($val = FALSE)
	{
		$this->allow_js_event_handlers = ($val === FALSE) ? FALSE : TRUE;
	}
	
	
}
// END Typography Class

/* End of file Typography.php */
/* Location: ./system/libraries/Typography.php */