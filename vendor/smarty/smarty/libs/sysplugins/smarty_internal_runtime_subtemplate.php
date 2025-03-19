<?php

/**
 * Sub Template Runtime Methods render, setupSubTemplate
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 **/
class Smarty_Internal_Runtime_SubTemplate
{

    /**
     * Subtemplate template object cache
     *
     * @var Smarty_Internal_Template[]
     */
    public $tplObjects = array();

    /**
     * Subtemplate call count
     *
     * @var int[]
     */
    public $subTplInfo = array();

    /**
     * Runtime function to render subtemplate
     *
     * @param \Smarty_Internal_Template $parent
     * @param string                    $template       template name
     * @param mixed                     $cache_id       cache id
     * @param mixed                     $compile_id     compile id
     * @param integer                   $caching        cache mode
     * @param integer                   $cache_lifetime life time of cache data
     * @param array                     $data           passed parameter template variables
     * @param int                       $scope          scope in which {include} should execute
     * @param bool                      $forceTplCache  cache template object
     * @param string                    $uid            file dependency uid
     * @param string                    $content_func   function name
     *
     */
    public function render(Smarty_Internal_Template $parent, $template, $cache_id, $compile_id, $caching,
                           $cache_lifetime, $data, $scope, $forceTplCache, $uid = null, $content_func = null)
    {
        // if there are cached template objects calculate $templateID
        $_templateId =
            !empty($this->tplObjects) ? $parent->smarty->_getTemplateId($template, $cache_id, $compile_id, $caching) :
                null;
        // already in template cache?
        /* @var Smarty_Internal_Template $tpl */
        if (isset($_templateId) && isset($this->tplObjects[$_templateId])) {
            // clone cached template object because of possible recursive call
            $tpl = clone $this->tplObjects[$_templateId];
            $tpl->parent = $parent;
            // if $caching mode changed the compiled resource is invalid
            if ((bool) $tpl->caching !== (bool) $caching) {
                unset($tpl->compiled);
            }
            // get variables from calling scope
            $tpl->tpl_vars = $parent->tpl_vars;
            $tpl->config_vars = $parent->config_vars;
            // get template functions
            $tpl->tpl_function = $parent->tpl_function;
            // copy inheritance object?
            if (isset($parent->ext->_inheritance)) {
                $tpl->ext->_inheritance = $parent->ext->_inheritance;
            } else {
                unset($tpl->ext->_inheritance);
            }
        } else {
            $tpl = clone $parent;
            $tpl->parent = $parent;
            if (!isset($tpl->templateId) || $tpl->templateId !== $_templateId) {
                $tpl->templateId = $_templateId;
                $tpl->template_resource = $template;
                $tpl->cache_id = $cache_id;
                $tpl->compile_id = $compile_id;
                if (isset($uid)) {
                    // for inline templates we can get all resource information from file dependency
                    if (isset($tpl->compiled->file_dependency[$uid])) {
                        list($filepath, $timestamp, $resource) = $tpl->compiled->file_dependency[$uid];
                        $tpl->source =
                            new Smarty_Template_Source(isset($tpl->smarty->_cache['resource_handlers'][$resource]) ?
                                                           $tpl->smarty->_cache['resource_handlers'][$resource] :
                                                           Smarty_Resource::load($tpl->smarty, $resource), $tpl->smarty,
                                                       $filepath, $resource, $filepath);
                        $tpl->source->filepath = $filepath;
                        $tpl->source->timestamp = $timestamp;
                        $tpl->source->exists = true;
                        $tpl->source->uid = $uid;
                    } else {
                        $tpl->source = null;
                    }
                } else {
                    $tpl->source = null;
                }
                if (!isset($tpl->source)) {
                    $tpl->source = Smarty_Template_Source::load($tpl);
                    unset($tpl->compiled);
                }
                unset($tpl->cached);
            }
        }
        $tpl->caching = $caching;
        $tpl->cache_lifetime = $cache_lifetime;
        if ($caching == 9999) {
            $tpl->cached = $parent->cached;
        }
        // set template scope
        $tpl->scope = $scope;
        $scopePtr = false;
        if ($scope & ~Smarty::SCOPE_BUBBLE_UP) {
            if ($scope == Smarty::SCOPE_GLOBAL) {
                $tpl->tpl_vars = Smarty::$global_tpl_vars;
                $tpl->config_vars = $tpl->smarty->config_vars;
                $scopePtr = true;
            } else {
                if ($scope == Smarty::SCOPE_PARENT) {
                    $scopePtr = $parent;
                } elseif ($scope == Smarty::SCOPE_SMARTY) {
                    $scopePtr = $tpl->smarty;
                } else {
                    $scopePtr = $tpl;
                    while (isset($scopePtr->parent)) {
                        if ($scopePtr->parent->_objType != 2 && $scope & Smarty::SCOPE_TPL_ROOT) {
                            break;
                        }
                        $scopePtr = $scopePtr->parent;
                    }
                }
                $tpl->tpl_vars = $scopePtr->tpl_vars;
                $tpl->config_vars = $scopePtr->config_vars;
            }
        }

        if (!isset($this->tplObjects[$tpl->_getTemplateId()]) && !$tpl->source->handler->recompiled) {
            // if template is called multiple times set flag to to cache template objects
            $forceTplCache = $forceTplCache ||
                (isset($this->subTplInfo[$tpl->template_resource]) && $this->subTplInfo[$tpl->template_resource] > 1);
            // check if template object should be cached
            if ($tpl->parent->_objType == 2 && isset($this->tplObjects[$tpl->parent->templateId]) ||
                ($forceTplCache && $tpl->smarty->resource_cache_mode & Smarty::RESOURCE_CACHE_AUTOMATIC) ||
                ($tpl->smarty->resource_cache_mode & Smarty::RESOURCE_CACHE_ON)
            ) {
                $this->tplObjects[$tpl->_getTemplateId()] = $tpl;
            }
        }

        if (!empty($data)) {
            // set up variable values
            foreach ($data as $_key => $_val) {
                $tpl->tpl_vars[$_key] = new Smarty_Variable($_val);
            }
        }
        if (isset($uid)) {
            if ($parent->smarty->debugging) {
                $parent->smarty->_debug->start_template($tpl);
                $parent->smarty->_debug->start_render($tpl);
            }
            $tpl->compiled->getRenderedTemplateCode($tpl, $content_func);
            if ($parent->smarty->debugging) {
                $parent->smarty->_debug->end_template($tpl);
                $parent->smarty->_debug->end_render($tpl);
            }
            if ($tpl->caching == 9999 && $tpl->compiled->has_nocache_code) {
                $parent->cached->hashes[$tpl->compiled->nocache_hash] = true;
            }
        } else {
            if (isset($tpl->compiled)) {
                $tpl->compiled->render($tpl);
            } else {
                $tpl->render();
            }
        }
        if ($scopePtr) {
            if ($scope == Smarty::SCOPE_GLOBAL) {
                Smarty::$global_tpl_vars = $tpl->tpl_vars;
                $tpl->smarty->config_vars = $tpl->config_vars;
            } else {
                $scopePtr->tpl_vars = $tpl->tpl_vars;
                $scopePtr->config_vars = $tpl->config_vars;
            }
        }
    }

    /**
     * Get called subtemplates from compiled template and save call count
     *
     * @param \Smarty_Internal_Template $tpl
     */
    public function registerSubTemplates(Smarty_Internal_Template $tpl)
    {
        foreach ($tpl->compiled->includes as $name => $count) {
            if (isset($this->subTplInfo[$name])) {
                $this->subTplInfo[$name] += $count;
            } else {
                $this->subTplInfo[$name] = $count;
            }
        }
    }
}
