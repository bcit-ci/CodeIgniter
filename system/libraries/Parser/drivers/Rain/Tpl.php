<?php

namespace Rain;

require_once 'Tpl/PluginContainer.php';
require_once 'Tpl/Plugin.php';
require_once 'Tpl/Exception.php';
require_once 'Tpl/SyntaxException.php';
require_once 'Tpl/NotFoundException.php';

/**
 *  RainTPL
 *  --------
 *  Realized by Federico Ulfo & maintained by the Rain Team
 *  Distributed under GNU/LGPL 3 License
 *
 *  @version 3.0 Alpha milestone: https://github.com/rainphp/raintpl3/issues/milestones?with_issues=no
 */
class Tpl {

    // variables
    public $var = array();

    /**
     * Plugin container
     *
     * @var \Rain\Tpl\PluginContainer
     */
    protected static $plugins = null;
    // configuration
    protected static $conf = array(
        'checksum' => array(),
        'charset' => 'UTF-8',
        'debug' => FALSE,
        'tpl_dir' => 'templates/',
        'cache_dir' => 'cache/',
        'tpl_ext' => 'html',
        'base_url' => '',
        'php_enabled' => false,
        'template_syntax' => 'Rain',
        'registered_tags' => array(),
        'auto_escape' => TRUE,
        'tags' => array(
            'loop' => array('({loop.*?})', '/{loop="(?<variable>\${0,1}[^"]*)"(?: as (?<key>\$.*?)(?: => (?<value>\$.*?)){0,1}){0,1}}/'),
            'loop_close' => array('({\/loop})', '/{\/loop}/'),
            'loop_break' => array('({break})', '/{break}/'),
            'loop_continue' => array('({continue})', '/{continue}/'),
            'if' => array('({if.*?})', '/{if="([^"]*)"}/'),
            'elseif' => array('({elseif.*?})', '/{elseif="([^"]*)"}/'),
            'else' => array('({else})', '/{else}/'),
            'if_close' => array('({\/if})', '/{\/if}/'),
            'noparse' => array('({noparse})', '/{noparse}/'),
            'noparse_close' => array('({\/noparse})', '/{\/noparse}/'),
            'ignore' => array('({ignore}|{\*)', '/{ignore}|{\*/'),
            'ignore_close' => array('({\/ignore}|\*})', '/{\/ignore}|\*}/'),
            'include' => array('({include.*?})', '/{include="([^"]*)"}/'),
            'function' => array('({function.*?})', '/{function="([a-zA-Z_][a-zA-Z_0-9\:]*)(\(.*\)){0,1}"}/'),
            'variable' => array('({\$.*?})', '/{(\$.*?)}/'),
            'constant' => array('({#.*?})', '/{#(.*?)#{0,1}}/'),
        ),
        'sandbox' => true,
        'black_list' => array('exec', 'shell_exec', 'pcntl_exec', 'passthru', 'proc_open', 'system', 'posix_kill', 'posix_setsid', 'pcntl_fork', 'posix_uname', 'php_uname',
            'phpinfo', 'popen', 'file_get_contents', 'file_put_contents', 'rmdir', 'mkdir', 'unlink', 'highlight_contents', 'symlink', 'apache_child_terminate',
            'apache_setenv', 'define_syslog_variables', 'escapeshellarg', 'escapeshellcmd', 'eval', 'fp', 'fput', 'ftp_connect', 'ftp_exec', 'ftp_get',
            'ftp_login', 'ftp_nb_fput', 'ftp_put', 'ftp_raw', 'ftp_rawlist', 'highlight_file', 'ini_alter', 'ini_get_all', 'ini_restore', 'inject_code',
            'mysql_pconnect', 'openlog', 'passthru', 'php_uname', 'phpAds_remoteInfo', 'phpAds_XmlRpc', 'phpAds_xmlrpcDecode', 'phpAds_xmlrpcEncode',
            'posix_getpwuid', 'posix_kill', 'posix_mkfifo', 'posix_setpgid', 'posix_setsid', 'posix_setuid', 'posix_uname', 'proc_close', 'proc_get_status',
            'proc_nice', 'proc_open', 'proc_terminate', 'syslog', 'xmlrpc_entity_decode'
        ),
    );
    protected $templateInfo = array();
    
    /**
     * Draw the template
     */
    public function draw($templateFilePath, $toString = FALSE) {
        extract($this->var);
        ob_start();
        require $this->checkTemplate($templateFilePath);
        $html = ob_get_clean();
        if ($toString)
            return $html; 
        else
            echo $html;
    }

    /**
     * Draw the template
     */
    public function drawString($string, $toString = false) {
        extract($this->var);
        ob_start();
        require $this->checkString($string);
        $html = ob_get_clean();
        if ($toString)
            return $html; 
        else
            echo $html;
    }

    /**
     * Configure the template
     */
    public static function configure($setting, $value = null) {
        if (is_array($setting))
            foreach ($setting as $key => $value)
                static::configure($key, $value);
        else if (isset(static::$conf[$setting])) {
            static::$conf[$setting] = $value;

            static::$conf['checksum'][$setting] = $value; // take trace of all config
        }
    }

    /**
     * Assign variable
     * eg. 	$t->assign('name','mickey');
     *
     * @param mixed $variable Name of template variable or associative array name/value
     * @param mixed $value value assigned to this variable. Not set if variable_name is an associative array
     */
    public function assign($variable, $value = null) {
        if (is_array($variable))
            $this->var = $variable + $this->var;
        else
            $this->var[$variable] = $value;
        // return this for FluentInterface, example new Tpl()->assign('title','name')->draw('template');
        return $this;
    }

    /**
     * Clean the expired files from cache
     * @param type $expireTime Set the expiration time
     */
    public static function clean($expireTime = 2592000) {
        $files = glob(static::$conf['cache_dir'] . "*.rtpl.php");
        $time = time();
        foreach ($files as $file)
            if ($time - filemtime($file) > $expireTime)
                unlink($file);
    }

    public static function registerTag($tag, $parse, $function) {
        static::$conf['registered_tags'][$tag] = array("parse" => $parse, "function" => $function);
    }

    /**
     * Registers a plugin globally.
     *
     * @param \Rain\Tpl\IPlugin $plugin
     * @param string $name name can be used to distinguish plugins of same class.
     */
    public static function registerPlugin(\Rain\Tpl\IPlugin $plugin, $name = '') {
        if ('' === $name) {
            $name = \get_class($plugin);
        }
        static::getPlugins()->addPlugin($name, $plugin);
    }

    /**
     * Removes registered plugin from stack.
     *
     * @param string $name
     */
    public static function removePlugin($name) {
        static::getPlugins()->removePlugin($name);
    }

    /**
     * Returns plugin container.
     *
     * @return \Rain\Tpl\PluginContainer
     */
    protected static function getPlugins() {
        if (is_null(static::$plugins)) {
            static::$plugins = new \Rain\Tpl\PluginContainer();
        }
        return static::$plugins;
    }

    protected function checkTemplate($template) {
        // set filename
        $templateName = basename($template);
        $templateBasedir = strpos($template, DIRECTORY_SEPARATOR) ? dirname($template) . DIRECTORY_SEPARATOR : null;
        $templateDirectory = static::$conf['tpl_dir'] . $templateBasedir;
        $templateFilepath = $templateDirectory . $templateName . '.' . static::$conf['tpl_ext'];
        $parsedTemplateFilepath = static::$conf['cache_dir'] . $templateName . "." . md5($templateDirectory . serialize(static::$conf['checksum'])) . '.rtpl.php';

        // if the template doesn't exsist throw an error
        if (!file_exists($templateFilepath)) {
            $e = new Tpl\NotFoundException('Template ' . $templateName . ' not found!');
            throw $e->templateFile($templateFilepath);
        }

        // Compile the template if the original has been updated
        if (static::$conf['debug'] || !file_exists($parsedTemplateFilepath) || ( filemtime($parsedTemplateFilepath) < filemtime($templateFilepath) ))
            $this->compileFile($templateName, $templateBasedir, $templateDirectory, $templateFilepath, $parsedTemplateFilepath);

        return $parsedTemplateFilepath;
    }

    /**
     * Check if a string has been already compiled
     * @param type $string
     */
    protected function checkString($string) {

        // set filename
        $templateName = md5($string . implode(static::$conf['checksum']));
        $parsedTemplateFilepath = static::$conf['cache_dir'] . $templateName . '.s.rtpl.php';
        $templateFilepath = '';
        $templateBasedir = '';


        // Compile the template if the original has been updated
        if (static::$conf['debug'] || !file_exists($parsedTemplateFilepath))
            $this->compileString($templateName, $templateBasedir, $templateFilepath, $parsedTemplateFilepath, $string);

        return $parsedTemplateFilepath;
    }

    /**
     * Compile the file
     */
    protected function compileFile($templateName, $templateBasedir, $templateDirectory, $templateFilepath, $parsedTemplateFilepath) {

        // open the template
        $fp = fopen($templateFilepath, "r");

        // lock the file
        if (flock($fp, LOCK_SH)) {

            // save the filepath in the info
            $this->templateInfo['template_filepath'] = $templateFilepath;

            // read the file			
            $this->templateInfo['code'] = $code = fread($fp, filesize($templateFilepath));

            // xml substitution
            $code = preg_replace("/<\?xml(.*?)\?>/s", "##XML\\1XML##", $code);

            // disable php tag
            if (!static::$conf['php_enabled'])
                $code = str_replace(array("<?", "?>"), array("&lt;?", "?&gt;"), $code);

            // xml re-substitution
            $code = preg_replace_callback("/##XML(.*?)XML##/s", function( $match ) {
                        return "<?php echo '<?xml " . stripslashes($match[1]) . " ?>'; ?>";
                    }, $code);

            $parsedCode = $this->compileTemplate($code, $isString = false, $templateBasedir, $templateDirectory, $templateFilepath);
            $parsedCode = "<?php if(!class_exists('Rain\Tpl')){exit;}?>" . $parsedCode;

            // fix the php-eating-newline-after-closing-tag-problem
            $parsedCode = str_replace("?>\n", "?>\n\n", $parsedCode);

            // create directories
            if (!is_dir(static::$conf['cache_dir']))
                mkdir(static::$conf['cache_dir'], 0755, TRUE);

            // check if the cache is writable
            if (!is_writable(static::$conf['cache_dir']))
                throw new Tpl\Exception('Cache directory ' . static::$conf['cache_dir'] . 'doesn\'t have write permission. Set write permission or set RAINTPL_CHECK_TEMPLATE_UPDATE to FALSE. More details on http://www.raintpl.com/Documentation/Documentation-for-PHP-developers/Configuration/');

            // write compiled file
            file_put_contents($parsedTemplateFilepath, $parsedCode);

            // release the file lock
            flock($fp, LOCK_UN);
        }

        // close the file
        fclose($fp);
    }

    /**
     * Compile the file
     */
    protected function compileString($templateName, $templateBasedir, $templateFilepath, $parsedTemplateFilepath, $code) {

        // open the template
        $fp = fopen($parsedTemplateFilepath, "w");

        // lock the file
        if (flock($fp, LOCK_SH)) {

            // xml substitution
            $code = preg_replace("/<\?xml(.*?)\?>/s", "##XML\\1XML##", $code);

            // disable php tag
            if (!static::$conf['php_enabled'])
                $code = str_replace(array("<?", "?>"), array("&lt;?", "?&gt;"), $code);

            // xml re-substitution
            $code = preg_replace_callback("/##XML(.*?)XML##/s", function( $match ) {
                        return "<?php echo '<?xml " . stripslashes($match[1]) . " ?>'; ?>";
                    }, $code);

            $parsedCode = $this->compileTemplate($code, $isString = true, $templateBasedir, $templateDirectory = null, $templateFilepath);

            $parsedCode = "<?php if(!class_exists('Rain\Tpl')){exit;}?>" . $parsedCode;

            // fix the php-eating-newline-after-closing-tag-problem
            $parsedCode = str_replace("?>\n", "?>\n\n", $parsedCode);

            // create directories
            if (!is_dir(static::$conf['cache_dir']))
                mkdir(static::$conf['cache_dir'], 0755, true);

            // check if the cache is writable
            if (!is_writable(static::$conf['cache_dir']))
                throw new Tpl\Exception('Cache directory ' . static::$conf['cache_dir'] . 'doesn\'t have write permission. Set write permission or set RAINTPL_CHECK_TEMPLATE_UPDATE to false. More details on http://www.raintpl.com/Documentation/Documentation-for-PHP-developers/Configuration/');

            // write compiled file
            fwrite($fp, $parsedCode);

            // release the file lock
            flock($fp, LOCK_UN);
        }

        // close the file
        fclose($fp);
    }

    /**
     * Compile template
     * @access protected
     */
    protected function compileTemplate($code, $isString, $templateBasedir, $templateDirectory, $templateFilepath) {

        // Execute plugins, before_parse
        $context = $this->getPlugins()->createContext(array(
            'code' => $code,
            'template_basedir' => $templateBasedir,
            'template_filepath' => $templateFilepath,
            'conf' => static::$conf,
                ));
        $this->getPlugins()->run('beforeParse', $context);
        $code = $context->code;

        // set tags
        foreach (static::$conf['tags'] as $tag => $tagArray) {
            list( $split, $match ) = $tagArray;
            $tagSplit[$tag] = $split;
            $tagMatch[$tag] = $match;
        }

        $keys = array_keys(static::$conf['registered_tags']);
        $tagSplit += array_merge($tagSplit, $keys);


        //split the code with the tags regexp
        $codeSplit = preg_split("/" . implode("|", $tagSplit) . "/", $code, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        //variables initialization
        $parsedCode = $comentIsOpen = $ignoreIsOpen = NULL;
        $openIf = $loopLevel = 0;

        // if the template is not empty
        if ($codeSplit)

        //read all parsed code
            foreach ($codeSplit as $html) {

                //close ignore tag
                if (!$comentIsOpen && preg_match($tagMatch['ignore_close'], $html))
                    $ignoreIsOpen = FALSE;

                //code between tag ignore id deleted
                elseif ($ignoreIsOpen) {
                    //ignore the code
                }

                //close no parse tag
                elseif (preg_match($tagMatch['noparse_close'], $html))
                    $comentIsOpen = FALSE;

                //code between tag noparse is not compiled
                elseif ($comentIsOpen)
                    $parsedCode .= $html;

                //ignore
                elseif (preg_match($tagMatch['ignore'], $html))
                    $ignoreIsOpen = TRUE;

                //noparse
                elseif (preg_match($tagMatch['noparse'], $html))
                    $comentIsOpen = TRUE;

                //include tag
                elseif (preg_match($tagMatch['include'], $html, $matches)) {

                    //get the folder of the actual template
                    $actualFolder = substr($templateDirectory, strlen(static::$conf['tpl_dir']));

                    //get the included template
                    $includeTemplate = $actualFolder . $this->varReplace($matches[1], $loopLevel);

                    // reduce the path
                    $includeTemplate = preg_replace('/\w+\/\.\.\//', '', $includeTemplate);
                    
                    //dynamic include
                    $parsedCode .= '<?php require $this->checkTemplate("' . $includeTemplate . '");?>';

                }

                //loop
                elseif (preg_match($tagMatch['loop'], $html, $matches)) {

                    // increase the loop counter
                    $loopLevel++;

                    //replace the variable in the loop
                    $var = $this->varReplace($matches['variable'], $loopLevel - 1, $escape = FALSE);
                    if (preg_match('#\(#', $var)) {
                        $newvar = "\$newvar{$loopLevel}";
                        $assignNewVar = "$newvar=$var;";
                    } else {
                        $newvar = $var;
                        $assignNewVar = null;
                    }

                    // check black list
                    $this->blackList($var);

                    //loop variables
                    $counter = "\$counter$loopLevel";       // count iteration

                    if (isset($matches['key']) && isset($matches['value'])) {
                        $key = $matches['key'];
                        $value = $matches['value'];
                    } elseif (isset($matches['key'])) {
                        $key = "\$key$loopLevel";               // key
                        $value = $matches['key'];
                    } else {
                        $key = "\$key$loopLevel";               // key
                        $value = "\$value$loopLevel";           // value
                    }



                    //loop code
                    $parsedCode .= "<?php $counter=-1; $assignNewVar if( isset($newvar) && ( is_array($newvar) || $newvar instanceof Traversable ) && sizeof($newvar) ) foreach( $newvar as $key => $value ){ $counter++; ?>";
                }

                //close loop tag
                elseif (preg_match($tagMatch['loop_close'], $html)) {

                    //iterator
                    $counter = "\$counter$loopLevel";

                    //decrease the loop counter
                    $loopLevel--;

                    //close loop code
                    $parsedCode .= "<?php } ?>";
                }

                //break loop tag
                elseif (preg_match($tagMatch['loop_break'], $html)) {
                    //close loop code
                    $parsedCode .= "<?php break; ?>";
                }

                //continue loop tag
                elseif (preg_match($tagMatch['loop_continue'], $html)) {
                    //close loop code
                    $parsedCode .= "<?php continue; ?>";
                }

                //if
                elseif (preg_match($tagMatch['if'], $html, $matches)) {

                    //increase open if counter (for intendation)
                    $openIf++;

                    //tag
                    $tag = $matches[0];

                    //condition attribute
                    $condition = $matches[1];

                    // check black list
                    $this->blackList($condition);

                    //variable substitution into condition (no delimiter into the condition)
                    $parsedCondition = $this->varReplace($condition, $loopLevel, $escape = FALSE);

                    //if code
                    $parsedCode .= "<?php if( $parsedCondition ){ ?>";
                }

                //elseif
                elseif (preg_match($tagMatch['elseif'], $html, $matches)) {

                    //tag
                    $tag = $matches[0];

                    //condition attribute
                    $condition = $matches[1];

                    // check black list
                    $this->blackList($condition);

                    //variable substitution into condition (no delimiter into the condition)
                    $parsedCondition = $this->varReplace($condition, $loopLevel, $escape = FALSE);

                    //elseif code
                    $parsedCode .= "<?php }elseif( $parsedCondition ){ ?>";
                }

                //else
                elseif (preg_match($tagMatch['else'], $html)) {

                    //else code
                    $parsedCode .= '<?php }else{ ?>';
                }

                //close if tag
                elseif (preg_match($tagMatch['if_close'], $html)) {

                    //decrease if counter
                    $openIf--;

                    // close if code
                    $parsedCode .= '<?php } ?>';
                }

                // function
                elseif (preg_match($tagMatch['function'], $html, $matches)) {

                    // get function
                    $function = $matches[1];

                    // var replace
                    if (isset($matches[2]))
                        $parsedFunction = $function . $this->varReplace($matches[2], $loopLevel, $escape = FALSE, $echo = FALSE);
                    else
                        $parsedFunction = $function . "()";

                    // check black list
                    $this->blackList($parsedFunction);

                    // function 
                    $parsedCode .= "<?php echo $parsedFunction; ?>";
                }

                //variables
                elseif (preg_match($tagMatch['variable'], $html, $matches)) {
                    //variables substitution (es. {$title})
                    $parsedCode .= "<?php " . $this->varReplace($matches[1], $loopLevel, $escape = TRUE, $echo = TRUE) . "; ?>";
                }


                //constants
                elseif (preg_match($tagMatch['constant'], $html, $matches)) {
                    $parsedCode .= "<?php echo " . $this->conReplace($matches[1], $loopLevel) . "; ?>";
                }
                // registered tags
                else {
                    
                    $found = FALSE;                   
                    foreach (static::$conf['registered_tags'] as $tags => $array) {
                        if (preg_match_all('/' . $array['parse'] . '/', $html, $matches)) {
                            $found = true;
                            $parsedCode .= "<?php echo call_user_func( static::\$conf['registered_tags']['$tags']['function'], " . var_export($matches, 1) . " ); ?>";
                        }
                    }

                    if (!$found){
                        $parsedCode .= $html;
                    }
                }
            }


        if ($isString) {
            if ($openIf > 0) {

                $trace = debug_backtrace();
                $caller = array_shift($trace);

                $e = new Tpl\SyntaxException("Error! You need to close an {if} tag in the string, loaded by {$caller['file']} at line {$caller['line']}");
                throw $e->templateFile($templateFilepath);
            }

            if ($loopLevel > 0) {

                $trace = debug_backtrace();
                $caller = array_shift($trace);
                $e = new Tpl\SyntaxException("Error! You need to close the {loop} tag in the string, loaded by {$caller['file']} at line {$caller['line']}");
                throw $e->templateFile($templateFilepath);
            }
        } else {
            if ($openIf > 0) {
                $e = new Tpl\SyntaxException("Error! You need to close an {if} tag in $templateFilepath template");
                throw $e->templateFile($templateFilepath);
            }

            if ($loopLevel > 0) {
                $e = new Tpl\SyntaxException("Error! You need to close the {loop} tag in $templateFilepath template");
                throw $e->templateFile($templateFilepath);
            }
        }

        // Execute plugins, after_parse
        $context->code = $parsedCode;
        $this->getPlugins()->run('afterParse', $context);

        return $context->code;
    }

    protected function varReplace($html, $loopLevel = NULL, $escape = TRUE, $echo = FALSE) {

        // change variable name if loop level
        if (!empty($loopLevel))
            $html = preg_replace(array('/(\$key)\b/', '/(\$value)\b/', '/(\$counter)\b/'), array('${1}' . $loopLevel, '${1}' . $loopLevel, '${1}' . $loopLevel), $html);

        // if it is a variable
        if (preg_match_all('/(\$[a-z_A-Z][^\s]*)/', $html, $matches)) {
            // substitute . and [] with [" "]
            for ($i = 0; $i < count($matches[1]); $i++) {

                $rep = preg_replace('/\[(\${0,1}[a-zA-Z_0-9]*)\]/', '["$1"]', $matches[1][$i]);
                $rep = preg_replace('/\.(\${0,1}[a-zA-Z_0-9]*)/', '["$1"]', $rep);
                $html = str_replace($matches[0][$i], $rep, $html);
            }

            // update modifier
            $html = $this->modifierReplace($html);

            // if does not initialize a value, e.g. {$a = 1}
            if (!preg_match('/\$.*=.*/', $html)) {

                // escape character
                if (static::$conf['auto_escape'] && $escape)
                //$html = "htmlspecialchars( $html )";
                    $html = "htmlspecialchars( $html, ENT_COMPAT, '" . static::$conf['charset'] . "', FALSE )";

                // if is an assignment it doesn't add echo
                if ($echo)
                    $html = "echo " . $html;
            }
        }

        return $html;
    }

    protected function conReplace($html) {
        $html = $this->modifierReplace($html);
        return $html;
    }

    protected function modifierReplace($html) {

        if ($pos = strrpos($html, "|")) {

            // check black list
            $this->blackList($html);

            $explode = explode(":", substr($html, $pos + 1));
            $function = $explode[0];
            $params = isset($explode[1]) ? "," . $explode[1] : null;

            $html = $function . "(" . $this->modifierReplace(substr($html, 0, $pos)) . "$params)";
        }

        return $html;
    }

    protected function blackList($html) {

        if (!self::$conf['sandbox'] || !self::$conf['black_list'])
            return true;

        if (empty(self::$conf['black_list_preg']))
            self::$conf['black_list_preg'] = '#[\W\s]*' . implode('[\W\s]*|[\W\s]*', self::$conf['black_list']) . '[\W\s]*#';

        // check if the function is in the black list (or not in white list)
        if (preg_match(self::$conf['black_list_preg'], $html, $match)) {

            // find the line of the error
            $line = 0;
            $rows = explode("\n", $this->templateInfo['code']);
            while (!strpos($rows[$line], $html) && $line + 1 < count($rows))
                $line++;

            // stop the execution of the script
            $e = new Tpl\SyntaxException('Syntax ' . $match[0] . ' not allowed in template: ' . $this->templateInfo['template_filepath'] . ' at line ' . $line);
            throw $e->templateFile($this->templateInfo['template_filepath'])
                    ->tag($match[0])
                    ->templateLine($line);

            return false;
        }
    }

}

// -- end
