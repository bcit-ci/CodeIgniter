<?php

/**
 * Smarty Extension handler
 *
 * Load extensions dynamically
 *
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 * @property Smarty_Internal_Runtime_Inheritance      $_inheritance
 * @property Smarty_Internal_Runtime_SubTemplate      $_subTemplate
 * @property Smarty_Internal_Runtime_TplFunction      $_tplFunction
 * @property Smarty_Internal_Runtime_Var              $_var
 * @property Smarty_Internal_Runtime_Config           $_config
 * @property Smarty_Internal_Runtime_Foreach          $_foreach
 * @property Smarty_Internal_Runtime_Hhvm             $_hhvm
 * @property Smarty_Internal_Runtime_WriteFile        $_writeFile
 * @property Smarty_Internal_Runtime_ValidateCompiled $_validateCompiled
 * @property Smarty_Internal_Runtime_CodeFrame        $_codeFrame
 * @property Smarty_Internal_Runtime_FilterHandler    $_filterHandler
 * @property Smarty_Internal_Runtime_GetIncludePath   $_getIncludePath
 * @property Smarty_Internal_Runtime_UpdateScope      $_updateScope
 * @property Smarty_Internal_Runtime_IsCached         $_isCached
 * @property Smarty_Internal_Runtime_CacheModify      $_cacheModify
 * @property Smarty_Internal_Runtime_UpdateCache      $_updateCache
 * @property Smarty_Internal_Method_GetTemplateVars   $getTemplateVars
 * @property Smarty_Internal_Method_Append            $append
 * @property Smarty_Internal_Method_AppendByRef       $appendByRef
 * @property Smarty_Internal_Method_AssignGlobal      $assignGlobal
 * @property Smarty_Internal_Method_AssignByRef       $assignByRef
 * @property Smarty_Internal_Method_LoadFilter        $loadFilter
 * @property Smarty_Internal_Method_LoadPlugin        $loadPlugin
 * @property Smarty_Internal_Method_RegisterFilter    $registerFilter
 * @property Smarty_Internal_Method_RegisterObject    $registerObject
 * @property Smarty_Internal_Method_RegisterPlugin    $registerPlugin
 */
class Smarty_Internal_Extension_Handler
{

    public $objType = null;

    /**
     * Cache for property information from generic getter/setter
     * Preloaded with names which should not use with generic getter/setter
     *
     * @var array
     */
    private $_property_info = array('AutoloadFilters' => 0, 'DefaultModifiers' => 0, 'ConfigVars' => 0,
                                    'DebugTemplate'   => 0, 'RegisteredObject' => 0, 'StreamVariable' => 0,
                                    'TemplateVars'    => 0,);#

    private $resolvedProperties = array();

    /**
     * Call external Method
     *
     * @param \Smarty_Internal_Data $data
     * @param string                $name external method names
     * @param array                 $args argument array
     *
     * @return mixed
     * @throws SmartyException
     */
    public function _callExternalMethod(Smarty_Internal_Data $data, $name, $args)
    {
        /* @var Smarty $data ->smarty */
        $smarty = isset($data->smarty) ? $data->smarty : $data;
        if (!isset($smarty->ext->$name)) {
            $class = 'Smarty_Internal_Method_' . ucfirst($name);
            if (preg_match('/^(set|get)([A-Z].*)$/', $name, $match)) {
                if (!isset($this->_property_info[$prop = $match[2]])) {
                    // convert camel case to underscored name
                    $this->resolvedProperties[$prop] = $pn = strtolower(join('_',
                                                                             preg_split('/([A-Z][^A-Z]*)/', $prop, - 1,
                                                                                        PREG_SPLIT_NO_EMPTY |
                                                                                        PREG_SPLIT_DELIM_CAPTURE)));
                    $this->_property_info[$prop] = property_exists($data, $pn) ? 1 :
                        ($data->_objType == 2 && property_exists($smarty, $pn) ? 2 : 0);
                }
                if ($this->_property_info[$prop]) {
                    $pn = $this->resolvedProperties[$prop];
                    if ($match[1] == 'get') {
                        return $this->_property_info[$prop] == 1 ? $data->$pn : $data->smarty->$pn;
                    } else {
                        return $this->_property_info[$prop] == 1 ? $data->$pn = $args[0] :
                            $data->smarty->$pn = $args[0];
                    }
                } elseif (!class_exists($class)) {
                    throw new SmartyException("property '$pn' does not exist.");
                }
            }
            if (class_exists($class)) {
                $callback = array($smarty->ext->$name = new $class(), $name);
            }
        } else {
            $callback = array($smarty->ext->$name, $name);
        }
        array_unshift($args, $data);
        if (isset($callback) && $callback[0]->objMap | $data->_objType) {
            return call_user_func_array($callback, $args);
        }
        return call_user_func_array(array(new Smarty_Internal_Undefined(), $name), $args);
    }

    /**
     * set extension property
     *
     * @param string $property_name property name
     * @param mixed  $value         value
     *
     * @throws SmartyException
     */
    public function __set($property_name, $value)
    {
        $this->$property_name = $value;
    }

    /**
     * get extension object
     *
     * @param string $property_name property name
     *
     * @return mixed|Smarty_Template_Cached
     * @throws SmartyException
     */
    public function __get($property_name)
    {
        // object properties of runtime template extensions will start with '_'
        if ($property_name[0] == '_') {
            $class = 'Smarty_Internal_Runtime_' . ucfirst(substr($property_name, 1));
        } else {
            $class = 'Smarty_Internal_Method_' . ucfirst($property_name);
        }
        if (class_exists($class)) {
            return $this->$property_name = new $class();
        }
        return $this;
    }

    /**
     * Call error handler for undefined method
     *
     * @param string $name unknown method-name
     * @param array  $args argument array
     *
     * @return mixed
     * @throws SmartyException
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array(new Smarty_Internal_Undefined(), $name), $args);
    }

}