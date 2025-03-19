<?php

/**
 * Tplfunc Runtime Methods callTemplateFunction
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 **/
class Smarty_Internal_Runtime_TplFunction
{
    /**
     * Call template function
     *
     * @param \Smarty_Internal_Template $tpl     template object
     * @param string                    $name    template function name
     * @param array                     $params  parameter array
     * @param bool                      $nocache true if called nocache
     *
     * @throws \SmartyException
     */
    public function callTemplateFunction(Smarty_Internal_Template $tpl, $name, $params, $nocache)
    {
        if (isset($tpl->tpl_function[$name])) {
            if (!$tpl->caching || ($tpl->caching && $nocache)) {
                $function = $tpl->tpl_function[$name]['call_name'];
            } else {
                if (isset($tpl->tpl_function[$name]['call_name_caching'])) {
                    $function = $tpl->tpl_function[$name]['call_name_caching'];
                } else {
                    $function = $tpl->tpl_function[$name]['call_name'];
                }
            }
            if (function_exists($function)) {
                $function ($tpl, $params);
                return;
            }
            // try to load template function dynamically
            if ($this->addTplFuncToCache($tpl, $name, $function)) {
                $function ($tpl, $params);
                return;
            }
        }
        throw new SmartyException("Unable to find template function '{$name}'");
    }

    /**
     *
     * Add template function to cache file for nocache calls
     *
     * @param Smarty_Internal_Template $tpl
     * @param string                   $_name     template function name
     * @param string                   $_function PHP function name
     *
     * @return bool
     */
    public function addTplFuncToCache(Smarty_Internal_Template $tpl, $_name, $_function)
    {
        $funcParam = $tpl->tpl_function[$_name];
        if (is_file($funcParam['compiled_filepath'])) {
            // read compiled file
            $code = file_get_contents($funcParam['compiled_filepath']);
            // grab template function
            if (preg_match("/\/\* {$_function} \*\/([\S\s]*?)\/\*\/ {$_function} \*\//", $code, $match)) {
                // grab source info from file dependency
                preg_match("/\s*'{$funcParam['uid']}'([\S\s]*?)\),/", $code, $match1);
                unset($code);
                // make PHP function known
                eval($match[0]);
                if (function_exists($_function)) {
                    // search cache file template
                    $tplPtr = $tpl;
                    while (!isset($tplPtr->cached) && isset($tplPtr->parent)) {
                        $tplPtr = $tplPtr->parent;
                    }
                    // add template function code to cache file
                    if (isset($tplPtr->cached)) {
                        $cache = $tplPtr->cached;
                        $content = $cache->read($tplPtr);
                        if ($content) {
                            // check if we must update file dependency
                            if (!preg_match("/'{$funcParam['uid']}'(.*?)'nocache_hash'/", $content, $match2)) {
                                $content = preg_replace("/('file_dependency'(.*?)\()/", "\\1{$match1[0]}", $content);
                            }
                            $tplPtr->smarty->ext->_updateCache->write($cache, $tplPtr, preg_replace('/\s*\?>\s*$/', "\n", $content) . "\n" .
                                                 preg_replace(array('/^\s*<\?php\s+/', '/\s*\?>\s*$/'), "\n",
                                                              $match[0]));
                        }
                    }
                    return true;
                }
            }
        }
        return false;
    }
}
