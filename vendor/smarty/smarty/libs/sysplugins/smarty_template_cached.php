<?php
/**
 * Created by PhpStorm.
 * User: Uwe Tews
 * Date: 04.12.2014
 * Time: 06:08
 */

/**
 * Smarty Resource Data Object
 * Cache Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 */
class Smarty_Template_Cached extends Smarty_Template_Resource_Base
{
    /**
     * Cache Is Valid
     *
     * @var boolean
     */
    public $valid = null;

    /**
     * CacheResource Handler
     *
     * @var Smarty_CacheResource
     */
    public $handler = null;

    /**
     * Template Cache Id (Smarty_Internal_Template::$cache_id)
     *
     * @var string
     */
    public $cache_id = null;

    /**
     * saved cache lifetime in seconds
     *
     * @var integer
     */
    public $cache_lifetime = 0;

    /**
     * Id for cache locking
     *
     * @var string
     */
    public $lock_id = null;

    /**
     * flag that cache is locked by this instance
     *
     * @var bool
     */
    public $is_locked = false;

    /**
     * Source Object
     *
     * @var Smarty_Template_Source
     */
    public $source = null;

    /**
     * Nocache hash codes of processed compiled templates
     *
     * @var array
     */
    public $hashes = array();

    /**
     * create Cached Object container
     *
     * @param Smarty_Internal_Template $_template template object
     */
    public function __construct(Smarty_Internal_Template $_template)
    {
        $this->compile_id = $_template->compile_id;
        $this->cache_id = $_template->cache_id;
        $this->source = $_template->source;
        if (!class_exists('Smarty_CacheResource', false)) {
            require SMARTY_SYSPLUGINS_DIR . 'smarty_cacheresource.php';
        }
        $this->handler = Smarty_CacheResource::load($_template->smarty);
    }

    /**
     * @param Smarty_Internal_Template $_template
     *
     * @return Smarty_Template_Cached
     */
    static function load(Smarty_Internal_Template $_template)
    {
        $_template->cached = new Smarty_Template_Cached($_template);
        $_template->cached->handler->populate($_template->cached, $_template);
        // caching enabled ?
        if (!($_template->caching == Smarty::CACHING_LIFETIME_CURRENT ||
                $_template->caching == Smarty::CACHING_LIFETIME_SAVED) || $_template->source->handler->recompiled
        ) {
            $_template->cached->valid = false;
        }
        return $_template->cached;
    }

    /**
     * Render cache template
     *
     * @param \Smarty_Internal_Template $_template
     * @param  bool                     $no_output_filter
     *
     * @throws \Exception
     */
    public function render(Smarty_Internal_Template $_template, $no_output_filter = true)
    {
        if ($this->isCached($_template)) {
            if ($_template->smarty->debugging) {
                $_template->smarty->_debug->start_cache($_template);
            }
            if (!$this->processed) {
                $this->process($_template);
            }
            $this->getRenderedTemplateCode($_template);
            if ($_template->smarty->debugging) {
                $_template->smarty->_debug->end_cache($_template);
            }
            return;
        } else {
            $_template->smarty->ext->_updateCache->updateCache($this, $_template, $no_output_filter);
        }
    }

    /**
     * Check if cache is valid, lock cache if required
     *
     * @param \Smarty_Internal_Template $_template
     *
     * @return bool flag true if cache is valid
     */
    public function isCached(Smarty_Internal_Template $_template)
    {
        if ($this->valid !== null) {
            return $this->valid;
        }
        while (true) {
            while (true) {
                if ($this->exists === false || $_template->smarty->force_compile || $_template->smarty->force_cache) {
                    $this->valid = false;
                } else {
                    $this->valid = true;
                }
                if ($this->valid && $_template->caching == Smarty::CACHING_LIFETIME_CURRENT &&
                    $_template->cache_lifetime >= 0 && time() > ($this->timestamp + $_template->cache_lifetime)
                ) {
                    // lifetime expired
                    $this->valid = false;
                }
                if ($this->valid && $_template->smarty->compile_check == 1 &&
                    $_template->source->getTimeStamp() > $this->timestamp
                ) {
                    $this->valid = false;
                }
                if ($this->valid || !$_template->smarty->cache_locking) {
                    break;
                }
                if (!$this->handler->locked($_template->smarty, $this)) {
                    $this->handler->acquireLock($_template->smarty, $this);
                    break 2;
                }
                $this->handler->populate($this, $_template);
            }
            if ($this->valid) {
                if (!$_template->smarty->cache_locking || $this->handler->locked($_template->smarty, $this) === null) {
                    // load cache file for the following checks
                    if ($_template->smarty->debugging) {
                        $_template->smarty->_debug->start_cache($_template);
                    }
                    if ($this->handler->process($_template, $this) === false) {
                        $this->valid = false;
                    } else {
                        $this->processed = true;
                    }
                    if ($_template->smarty->debugging) {
                        $_template->smarty->_debug->end_cache($_template);
                    }
                } else {
                    $this->is_locked = true;
                    continue;
                }
            } else {
                return $this->valid;
            }
            if ($this->valid && $_template->caching === Smarty::CACHING_LIFETIME_SAVED &&
                $_template->cached->cache_lifetime >= 0 &&
                (time() > ($_template->cached->timestamp + $_template->cached->cache_lifetime))
            ) {
                $this->valid = false;
            }
            if ($_template->smarty->cache_locking) {
                if (!$this->valid) {
                    $this->handler->acquireLock($_template->smarty, $this);
                } elseif ($this->is_locked) {
                    $this->handler->releaseLock($_template->smarty, $this);
                }
            }
            return $this->valid;
        }
        return $this->valid;
    }

    /**
     * Process cached template
     *
     * @param Smarty_Internal_Template $_template template object
     * @param bool                     $update    flag if called because cache update
     */
    public function process(Smarty_Internal_Template $_template, $update = false)
    {
        if ($this->handler->process($_template, $this, $update) === false) {
            $this->valid = false;
        }
        if ($this->valid) {
            $this->processed = true;
        } else {
            $this->processed = false;
        }
    }

    /**
     * Read cache content from handler
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @return string content
     */
    public function read(Smarty_Internal_Template $_template)
    {
        if (!$_template->source->handler->recompiled) {
            return $this->handler->readCachedContent($_template);
        }
        return false;
    }
}
