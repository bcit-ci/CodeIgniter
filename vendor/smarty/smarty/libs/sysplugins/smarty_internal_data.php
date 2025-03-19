<?php
/**
 * Smarty Internal Plugin Data
 * This file contains the basic classes and methods for template and variable creation
 *
 * @package    Smarty
 * @subpackage Template
 * @author     Uwe Tews
 */

/**
 * Base class with template and variable methods
 *
 * @package    Smarty
 * @subpackage Template
 *
 * @method mixed getConfigVars(string $varName = null, bool $searchParents = true)
 * @method mixed getStreamVariable(string $variable)
 * @method Smarty_Internal_Data clearAssign(mixed $tpl_var)
 * @method Smarty_Internal_Data clearAllAssign()
 * @method Smarty_Internal_Data clearConfig(string $varName = null)
 * @method Smarty_Internal_Data configLoad(string $config_file, mixed $sections = null, string $scope = 'local')
 * @property int $scope
 */
class Smarty_Internal_Data
{
    /**
     * This object type (Smarty = 1, template = 2, data = 4)
     *
     * @var int
     */
    public $_objType = 4;

    /**
     * name of class used for templates
     *
     * @var string
     */
    public $template_class = 'Smarty_Internal_Template';

    /**
     * template variables
     *
     * @var Smarty_Variable[]
     */
    public $tpl_vars = array();

    /**
     * parent template (if any)
     *
     * @var Smarty|Smarty_Internal_Template|Smarty_Internal_Data
     */
    public $parent = null;

    /**
     * configuration settings
     *
     * @var string[]
     */
    public $config_vars = array();

    /**
     * extension handler
     *
     * @var Smarty_Internal_Extension_Handler
     */
    public $ext = null;

    /**
     * Smarty_Internal_Data constructor.
     *
     * Install extension handler
     */
    public function __construct()
    {
        $this->ext = new Smarty_Internal_Extension_Handler();
        $this->ext->objType = $this->_objType;
    }

    /**
     * assigns a Smarty variable
     *
     * @param  array|string $tpl_var the template variable name(s)
     * @param  mixed        $value   the value to assign
     * @param  boolean      $nocache if true any output of this variable will be not cached
     *
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for
     *                              chaining
     */
    public function assign($tpl_var, $value = null, $nocache = false)
    {
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $_key => $_val) {
                if ($_key != '') {
                    $this->tpl_vars[$_key] = new Smarty_Variable($_val, $nocache);
                    if ($this->_objType == 2 && $this->scope) {
                        $this->ext->_updateScope->updateScope($this, $_key);
                    }
                }
            }
        } else {
            if ($tpl_var != '') {
                $this->tpl_vars[$tpl_var] = new Smarty_Variable($value, $nocache);
                if ($this->_objType == 2 && $this->scope) {
                    $this->ext->_updateScope->updateScope($this, $tpl_var);
                }
            }
        }
        return $this;
    }

    /**
     * appends values to template variables
     *
     * @api  Smarty::append()
     * @link http://www.smarty.net/docs/en/api.append.tpl
     *
     * @param  array|string $tpl_var                                           the template variable name(s)
     * @param  mixed        $value                                             the value to append
     * @param  bool         $merge                                             flag if array elements shall be merged
     * @param  bool         $nocache                                           if true any output of this variable will
     *                                                                         be not cached
     *
     * @return \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty
     */
    public function append($tpl_var, $value = null, $merge = false, $nocache = false)
    {
        return $this->ext->append->append($this, $tpl_var, $value, $merge, $nocache);
    }

    /**
     * assigns a global Smarty variable
     *
     * @param  string  $varName the global variable name
     * @param  mixed   $value   the value to assign
     * @param  boolean $nocache if true any output of this variable will be not cached
     *
     * @return \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty
     */
    public function assignGlobal($varName, $value = null, $nocache = false)
    {
        return $this->ext->assignGlobal->assignGlobal($this, $varName, $value, $nocache);
    }

    /**
     * appends values to template variables by reference
     *
     * @param  string  $tpl_var the template variable name
     * @param  mixed   &$value  the referenced value to append
     * @param  boolean $merge   flag if array elements shall be merged
     *
     * @return \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty
     */
    public function appendByRef($tpl_var, &$value, $merge = false)
    {
        return $this->ext->appendByRef->appendByRef($this, $tpl_var, $value, $merge);
    }

    /**
     * assigns values to template variables by reference
     *
     * @param string   $tpl_var the template variable name
     * @param          $value
     * @param  boolean $nocache if true any output of this variable will be not cached
     *
     * @return \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty
     */
    public function assignByRef($tpl_var, &$value, $nocache = false)
    {
        return $this->ext->assignByRef->assignByRef($this, $tpl_var, $value, $nocache);
    }

    /**
     * Returns a single or all template variables
     *
     * @api  Smarty::getTemplateVars()
     * @link http://www.smarty.net/docs/en/api.get.template.vars.tpl
     *
     * @param  string                                                 $varName       variable name or null
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $_ptr          optional pointer to data object
     * @param  bool                                                   $searchParents include parent templates?
     *
     * @return mixed variable value or or array of variables
     */
    public function getTemplateVars($varName = null, Smarty_Internal_Data $_ptr = null, $searchParents = true)
    {
        return $this->ext->getTemplateVars->getTemplateVars($this, $varName, $_ptr, $searchParents);
    }

    /**
     * gets the object of a Smarty variable
     *
     * @param  string  $variable       the name of the Smarty variable
     * @param  Smarty_Internal_Data  $_ptr           optional pointer to data object
     * @param  boolean $searchParents search also in parent data
     * @param bool     $error_enable
     *
     * @return Smarty_Variable|Smarty_Undefined_Variable the object of the variable
     * @deprecated since 3.1.28 please use Smarty_Internal_Data::getTemplateVars() instead.
     */
    public function getVariable($variable = null, Smarty_Internal_Data $_ptr = null, $searchParents = true, $error_enable = true){
        return $this->ext->getTemplateVars->_getVariable($this, $variable, $_ptr, $searchParents, $error_enable);
    }
    
    /**
     * Follow the parent chain an merge template and config variables
     *
     * @param \Smarty_Internal_Data|null $data
     */
    public function _mergeVars(Smarty_Internal_Data $data = null)
    {
        if (isset($data)) {
            if (!empty($this->tpl_vars)) {
                $data->tpl_vars = array_merge($this->tpl_vars, $data->tpl_vars);
            }
            if (!empty($this->config_vars)) {
                $data->config_vars = array_merge($this->config_vars, $data->config_vars);
            }
        } else {
            $data = $this;
        }
        if (isset($this->parent)) {
            $this->parent->_mergeVars($data);
        }
    }

    /**
     * Handle unknown class methods
     *
     * @param string $name unknown method-name
     * @param array  $args argument array
     *
     * @return mixed
     * @throws SmartyException
     */
    public function __call($name, $args)
    {
        return $this->ext->_callExternalMethod($this, $name, $args);
    }
}
