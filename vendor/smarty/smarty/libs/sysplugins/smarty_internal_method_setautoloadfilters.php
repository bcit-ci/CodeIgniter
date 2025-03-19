<?php

/**
 * Smarty Method SetAutoloadFilters
 *
 * Smarty::setAutoloadFilters() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_SetAutoloadFilters
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;

    /**
     * Valid filter types
     *
     * @var array
     */
    private $filterTypes = array('pre' => true, 'post' => true, 'output' => true, 'variable' => true);

    /**
     * Set autoload filters
     *
     * @api Smarty::setAutoloadFilters()
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  array                                                          $filters filters to load automatically
     * @param  string                                                         $type    "pre", "output", … specify the
     *                                                                                 filter type to set. Defaults to
     *                                                                                 none treating $filters' keys as
     *                                                                                 the appropriate types
     *
     * @return \Smarty|\Smarty_Internal_Template
     */
    public function setAutoloadFilters(Smarty_Internal_TemplateBase $obj, $filters, $type = null)
    {
        $smarty = isset($obj->smarty) ? $obj->smarty : $obj;
        if ($type !== null) {
            $this->_checkFilterType($type);
            $smarty->autoload_filters[$type] = (array) $filters;
        } else {
            foreach ((array) $filters as $type => $value) {
                $this->_checkFilterType($type);
            }
            $smarty->autoload_filters = (array) $filters;
        }
        return $obj;
    }

    /**
     * Check if filter type is valid
     *
     * @param string $type
     *
     * @throws \SmartyException
     */
    public function _checkFilterType($type)
    {
        if (!isset($this->filterTypes[$type])) {
            throw new SmartyException("Illegal filter type \"{$type}\"");
        }
    }
}