<?php
/**
 * Smarty Internal Plugin CacheResource File
 *
 * @package    Smarty
 * @subpackage Cacher
 * @author     Uwe Tews
 * @author     Rodney Rehm
 */

/**
 * This class does contain all necessary methods for the HTML cache on file system
 * Implements the file system as resource for the HTML cache Version ussing nocache inserts.
 *
 * @package    Smarty
 * @subpackage Cacher
 */
class Smarty_Internal_CacheResource_File extends Smarty_CacheResource
{
    /**
     * populate Cached Object with meta data from Resource
     *
     * @param Smarty_Template_Cached   $cached    cached object
     * @param Smarty_Internal_Template $_template template object
     *
     * @return void
     */
    public function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template)
    {
        $_source_file_path = str_replace(':', '.', $_template->source->filepath);
        $_cache_id = isset($_template->cache_id) ? preg_replace('![^\w\|]+!', '_', $_template->cache_id) : null;
        $_compile_id = isset($_template->compile_id) ? preg_replace('![^\w]+!', '_', $_template->compile_id) : null;
        $_filepath = $_template->source->uid;
        // if use_sub_dirs, break file into directories
        if ($_template->smarty->use_sub_dirs) {
            $_filepath = substr($_filepath, 0, 2) . DS . substr($_filepath, 2, 2) . DS . substr($_filepath, 4, 2) . DS .
                $_filepath;
        }
        $_compile_dir_sep = $_template->smarty->use_sub_dirs ? DS : '^';
        if (isset($_cache_id)) {
            $_cache_id = str_replace('|', $_compile_dir_sep, $_cache_id) . $_compile_dir_sep;
        } else {
            $_cache_id = '';
        }
        if (isset($_compile_id)) {
            $_compile_id = $_compile_id . $_compile_dir_sep;
        } else {
            $_compile_id = '';
        }
        $_cache_dir = $_template->smarty->getCacheDir();
        if ($_template->smarty->cache_locking) {
            // create locking file name
            // relative file name?
            if (!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_cache_dir)) {
                $_lock_dir = rtrim(getcwd(), '/\\') . DS . $_cache_dir;
            } else {
                $_lock_dir = $_cache_dir;
            }
            $cached->lock_id = $_lock_dir . sha1($_cache_id . $_compile_id . $_template->source->uid) . '.lock';
        }
        $cached->filepath = $_cache_dir . $_cache_id . $_compile_id . $_filepath . '.' . basename($_source_file_path) .
            '.php';
        $cached->timestamp = $cached->exists = is_file($cached->filepath);
        if ($cached->exists) {
            $cached->timestamp = filemtime($cached->filepath);
        }
    }

    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Cached $cached cached object
     *
     * @return void
     */
    public function populateTimestamp(Smarty_Template_Cached $cached)
    {
        $cached->timestamp = $cached->exists = is_file($cached->filepath);
        if ($cached->exists) {
            $cached->timestamp = filemtime($cached->filepath);
        }
    }

    /**
     * Read the cached template and process its header
     *
     * @param Smarty_Internal_Template $_template template object
     * @param Smarty_Template_Cached   $cached    cached object
     * @param bool                     $update flag if called because cache update
     *
     * @return boolean true or false if the cached content does not exist
     */
    public function process(Smarty_Internal_Template $_template, Smarty_Template_Cached $cached = null, $update = false)
    {
        /** @var Smarty_Internal_Template $_smarty_tpl
         * used in included file
         */
        $_smarty_tpl = $_template;
        $_template->cached->valid = false;
        if ($update && defined('HHVM_VERSION')) {
            return $_template->smarty->ext->_hhvm->includeHhvm($_template, $_template->cached->filepath);
        } else {
            return @include $_template->cached->filepath;
        }
    }

    /**
     * Write the rendered template output to cache
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return boolean success
     */
    public function writeCachedContent(Smarty_Internal_Template $_template, $content)
    {
        if ($_template->smarty->ext->_writeFile->writeFile($_template->cached->filepath, $content, $_template->smarty) === true) {
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($_template->cached->filepath);
            }
            $cached = $_template->cached;
            $cached->timestamp = $cached->exists = is_file($cached->filepath);
            if ($cached->exists) {
                $cached->timestamp = filemtime($cached->filepath);
                return true;
            }
        }
        return false;
    }

    /**
     * Read cached template from cache
     *
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return string  content
     */
    public function readCachedContent(Smarty_Internal_Template $_template)
    {
        if (is_file($_template->cached->filepath)) {
            return file_get_contents($_template->cached->filepath);
        }
        return false;
    }

    /**
     * Empty cache
     *
     * @param Smarty  $smarty
     * @param integer $exp_time expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    public function clearAll(Smarty $smarty, $exp_time = null)
    {
        return Smarty_Internal_Extension_Clear::clear($smarty, null, null, null, $exp_time);
    }

    /**
     * Empty cache for a specific template
     *
     * @param Smarty  $smarty
     * @param string  $resource_name template name
     * @param string  $cache_id      cache id
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time)
    {
        return Smarty_Internal_Extension_Clear::clear($smarty, $resource_name, $cache_id, $compile_id, $exp_time);
    }

    /**
     * Check is cache is locked for this template
     *
     * @param Smarty                 $smarty Smarty object
     * @param Smarty_Template_Cached $cached cached object
     *
     * @return boolean true or false if cache is locked
     */
    public function hasLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            clearstatcache(true, $cached->lock_id);
        } else {
            clearstatcache();
        }
        if (is_file($cached->lock_id)) {
            $t = @filemtime($cached->lock_id);
            return $t && (time() - $t < $smarty->locking_timeout);
        } else {
            return false;
        }
    }

    /**
     * Lock cache for this template
     *
     * @param Smarty                 $smarty Smarty object
     * @param Smarty_Template_Cached $cached cached object
     *
     * @return bool|void
     */
    public function acquireLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        $cached->is_locked = true;
        touch($cached->lock_id);
    }

    /**
     * Unlock cache for this template
     *
     * @param Smarty                 $smarty Smarty object
     * @param Smarty_Template_Cached $cached cached object
     *
     * @return bool|void
     */
    public function releaseLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        $cached->is_locked = false;
        @unlink($cached->lock_id);
    }
}
