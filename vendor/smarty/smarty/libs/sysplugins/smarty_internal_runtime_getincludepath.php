<?php
/**
 * Smarty read include path plugin
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Monte Ohrt
 */

/**
 * Smarty Internal Read Include Path Class
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Runtime_GetIncludePath
{
    /**
     * include path cache
     *
     * @var string
     */
    public $_include_path = '';

    /**
     * include path directory cache
     *
     * @var array
     */
    public $_include_dirs = array();

    /**
     * include path directory cache
     *
     * @var array
     */
    public $_user_dirs = array();

    /**
     * stream cache
     *
     * @var string[]
     */
    public $isFile = array();

    /**
     * stream cache
     *
     * @var string[]
     */
    public $isPath = array();

    /**
     * stream cache
     *
     * @var int[]
     */
    public $number = array();

    /**
     * status cache
     *
     * @var bool
     */
    public $_has_stream_include = null;

    /**
     * Number for array index
     *
     * @var int
     */
    public $counter = 0;

    /**
     * Check if include path was updated
     *
     * @param \Smarty $smarty
     *
     * @return bool
     */
    public function isNewIncludePath(Smarty $smarty)
    {
        $_i_path = get_include_path();
        if ($this->_include_path != $_i_path) {
            $this->_include_dirs = array();
            $this->_include_path = $_i_path;
            $_dirs = (array) explode(PATH_SEPARATOR, $_i_path);
            foreach ($_dirs as $_path) {
                if (is_dir($_path)) {
                    $this->_include_dirs[] = $smarty->_realpath($_path . DS, true);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * return array with include path directories
     *
     * @param \Smarty $smarty
     *
     * @return array
     */
    public function getIncludePathDirs(Smarty $smarty)
    {
        $this->isNewIncludePath($smarty);
        return $this->_include_dirs;
    }

    /**
     * Return full file path from PHP include_path
     *
     * @param  string[] $dirs
     * @param  string   $file
     * @param \Smarty   $smarty
     *
     * @return bool|string full filepath or false
     *
     */
    public function getIncludePath($dirs, $file, Smarty $smarty)
    {
        //if (!(isset($this->_has_stream_include) ? $this->_has_stream_include : $this->_has_stream_include = false)) {
           if (!(isset($this->_has_stream_include) ? $this->_has_stream_include : $this->_has_stream_include = function_exists('stream_resolve_include_path'))) {
                $this->isNewIncludePath($smarty);
        }
        // try PHP include_path
        foreach ($dirs as $dir) {
            $dir_n = isset($this->number[$dir]) ? $this->number[$dir] : $this->number[$dir] = $this->counter ++;
            if (isset($this->isFile[$dir_n][$file])) {
                if ($this->isFile[$dir_n][$file]) {
                    return $this->isFile[$dir_n][$file];
                } else {
                    continue;
                }
            }
            if (isset($this->_user_dirs[$dir_n])) {
                if (false === $this->_user_dirs[$dir_n]) {
                    continue;
                } else {
                    $dir = $this->_user_dirs[$dir_n];
                }
            } else {
                if ($dir[0] == '/' || $dir[1] == ':') {
                    $dir = str_ireplace(getcwd(), '.', $dir);
                    if ($dir[0] == '/' || $dir[1] == ':') {
                        $this->_user_dirs[$dir_n] = false;
                        continue;
                    }
                }
                $dir = substr($dir, 2);
                $this->_user_dirs[$dir_n] = $dir;
            }
            if ($this->_has_stream_include) {
                $path = stream_resolve_include_path($dir . (isset($file) ? $file : ''));
                if ($path) {
                    return $this->isFile[$dir_n][$file] = $path;
                }
            } else {
                foreach ($this->_include_dirs as $key => $_i_path) {
                    $path = isset($this->isPath[$key][$dir_n]) ? $this->isPath[$key][$dir_n] : $this->isPath[$key][$dir_n] = is_dir($_dir_path = $_i_path .
                        $dir) ? $_dir_path : false;
                    if ($path === false) {
                        continue;
                    }
                    if (isset($file)) {
                        $_file = $this->isFile[$dir_n][$file] = (is_file($path . $file)) ? $path . $file : false;
                        if ($_file) {
                            return $_file;
                        }
                    } else {
                        // no file was given return directory path
                        return $path;
                    }
                }
            }
        }
        return false;
    }
}
