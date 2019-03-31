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
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * FTP Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/ftp.html
 */
class CI_FTP
{

    /**
     * FTP Server hostname
     *
     * @var	string
     */
    public $hostname = '';

    /**
     * FTP Username
     *
     * @var	string
     */
    public $username = '';

    /**
     * FTP Password
     *
     * @var	string
     */
    public $password = '';

    /**
     * FTP Server port
     *
     * @var	int
     */
    public $port = 21;

    /**
     * Passive mode flag
     *
     * @var	bool
     */
    public $passive = true;

    /**
     * Debug flag
     *
     * Specifies whether to display error messages.
     *
     * @var	bool
     */
    public $debug = false;

    // --------------------------------------------------------------------

    /**
     * Connection ID
     *
     * @var	resource
     */
    protected $conn_id;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param	array	$config
     * @return	void
     */
    public function __construct($config = array())
    {
        empty($config) or $this->initialize($config);
        log_message('info', 'FTP Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * Initialize preferences
     *
     * @param	array	$config
     * @return	void
     */
    public function initialize($config = array())
    {
        foreach ($config as $key => $val) {
            if (isset($this->$key)) {
                $this->$key = $val;
            }
        }

        // Prep the hostname
        $this->hostname = preg_replace('|.+?://|', '', $this->hostname);
    }

    // --------------------------------------------------------------------

    /**
     * FTP Connect
     *
     * @param	array	 $config	Connection values
     * @return	bool
     */
    public function connect($config = array())
    {
        if (count($config) > 0) {
            $this->initialize($config);
        }

        if (false === ($this->conn_id = @ftp_connect($this->hostname, $this->port))) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_connect');
            }

            return false;
        }

        if (! $this->_login()) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_login');
            }

            return false;
        }

        // Set passive mode if needed
        if ($this->passive === true) {
            ftp_pasv($this->conn_id, true);
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * FTP Login
     *
     * @return	bool
     */
    protected function _login()
    {
        return @ftp_login($this->conn_id, $this->username, $this->password);
    }

    // --------------------------------------------------------------------

    /**
     * Validates the connection ID
     *
     * @return	bool
     */
    protected function _is_conn()
    {
        if (! is_resource($this->conn_id)) {
            if ($this->debug === true) {
                $this->_error('ftp_no_connection');
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Change directory
     *
     * The second parameter lets us momentarily turn off debugging so that
     * this function can be used to test for the existence of a folder
     * without throwing an error. There's no FTP equivalent to is_dir()
     * so we do it by trying to change to a particular directory.
     * Internally, this parameter is only used by the "mirror" function below.
     *
     * @param	string	$path
     * @param	bool	$suppress_debug
     * @return	bool
     */
    public function changedir($path, $suppress_debug = false)
    {
        if (! $this->_is_conn()) {
            return false;
        }

        $result = @ftp_chdir($this->conn_id, $path);

        if ($result === false) {
            if ($this->debug === true && $suppress_debug === false) {
                $this->_error('ftp_unable_to_changedir');
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Create a directory
     *
     * @param	string	$path
     * @param	int	$permissions
     * @return	bool
     */
    public function mkdir($path, $permissions = null)
    {
        if ($path === '' or ! $this->_is_conn()) {
            return false;
        }

        $result = @ftp_mkdir($this->conn_id, $path);

        if ($result === false) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_mkdir');
            }

            return false;
        }

        // Set file permissions if needed
        if ($permissions !== null) {
            $this->chmod($path, (int) $permissions);
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Upload a file to the server
     *
     * @param	string	$locpath
     * @param	string	$rempath
     * @param	string	$mode
     * @param	int	$permissions
     * @return	bool
     */
    public function upload($locpath, $rempath, $mode = 'auto', $permissions = null)
    {
        if (! $this->_is_conn()) {
            return false;
        }

        if (! file_exists($locpath)) {
            $this->_error('ftp_no_source_file');
            return false;
        }

        // Set the mode if not specified
        if ($mode === 'auto') {
            // Get the file extension so we can set the upload type
            $ext = $this->_getext($locpath);
            $mode = $this->_settype($ext);
        }

        $mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

        $result = @ftp_put($this->conn_id, $rempath, $locpath, $mode);

        if ($result === false) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_upload');
            }

            return false;
        }

        // Set file permissions if needed
        if ($permissions !== null) {
            $this->chmod($rempath, (int) $permissions);
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Download a file from a remote server to the local server
     *
     * @param	string	$rempath
     * @param	string	$locpath
     * @param	string	$mode
     * @return	bool
     */
    public function download($rempath, $locpath, $mode = 'auto')
    {
        if (! $this->_is_conn()) {
            return false;
        }

        // Set the mode if not specified
        if ($mode === 'auto') {
            // Get the file extension so we can set the upload type
            $ext = $this->_getext($rempath);
            $mode = $this->_settype($ext);
        }

        $mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

        $result = @ftp_get($this->conn_id, $locpath, $rempath, $mode);

        if ($result === false) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_download');
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Rename (or move) a file
     *
     * @param	string	$old_file
     * @param	string	$new_file
     * @param	bool	$move
     * @return	bool
     */
    public function rename($old_file, $new_file, $move = false)
    {
        if (! $this->_is_conn()) {
            return false;
        }

        $result = @ftp_rename($this->conn_id, $old_file, $new_file);

        if ($result === false) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_'.($move === false ? 'rename' : 'move'));
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Move a file
     *
     * @param	string	$old_file
     * @param	string	$new_file
     * @return	bool
     */
    public function move($old_file, $new_file)
    {
        return $this->rename($old_file, $new_file, true);
    }

    // --------------------------------------------------------------------

    /**
     * Rename (or move) a file
     *
     * @param	string	$filepath
     * @return	bool
     */
    public function delete_file($filepath)
    {
        if (! $this->_is_conn()) {
            return false;
        }

        $result = @ftp_delete($this->conn_id, $filepath);

        if ($result === false) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_delete');
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Delete a folder and recursively delete everything (including sub-folders)
     * contained within it.
     *
     * @param	string	$filepath
     * @return	bool
     */
    public function delete_dir($filepath)
    {
        if (! $this->_is_conn()) {
            return false;
        }

        // Add a trailing slash to the file path if needed
        $filepath = preg_replace('/(.+?)\/*$/', '\\1/', $filepath);

        $list = $this->list_files($filepath);
        if (! empty($list)) {
            for ($i = 0, $c = count($list); $i < $c; $i++) {
                // If we can't delete the item it's probably a directory,
                // so we'll recursively call delete_dir()
                if (! preg_match('#/\.\.?$#', $list[$i]) && ! @ftp_delete($this->conn_id, $list[$i])) {
                    $this->delete_dir($filepath.$list[$i]);
                }
            }
        }

        if (@ftp_rmdir($this->conn_id, $filepath) === false) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_delete');
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Set file permissions
     *
     * @param	string	$path	File path
     * @param	int	$perm	Permissions
     * @return	bool
     */
    public function chmod($path, $perm)
    {
        if (! $this->_is_conn()) {
            return false;
        }

        if (@ftp_chmod($this->conn_id, $perm, $path) === false) {
            if ($this->debug === true) {
                $this->_error('ftp_unable_to_chmod');
            }

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * FTP List files in the specified directory
     *
     * @param	string	$path
     * @return	array
     */
    public function list_files($path = '.')
    {
        return $this->_is_conn()
            ? ftp_nlist($this->conn_id, $path)
            : false;
    }

    // ------------------------------------------------------------------------

    /**
     * Read a directory and recreate it remotely
     *
     * This function recursively reads a folder and everything it contains
     * (including sub-folders) and creates a mirror via FTP based on it.
     * Whatever the directory structure of the original file path will be
     * recreated on the server.
     *
     * @param	string	$locpath	Path to source with trailing slash
     * @param	string	$rempath	Path to destination - include the base folder with trailing slash
     * @return	bool
     */
    public function mirror($locpath, $rempath)
    {
        if (! $this->_is_conn()) {
            return false;
        }

        // Open the local file path
        if ($fp = @opendir($locpath)) {
            // Attempt to open the remote file path and try to create it, if it doesn't exist
            if (! $this->changedir($rempath, true) && (! $this->mkdir($rempath) or ! $this->changedir($rempath))) {
                return false;
            }

            // Recursively read the local directory
            while (false !== ($file = readdir($fp))) {
                if (is_dir($locpath.$file) && $file[0] !== '.') {
                    $this->mirror($locpath.$file.'/', $rempath.$file.'/');
                } elseif ($file[0] !== '.') {
                    // Get the file extension so we can se the upload type
                    $ext = $this->_getext($file);
                    $mode = $this->_settype($ext);

                    $this->upload($locpath.$file, $rempath.$file, $mode);
                }
            }

            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Extract the file extension
     *
     * @param	string	$filename
     * @return	string
     */
    protected function _getext($filename)
    {
        return (($dot = strrpos($filename, '.')) === false)
            ? 'txt'
            : substr($filename, $dot + 1);
    }

    // --------------------------------------------------------------------

    /**
     * Set the upload type
     *
     * @param	string	$ext	Filename extension
     * @return	string
     */
    protected function _settype($ext)
    {
        return in_array($ext, array('txt', 'text', 'php', 'phps', 'php4', 'js', 'css', 'htm', 'html', 'phtml', 'shtml', 'log', 'xml'), true)
            ? 'ascii'
            : 'binary';
    }

    // ------------------------------------------------------------------------

    /**
     * Close the connection
     *
     * @return	bool
     */
    public function close()
    {
        return $this->_is_conn()
            ? @ftp_close($this->conn_id)
            : false;
    }

    // ------------------------------------------------------------------------

    /**
     * Display error message
     *
     * @param	string	$line
     * @return	void
     */
    protected function _error($line)
    {
        $CI =& get_instance();
        $CI->lang->load('ftp');
        show_error($CI->lang->line($line));
    }
}
