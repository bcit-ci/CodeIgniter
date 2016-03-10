<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2016, British Columbia Institute of Technology
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
 * @copyright	Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['imglib_source_image_required'] = 'You must specify a source image in your preferences.';
$lang['imglib_gd_required'] = 'The GD image library is required for this feature.';
$lang['imglib_gd_required_for_props'] = 'Your server must support the GD image library in order to determine the image properties.';
$lang['imglib_unsupported_imagecreate'] = 'Your server does not support the GD function required to process this type of image.';
$lang['imglib_gif_not_supported'] = 'GIF images are often not supported due to licensing restrictions. You may have to use JPG or PNG images instead.';
$lang['imglib_jpg_not_supported'] = 'JPG images are not supported.';
$lang['imglib_png_not_supported'] = 'PNG images are not supported.';
$lang['imglib_jpg_or_png_required'] = 'The image resize protocol specified in your preferences only works with JPEG or PNG image types.';
$lang['imglib_copy_error'] = 'An error was encountered while attempting to replace the file. Please make sure your file directory is writable.';
$lang['imglib_rotate_unsupported'] = 'Image rotation does not appear to be supported by your server.';
$lang['imglib_libpath_invalid'] = 'The path to your image library is not correct. Please set the correct path in your image preferences.';
$lang['imglib_image_process_failed'] = 'Image processing failed. Please verify that your server supports the chosen protocol and that the path to your image library is correct.';
$lang['imglib_rotation_angle_required'] = 'An angle of rotation is required to rotate the image.';
$lang['imglib_invalid_path'] = 'The path to the image is not correct.';
$lang['imglib_copy_failed'] = 'The image copy routine failed.';
$lang['imglib_missing_font'] = 'Unable to find a font to use.';
$lang['imglib_save_failed'] = 'Unable to save the image. Please make sure the image and file directory are writable.';
