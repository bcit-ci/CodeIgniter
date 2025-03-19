<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage Security
 * @author     Uwe Tews
 */

/*
 * FIXME: Smarty_Security API
 *      - getter and setter instead of public properties would allow cultivating an internal cache properly
 *      - current implementation of isTrustedResourceDir() assumes that Smarty::$template_dir and Smarty::$config_dir are immutable
 *        the cache is killed every time either of the variables change. That means that two distinct Smarty objects with differing
 *        $template_dir or $config_dir should NOT share the same Smarty_Security instance,
 *        as this would lead to (severe) performance penalty! how should this be handled?
 */

/**
 * This class does contain the security settings
 */
class Smarty_Security
{
    /**
     * This determines how Smarty handles "<?php ... ?>" tags in templates.
     * possible values:
     * <ul>
     *   <li>Smarty::PHP_PASSTHRU -> echo PHP tags as they are</li>
     *   <li>Smarty::PHP_QUOTE    -> escape tags as entities</li>
     *   <li>Smarty::PHP_REMOVE   -> remove php tags</li>
     *   <li>Smarty::PHP_ALLOW    -> execute php tags</li>
     * </ul>
     *
     * @var integer
     */
    public $php_handling = Smarty::PHP_PASSTHRU;

    /**
     * This is the list of template directories that are considered secure.
     * $template_dir is in this list implicitly.
     *
     * @var array
     */
    public $secure_dir = array();

    /**
     * This is an array of directories where trusted php scripts reside.
     * {@link $security} is disabled during their inclusion/execution.
     *
     * @var array
     */
    public $trusted_dir = array();

    /**
     * List of regular expressions (PCRE) that include trusted URIs
     *
     * @var array
     */
    public $trusted_uri = array();

    /**
     * List of trusted constants names
     *
     * @var array
     */
    public $trusted_constants = array();

    /**
     * This is an array of trusted static classes.
     * If empty access to all static classes is allowed.
     * If set to 'none' none is allowed.
     *
     * @var array
     */
    public $static_classes = array();

    /**
     * This is an nested array of trusted classes and static methods.
     * If empty access to all static classes and methods is allowed.
     * Format:
     * array (
     *         'class_1' => array('method_1', 'method_2'), // allowed methods listed
     *         'class_2' => array(),                       // all methods of class allowed
     *       )
     * If set to null none is allowed.
     *
     * @var array
     */
    public $trusted_static_methods = array();

    /**
     * This is an array of trusted static properties.
     * If empty access to all static classes and properties is allowed.
     * Format:
     * array (
     *         'class_1' => array('prop_1', 'prop_2'), // allowed properties listed
     *         'class_2' => array(),                   // all properties of class allowed
     *       )
     * If set to null none is allowed.
     *
     * @var array
     */
    public $trusted_static_properties = array();

    /**
     * This is an array of trusted PHP functions.
     * If empty all functions are allowed.
     * To disable all PHP functions set $php_functions = null.
     *
     * @var array
     */
    public $php_functions = array('isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'time',);

    /**
     * This is an array of trusted PHP modifiers.
     * If empty all modifiers are allowed.
     * To disable all modifier set $php_modifiers = null.
     *
     * @var array
     */
    public $php_modifiers = array('escape', 'count', 'nl2br',);

    /**
     * This is an array of allowed tags.
     * If empty no restriction by allowed_tags.
     *
     * @var array
     */
    public $allowed_tags = array();

    /**
     * This is an array of disabled tags.
     * If empty no restriction by disabled_tags.
     *
     * @var array
     */
    public $disabled_tags = array();

    /**
     * This is an array of allowed modifier plugins.
     * If empty no restriction by allowed_modifiers.
     *
     * @var array
     */
    public $allowed_modifiers = array();

    /**
     * This is an array of disabled modifier plugins.
     * If empty no restriction by disabled_modifiers.
     *
     * @var array
     */
    public $disabled_modifiers = array();

    /**
     * This is an array of disabled special $smarty variables.
     *
     * @var array
     */
    public $disabled_special_smarty_vars = array();

    /**
     * This is an array of trusted streams.
     * If empty all streams are allowed.
     * To disable all streams set $streams = null.
     *
     * @var array
     */
    public $streams = array('file');

    /**
     * + flag if constants can be accessed from template
     *
     * @var boolean
     */
    public $allow_constants = true;

    /**
     * + flag if super globals can be accessed from template
     *
     * @var boolean
     */
    public $allow_super_globals = true;

    /**
     * max template nesting level
     *
     * @var int
     */
    public $max_template_nesting = 0;

    /**
     * current template nesting level
     *
     * @var int
     */
    private $_current_template_nesting = 0;

    /**
     * Cache for $resource_dir lookup
     *
     * @var array
     */
    protected $_resource_dir = array();

    /**
     * Cache for $template_dir lookup
     *
     * @var array
     */
    protected $_template_dir = array();

    /**
     * Cache for $config_dir lookup
     *
     * @var array
     */
    protected $_config_dir = array();

    /**
     * Cache for $secure_dir lookup
     *
     * @var array
     */
    protected $_secure_dir = array();

    /**
     * Cache for $php_resource_dir lookup
     *
     * @var array
     */
    protected $_php_resource_dir = null;

    /**
     * Cache for $trusted_dir lookup
     *
     * @var array
     */
    protected $_trusted_dir = null;

    /**
     * Cache for include path status
     *
     * @var bool
     */
    protected $_include_path_status = false;

    /**
     * Cache for $_include_array lookup
     *
     * @var array
     */
    protected $_include_dir = array();

    /**
     * @param Smarty $smarty
     */
    public function __construct($smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * Check if PHP function is trusted.
     *
     * @param  string $function_name
     * @param  object $compiler compiler object
     *
     * @return boolean                 true if function is trusted
     * @throws SmartyCompilerException if php function is not trusted
     */
    public function isTrustedPhpFunction($function_name, $compiler)
    {
        if (isset($this->php_functions) &&
            (empty($this->php_functions) || in_array($function_name, $this->php_functions))
        ) {
            return true;
        }

        $compiler->trigger_template_error("PHP function '{$function_name}' not allowed by security setting");

        return false; // should not, but who knows what happens to the compiler in the future?
    }

    /**
     * Check if static class is trusted.
     *
     * @param  string $class_name
     * @param  object $compiler compiler object
     *
     * @return boolean                 true if class is trusted
     * @throws SmartyCompilerException if static class is not trusted
     */
    public function isTrustedStaticClass($class_name, $compiler)
    {
        if (isset($this->static_classes) &&
            (empty($this->static_classes) || in_array($class_name, $this->static_classes))
        ) {
            return true;
        }

        $compiler->trigger_template_error("access to static class '{$class_name}' not allowed by security setting");

        return false; // should not, but who knows what happens to the compiler in the future?
    }

    /**
     * Check if static class method/property is trusted.
     *
     * @param  string $class_name
     * @param  string $params
     * @param  object $compiler compiler object
     *
     * @return boolean                 true if class method is trusted
     * @throws SmartyCompilerException if static class method is not trusted
     */
    public function isTrustedStaticClassAccess($class_name, $params, $compiler)
    {
        if (!isset($params[2])) {
            // fall back
            return $this->isTrustedStaticClass($class_name, $compiler);
        }
        if ($params[2] == 'method') {
            $allowed = $this->trusted_static_methods;
            $name = substr($params[0], 0, strpos($params[0], '('));
        } else {
            $allowed = $this->trusted_static_properties;
            // strip '$'
            $name = substr($params[0], 1);
        }
        if (isset($allowed)) {
            if (empty($allowed)) {
                // fall back
                return $this->isTrustedStaticClass($class_name, $compiler);
            }
            if (isset($allowed[$class_name]) &&
                (empty($allowed[$class_name]) || in_array($name, $allowed[$class_name]))
            ) {
                return true;
            }
        }
        $compiler->trigger_template_error("access to static class '{$class_name}' {$params[2]} '{$name}' not allowed by security setting");
        return false; // should not, but who knows what happens to the compiler in the future?
    }

    /**
     * Check if PHP modifier is trusted.
     *
     * @param  string $modifier_name
     * @param  object $compiler compiler object
     *
     * @return boolean                 true if modifier is trusted
     * @throws SmartyCompilerException if modifier is not trusted
     */
    public function isTrustedPhpModifier($modifier_name, $compiler)
    {
        if (isset($this->php_modifiers) &&
            (empty($this->php_modifiers) || in_array($modifier_name, $this->php_modifiers))
        ) {
            return true;
        }

        $compiler->trigger_template_error("modifier '{$modifier_name}' not allowed by security setting");

        return false; // should not, but who knows what happens to the compiler in the future?
    }

    /**
     * Check if tag is trusted.
     *
     * @param  string $tag_name
     * @param  object $compiler compiler object
     *
     * @return boolean                 true if tag is trusted
     * @throws SmartyCompilerException if modifier is not trusted
     */
    public function isTrustedTag($tag_name, $compiler)
    {
        // check for internal always required tags
        if (in_array($tag_name, array('assign', 'call', 'private_filter', 'private_block_plugin',
                                      'private_function_plugin', 'private_object_block_function',
                                      'private_object_function', 'private_registered_function',
                                      'private_registered_block', 'private_special_variable',
                                      'private_print_expression', 'private_modifier'))) {
            return true;
        }
        // check security settings
        if (empty($this->allowed_tags)) {
            if (empty($this->disabled_tags) || !in_array($tag_name, $this->disabled_tags)) {
                return true;
            } else {
                $compiler->trigger_template_error("tag '{$tag_name}' disabled by security setting", null, true);
            }
        } elseif (in_array($tag_name, $this->allowed_tags) && !in_array($tag_name, $this->disabled_tags)) {
            return true;
        } else {
            $compiler->trigger_template_error("tag '{$tag_name}' not allowed by security setting", null, true);
        }

        return false; // should not, but who knows what happens to the compiler in the future?
    }

    /**
     * Check if special $smarty variable is trusted.
     *
     * @param  string $var_name
     * @param  object $compiler compiler object
     *
     * @return boolean                 true if tag is trusted
     * @throws SmartyCompilerException if modifier is not trusted
     */
    public function isTrustedSpecialSmartyVar($var_name, $compiler)
    {
        if (!in_array($var_name, $this->disabled_special_smarty_vars)) {
            return true;
        } else {
            $compiler->trigger_template_error("special variable '\$smarty.{$var_name}' not allowed by security setting", null, true);
        }

        return false; // should not, but who knows what happens to the compiler in the future?
    }

    /**
     * Check if modifier plugin is trusted.
     *
     * @param  string $modifier_name
     * @param  object $compiler compiler object
     *
     * @return boolean                 true if tag is trusted
     * @throws SmartyCompilerException if modifier is not trusted
     */
    public function isTrustedModifier($modifier_name, $compiler)
    {
        // check for internal always allowed modifier
        if (in_array($modifier_name, array('default'))) {
            return true;
        }
        // check security settings
        if (empty($this->allowed_modifiers)) {
            if (empty($this->disabled_modifiers) || !in_array($modifier_name, $this->disabled_modifiers)) {
                return true;
            } else {
                $compiler->trigger_template_error("modifier '{$modifier_name}' disabled by security setting", null, true);
            }
        } elseif (in_array($modifier_name, $this->allowed_modifiers) &&
            !in_array($modifier_name, $this->disabled_modifiers)
        ) {
            return true;
        } else {
            $compiler->trigger_template_error("modifier '{$modifier_name}' not allowed by security setting", null, true);
        }

        return false; // should not, but who knows what happens to the compiler in the future?
    }

    /**
     * Check if constants are enabled or trusted
     *
     * @param  string $const    constant name
     * @param  object $compiler compiler object
     *
     * @return bool
     */
    public function isTrustedConstant($const, $compiler)
    {
        if (in_array($const, array('true', 'false', 'null'))) {
            return true;
        }
        if (!empty($this->trusted_constants)) {
            if (!in_array($const, $this->trusted_constants)) {
                $compiler->trigger_template_error("Security: access to constant '{$const}' not permitted");
                return false;
            }
            return true;
        }
        if ($this->allow_constants) {
            return true;
        }
        $compiler->trigger_template_error("Security: access to constants not permitted");
        return false;
    }

    /**
     * Check if stream is trusted.
     *
     * @param  string $stream_name
     *
     * @return boolean         true if stream is trusted
     * @throws SmartyException if stream is not trusted
     */
    public function isTrustedStream($stream_name)
    {
        if (isset($this->streams) && (empty($this->streams) || in_array($stream_name, $this->streams))) {
            return true;
        }

        throw new SmartyException("stream '{$stream_name}' not allowed by security setting");
    }

    /**
     * Check if directory of file resource is trusted.
     *
     * @param  string   $filepath
     * @param null|bool $isConfig
     *
     * @return bool true if directory is trusted
     * @throws \SmartyException if directory is not trusted
     */
    public function isTrustedResourceDir($filepath, $isConfig = null)
    {
        if ($this->_include_path_status !== $this->smarty->use_include_path) {
            foreach ($this->_include_dir as $directory) {
                unset($this->_resource_dir[$directory]);
            }
            if ($this->smarty->use_include_path) {
                $this->_include_dir = array();
                $_dirs = $this->smarty->ext->_getIncludePath->getIncludePathDirs($this->smarty);
                foreach ($_dirs as $directory) {
                    $this->_include_dir[] = $directory;
                    $this->_resource_dir[$directory] = true;
                }
            }
            $this->_include_path_status = $this->smarty->use_include_path;
        }
        if ($isConfig !== true &&
            (!isset($this->smarty->_cache['template_dir_new']) || $this->smarty->_cache['template_dir_new'])
        ) {
            $_dir = $this->smarty->getTemplateDir();
            if ($this->_template_dir !== $_dir) {
                foreach ($this->_template_dir as $directory) {
                    unset($this->_resource_dir[$directory]);
                }
                foreach ($_dir as $directory) {
                    $this->_resource_dir[$directory] = true;
                }
                $this->_template_dir = $_dir;
            }
            $this->smarty->_cache['template_dir_new'] = false;
        }
        if ($isConfig !== false &&
            (!isset($this->smarty->_cache['config_dir_new']) || $this->smarty->_cache['config_dir_new'])
        ) {
            $_dir = $this->smarty->getConfigDir();
            if ($this->_config_dir !== $_dir) {
                foreach ($this->_config_dir as $directory) {
                    unset($this->_resource_dir[$directory]);
                }
                foreach ($_dir as $directory) {
                    $this->_resource_dir[$directory] = true;
                }
                $this->_config_dir = $_dir;
            }
            $this->smarty->_cache['config_dir_new'] = false;
        }
        if ($this->_secure_dir !== (array) $this->secure_dir) {
            foreach ($this->_secure_dir as $directory) {
                unset($this->_resource_dir[$directory]);
            }
            foreach ((array) $this->secure_dir as $directory) {
                $directory = $this->smarty->_realpath($directory . DS, true);
                $this->_resource_dir[$directory] = true;
            }
            $this->_secure_dir = (array) $this->secure_dir;
        }
        $this->_resource_dir = $this->_checkDir($filepath, $this->_resource_dir);
        return true;
    }

    /**
     * Check if URI (e.g. {fetch} or {html_image}) is trusted
     * To simplify things, isTrustedUri() resolves all input to "{$PROTOCOL}://{$HOSTNAME}".
     * So "http://username:password@hello.world.example.org:8080/some-path?some=query-string"
     * is reduced to "http://hello.world.example.org" prior to applying the patters from {@link $trusted_uri}.
     *
     * @param  string $uri
     *
     * @return boolean         true if URI is trusted
     * @throws SmartyException if URI is not trusted
     * @uses $trusted_uri for list of patterns to match against $uri
     */
    public function isTrustedUri($uri)
    {
        $_uri = parse_url($uri);
        if (!empty($_uri['scheme']) && !empty($_uri['host'])) {
            $_uri = $_uri['scheme'] . '://' . $_uri['host'];
            foreach ($this->trusted_uri as $pattern) {
                if (preg_match($pattern, $_uri)) {
                    return true;
                }
            }
        }

        throw new SmartyException("URI '{$uri}' not allowed by security setting");
    }

    /**
     * Check if directory of file resource is trusted.
     *
     * @param  string $filepath
     *
     * @return boolean         true if directory is trusted
     * @throws SmartyException if PHP directory is not trusted
     */
    public function isTrustedPHPDir($filepath)
    {
        if (empty($this->trusted_dir)) {
            throw new SmartyException("directory '{$filepath}' not allowed by security setting (no trusted_dir specified)");
        }

        // check if index is outdated
        if (!$this->_trusted_dir || $this->_trusted_dir !== $this->trusted_dir) {
            $this->_php_resource_dir = array();

            $this->_trusted_dir = $this->trusted_dir;
            foreach ((array) $this->trusted_dir as $directory) {
                $directory = $this->smarty->_realpath($directory . DS, true);
                $this->_php_resource_dir[$directory] = true;
            }
        }

        $this->_php_resource_dir = $this->_checkDir($this->smarty->_realpath($filepath, true), $this->_php_resource_dir);
        return true;
    }

    /**
     * Start template processing
     *
     * @param $template
     *
     * @throws SmartyException
     */
    public function startTemplate($template)
    {
        if ($this->max_template_nesting > 0 && $this->_current_template_nesting ++ >= $this->max_template_nesting) {
            throw new SmartyException("maximum template nesting level of '{$this->max_template_nesting}' exceeded when calling '{$template->template_resource}'");
        }
    }

    /**
     * Exit template processing
     *
     * @internal param $template
     */
    public function exitTemplate()
    {
        if ($this->max_template_nesting > 0) {
            $this->_current_template_nesting --;
        }
    }

    /**
     * Check if file is inside a valid directory
     *
     * @param string $filepath
     * @param array  $dirs valid directories
     *
     * @return array
     * @throws \SmartyException
     */
    private function _checkDir($filepath, $dirs)
    {
        $directory = dirname($filepath) . DS;
        $_directory = array();
        while (true) {
            // remember the directory to add it to _resource_dir in case we're successful
            $_directory[$directory] = true;
            // test if the directory is trusted
            if (isset($dirs[$directory])) {
                // merge sub directories of current $directory into _resource_dir to speed up subsequent lookup
                $dirs = array_merge($dirs, $_directory);

                return $dirs;
            }
            // abort if we've reached root
            if (!preg_match('#[\\\/][^\\\/]+[\\\/]$#', $directory)) {
                break;
            }
            // bubble up one level
            $directory = preg_replace('#[\\\/][^\\\/]+[\\\/]$#', DS, $directory);
        }

        // give up
        throw new SmartyException("directory '{$filepath}' not allowed by security setting");
    }

    /**
     * Loads security class and enables security
     *
     * @param \Smarty                 $smarty
     * @param  string|Smarty_Security $security_class if a string is used, it must be class-name
     *
     * @return \Smarty current Smarty instance for chaining
     * @throws \SmartyException when an invalid class name is provided
     */
    public static function enableSecurity(Smarty $smarty, $security_class)
    {
        if ($security_class instanceof Smarty_Security) {
            $smarty->security_policy = $security_class;
            return;
        } elseif (is_object($security_class)) {
            throw new SmartyException("Class '" . get_class($security_class) . "' must extend Smarty_Security.");
        }
        if ($security_class == null) {
            $security_class = $smarty->security_class;
        }
        if (!class_exists($security_class)) {
            throw new SmartyException("Security class '$security_class' is not defined");
        } elseif ($security_class !== 'Smarty_Security' && !is_subclass_of($security_class, 'Smarty_Security')) {
            throw new SmartyException("Class '$security_class' must extend Smarty_Security.");
        } else {
            $smarty->security_policy = new $security_class($smarty);
        }
        return;
    }
}
