<?php
/**
 * Smarty Internal Plugin
 *
 * @package    Smarty
 * @subpackage Cacher
 */

/**
 * Cache Handler API
 *
 * @package    Smarty
 * @subpackage Cacher
 * @author     Rodney Rehm
 */
abstract class Smarty_CacheResource
{
    /**
     * resource types provided by the core
     *
     * @var array
     */
    protected static $sysplugins = array('file' => 'smarty_internal_cacheresource_file.php',);

    /**
     * populate Cached Object with meta data from Resource
     *
     * @param Smarty_Template_Cached   $cached    cached object
     * @param Smarty_Internal_Template $_template template object
     *
     * @return void
     */
    abstract public function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template);

    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Cached $cached
     *
     * @return void
     */
    abstract public function populateTimestamp(Smarty_Template_Cached $cached);

    /**
     * Read the cached template and process header
     *
     * @param Smarty_Internal_Template $_template template object
     * @param Smarty_Template_Cached   $cached    cached object
     * @param bool                     $update    flag if called because cache update
     *
     * @return bool true or false if the cached content does not exist
     */
    abstract public function process(Smarty_Internal_Template $_template, Smarty_Template_Cached $cached = null, $update = false);

    /**
     * Write the rendered template output to cache
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return boolean success
     */
    abstract public function writeCachedContent(Smarty_Internal_Template $_template, $content);

    /**
     * Return cached content
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @return null|string
     */
    public function getCachedContent(Smarty_Internal_Template $_template)
    {
        if ($_template->cached->handler->process($_template)) {
            ob_start();
            $unifunc = $_template->cached->unifunc;
            $unifunc($_template);
            return ob_get_clean();
        }

        return null;
    }

    /**
     * Empty cache
     *
     * @param Smarty  $smarty   Smarty object
     * @param integer $exp_time expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    abstract public function clearAll(Smarty $smarty, $exp_time = null);

    /**
     * Empty cache for a specific template
     *
     * @param Smarty  $smarty        Smarty object
     * @param string  $resource_name template name
     * @param string  $cache_id      cache id
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    abstract public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time);

    /**
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool|null
     */
    public function locked(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        // theoretically locking_timeout should be checked against time_limit (max_execution_time)
        $start = microtime(true);
        $hadLock = null;
        while ($this->hasLock($smarty, $cached)) {
            $hadLock = true;
            if (microtime(true) - $start > $smarty->locking_timeout) {
                // abort waiting for lock release
                return false;
            }
            sleep(1);
        }

        return $hadLock;
    }

    /**
     * Check is cache is locked for this template
     *
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool
     */
    public function hasLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        // check if lock exists
        return false;
    }

    /**
     * Lock cache for this template
     *
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool
     */
    public function acquireLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        // create lock
        return true;
    }

    /**
     * Unlock cache for this template
     *
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool
     */
    public function releaseLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        // release lock
        return true;
    }

    /**
     * Load Cache Resource Handler
     *
     * @param Smarty $smarty Smarty object
     * @param string $type   name of the cache resource
     *
     * @throws SmartyException
     * @return Smarty_CacheResource Cache Resource Handler
     */
    public static function load(Smarty $smarty, $type = null)
    {
        if (!isset($type)) {
            $type = $smarty->caching_type;
        }

        // try smarty's cache
        if (isset($smarty->_cache['cacheresource_handlers'][$type])) {
            return $smarty->_cache['cacheresource_handlers'][$type];
        }

        // try registered resource
        if (isset($smarty->registered_cache_resources[$type])) {
            // do not cache these instances as they may vary from instance to instance
            return $smarty->_cache['cacheresource_handlers'][$type] = $smarty->registered_cache_resources[$type];
        }
        // try sysplugins dir
        if (isset(self::$sysplugins[$type])) {
            $cache_resource_class = 'Smarty_Internal_CacheResource_' . ucfirst($type);
            return $smarty->_cache['cacheresource_handlers'][$type] = new $cache_resource_class();
        }
        // try plugins dir
        $cache_resource_class = 'Smarty_CacheResource_' . ucfirst($type);
        if ($smarty->loadPlugin($cache_resource_class)) {
            return $smarty->_cache['cacheresource_handlers'][$type] = new $cache_resource_class();
        }
        // give up
        throw new SmartyException("Unable to load cache resource '{$type}'");
    }

    /**
     * Invalid Loaded Cache Files
     *
     * @param Smarty $smarty Smarty object
     */
    public function invalidLoadedCache(Smarty $smarty)
    {
        $smarty->_cache['isCached'] = array();
        if (isset($smarty->ext->_subtemplate)) {
            $smarty->ext->_subtemplate->tplObjects = array();
        }
    }
}
