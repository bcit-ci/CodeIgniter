<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		BayssMekanique
 * @copyright	Copyright (c) 2008 - 2013, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Output Cache Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		BayssMekanique
 * @link		http://codeigniter.com/user_guide/helpers/output_cache_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('output_cache_array'))
{
	/**
	 * Get array of output cache files
	 *
	 * Returns an array of the cache files created by the output class with
	 * options for returning the contents of the file within the array as
	 * well as performing a purge of expired files.
	 *
	 * File type matching portion is very expensive, so $search_type attribute
	 * allows for the file type matching to be skipped for instances where
	 * file type is not needed.
	 *
	 * This function is used by other helpers within this helper file.
	 *
	 * @param	bool
	 * @param	bool
	 * @param	bool
	 * @return	array
	 */
	function output_cache_array($return_content = FALSE, $perform_clean = TRUE, $search_type = TRUE)
	{
		$CI = &get_instance();
		$path = $CI->config->item('cache_path');
		$cache_path = ($path === '') ? APPPATH.'cache/' : $path;
		$mimes = get_mimes();

		if (!is_dir($cache_path) OR !is_really_writable($cache_path))
		{
			log_message('error', 'Unable to write cache file: '.$cache_path);
			return;
		}

		$cache_array = array();
		$files = scandir($cache_path);
		if (!empty($files))
		{
			foreach ($files as $filename)
			{
				$filepath = $cache_path.$filename;
				if (!@file_exists($filepath) OR !$file_object = @fopen($filepath, FOPEN_READ))
				{
					continue;
				}

				flock($file_object, LOCK_SH);

				$file_cache = (filesize($filepath) > 0) ? fread($file_object, filesize($filepath)) : '';

				flock($file_object, LOCK_UN);
				fclose($file_object);

				if (!preg_match('/^(.*)ENDCI--->/', $file_cache, $match))
				{
					continue;
				}

				$cache_header = unserialize($match[1]);
				$file_expires = $cache_header['expire'];

				if ($perform_clean === TRUE)
				{
					if ($_SERVER['REQUEST_TIME'] >= $file_expires && is_really_writable($cache_path))
					{
						@unlink($filepath);
						log_message('debug', 'Cache file has expired. File deleted.');
						continue;
					}
				}

				$file_header = array();
				$file_type = NULL;
				if (!empty($cache_header))
				{
					foreach ($cache_header as $attribute)
					{
						if (isset($attribute[0][0]) && !empty($attribute[0][0]))
						{
							if ($search_type === TRUE && preg_match('/Content\-Type\:\s*([^;]*)?/i', $attribute[0][0], $match))
							{
								if (isset($match[1]) && !empty($match[1]))
								{
									foreach ($mimes as $ext => $mime)
									{
										if (!is_array($mime))
										{
											if ($match[1] === $mime)
											{
												$file_type = $ext;
												break;
											}
										}
										else
										{
											foreach ($mime as $sub_mime)
											{
												if ($match[1] === $sub_mime)
												{
													$file_type = $ext;
													break 2;
												}
											}
										}
									}
								}
							}
							$file_header[] = $attribute[0][0];
						}
					}
				}

				$last_modified = filemtime($filepath);

				$cache_array[$filename] = array(
					'type' => $file_type,
					'header' => $file_header,
					'expires' => $file_expires,
					'last_modified' => $last_modified,
				);

				if ($return_content === TRUE)
				{
					$cache_array[$filename]['content'] = substr($file_cache, strlen($match[0]));
				}
			}
		}
		return $cache_array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('output_cache_hash_uri'))
{
	/**
	 * Get hash of URI used as filename for cache directory
	 *
	 * Returns a hashed string for either the current URI or a passed in
	 * URI string.
	 *
	 * This function is used by other helpers within this helper file.
	 *
	 * @param	string	Optional, current URI used if none is provided
	 * @return	string
	 */
	function output_cache_hash_uri($uri_string = NULL)
	{
		$CI = &get_instance();

		if (empty($uri_string))
		{
			$uri_string = $CI->uri->uri_string();
		}

		$uri_string = ltrim($uri_string, '/');

		$full_uri = $CI->config->item('base_url').$CI->config->item('index_page').$uri_string;

		return md5($full_uri);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('output_cache_delete'))
{
	/**
	 * Deletes cache files based on array of targets.
	 *
	 * Target array must contain either URI's, file types, or URI hashes.
	 * The type of target must be specified in the second paramater.
	 *
	 * Possible target types: uri, type, hash
	 *
	 * @param	mixed	Array of strings or a single string
	 * @param	string
	 * @return	bool
	 */
	function output_cache_delete($targets, $target_type = 'uri')
	{
		if (!is_array($targets))
		{
			$targets = array($targets);
		}

		$success = TRUE;

		$target_type = strtolower($target_type);

		foreach ($targets as $target)
		{
			switch($target_type)
			{
				case 'uri' :
					$target_hash = output_cache_hash_uri($target);
					$success = $success && output_cache_delete_hash($target_hash);
					break;
				case 'type' :
					$success = $success && output_cache_delete_type($target);
					break;
				case 'hash' :
					$success = $success && output_cache_delete_hash($target);
					break;
			}
		}

		return $success;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('output_cache_delete_type'))
{
	/**
	 * Deletes cache files based on a generic type.
	 *
	 * The type string may contain any generic type specified in the MIME
	 * config array. (Generic type refers to the file extension used as
	 * key, not the specific content types contained as values within
	 * the array)
	 *
	 * This function is used by other helpers within this helper file.
	 *
	 * @param	string
	 * @return	bool
	 */
	function output_cache_delete_type($type)
	{
		$cache_array = output_cache_array(FALSE, FALSE, TRUE);

		$success = TRUE;

		if (!empty($cache_array))
		{
			foreach ($cache_array as $key => $cache_object)
			{
				if ($cache_object['type'] === $type)
				{
					$success = $success && output_cache_delete_hash($key);
				}
			}
		}

		return $success;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('output_cache_delete_hash'))
{
	/**
	 * Deletes cache files based on the URI hash.
	 *
	 * The hash string is used to remove the specified cache file from the 
	 * cache directory.
	 *
	 * This function is used by other helpers within this helper file.
	 *
	 * @param	string
	 * @return	bool
	 */
	function output_cache_delete_hash($hash)
	{
		$CI = &get_instance();
		$cache_path = $CI->config->item('cache_path');
		if ($cache_path === '')
		{
			$cache_path = APPPATH.'cache/';
		}

		if (!is_dir($cache_path))
		{
			log_message('error', 'Unable to find cache path: '.$cache_path);
			return FALSE;
		}

		$cache_path .= $hash;
		if (!@unlink($cache_path))
		{
			log_message('error', 'Unable to delete cache file '.$hash);
		}

		return TRUE;
	}
}

/* End of file cache_helper.php */
/* Location: ./application/helpers/cache_helper.php */