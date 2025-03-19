<?php

/**
 * Inline Runtime Methods render, setSourceByUid, setupSubTemplate
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 **/
class Smarty_Internal_Runtime_UpdateCache
{
    /**
     * check client side cache
     *
     * @param \Smarty_Template_Cached  $cached
     * @param Smarty_Internal_Template $_template
     * @param  string                  $content
     */
    public function cacheModifiedCheck(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template, $content)
    {
    }

    /**
     * Sanitize content and write it to cache resource
     *
     * @param \Smarty_Template_Cached  $cached
     * @param Smarty_Internal_Template $_template
     * @param bool                     $no_output_filter
     *
     * @throws \SmartyException
     */
    public function removeNoCacheHash(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template,
                                      $no_output_filter)
    {
        $content = ob_get_clean();
        unset($cached->hashes[$_template->compiled->nocache_hash]);
        if (!empty($cached->hashes)) {
            $hash_array = array();
            foreach ($cached->hashes as $hash => $foo) {
                $hash_array[] = "/{$hash}/";
            }
            $content = preg_replace($hash_array, $_template->compiled->nocache_hash, $content);
        }
        $_template->cached->has_nocache_code = false;
        // get text between non-cached items
        $cache_split =
            preg_split("!/\*%%SmartyNocache:{$_template->compiled->nocache_hash}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->compiled->nocache_hash}%%\*/!s",
                       $content);
        // get non-cached items
        preg_match_all("!/\*%%SmartyNocache:{$_template->compiled->nocache_hash}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->compiled->nocache_hash}%%\*/!s",
                       $content, $cache_parts);
        $content = '';
        // loop over items, stitch back together
        foreach ($cache_split as $curr_idx => $curr_split) {
            // escape PHP tags in template content
            $content .= preg_replace('/(<%|%>|<\?php|<\?|\?>|<script\s+language\s*=\s*[\"\']?\s*php\s*[\"\']?\s*>)/',
                                     "<?php echo '\$1'; ?>\n", $curr_split);
            if (isset($cache_parts[0][$curr_idx])) {
                $_template->cached->has_nocache_code = true;
                $content .= $cache_parts[1][$curr_idx];
            }
        }
        if (!$no_output_filter && !$_template->compiled->has_nocache_code &&
            (isset($_template->smarty->autoload_filters['output']) ||
                isset($_template->smarty->registered_filters['output']))
        ) {
            $content = $_template->smarty->ext->_filterHandler->runFilter('output', $content, $_template);
        }
        // write cache file content
        $this->writeCachedContent($cached, $_template, $content);
    }

    /**
     * Cache was invalid , so render from compiled and write to cache
     *
     * @param \Smarty_Template_Cached   $cached
     * @param \Smarty_Internal_Template $_template
     * @param                           $no_output_filter
     *
     * @throws \Exception
     */
    public function updateCache(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template, $no_output_filter)
    {
        if ($_template->source->handler->uncompiled) {
            ob_start();
            $_template->source->render($_template);
        } else {
            ob_start();
            if (!isset($_template->compiled)) {
                $_template->loadCompiled();
            }
            $_template->compiled->render($_template);
        }
        if ($_template->smarty->debugging) {
            $_template->smarty->_debug->start_cache($_template);
        }
        $this->removeNoCacheHash($cached, $_template, $no_output_filter);
        $compile_check = $_template->smarty->compile_check;
        $_template->smarty->compile_check = false;
        if (isset($_template->parent) && $_template->parent->_objType == 2) {
            $_template->compiled->unifunc = $_template->parent->compiled->unifunc;
        }
        if (!$_template->cached->processed) {
            $_template->cached->process($_template, true);
        }
        $_template->smarty->compile_check = $compile_check;
        $cached->getRenderedTemplateCode($_template);
        if ($_template->smarty->debugging) {
            $_template->smarty->_debug->end_cache($_template);
        }
    }

    /**
     * Writes the content to cache resource
     *
     * @param \Smarty_Template_Cached  $cached
     * @param Smarty_Internal_Template $_template
     * @param string                   $content
     *
     * @return bool
     */
    public function writeCachedContent(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template, $content)
    {
        if ($_template->source->handler->recompiled || !($_template->caching == Smarty::CACHING_LIFETIME_CURRENT ||
                $_template->caching == Smarty::CACHING_LIFETIME_SAVED)
        ) {
            // don't write cache file
            return false;
        }
        $content = $_template->smarty->ext->_codeFrame->create($_template, $content, '', true);
        return $this->write($cached, $_template, $content);
    }

    /**
     * Write this cache object to handler
     *
     * @param \Smarty_Template_Cached  $cached
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return bool success
     */
    public function write(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template, $content)
    {
        if (!$_template->source->handler->recompiled) {
            if ($cached->handler->writeCachedContent($_template, $content)) {
                $cached->content = null;
                $cached->timestamp = time();
                $cached->exists = true;
                $cached->valid = true;
                $cached->cache_lifetime = $_template->cache_lifetime;
                $cached->processed = false;
                if ($_template->smarty->cache_locking) {
                    $cached->handler->releaseLock($_template->smarty, $cached);
                }

                return true;
            }
            $cached->content = null;
            $cached->timestamp = false;
            $cached->exists = false;
            $cached->valid = false;
            $cached->processed = false;
        }

        return false;
    }

}