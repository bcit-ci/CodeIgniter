<?php
/**
 * Smarty Internal Plugin Resource File
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Uwe Tews
 * @author     Rodney Rehm
 */

/**
 * Smarty Internal Plugin Resource File
 * Implements the file system as resource for Smarty templates
 *
 * @package    Smarty
 * @subpackage TemplateResources
 */
class Smarty_Internal_Resource_File extends Smarty_Resource
{
    /**
     * build template filepath by traversing the template_dir array
     *
     * @param Smarty_Template_Source    $source    source object
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return string fully qualified filepath
     * @throws SmartyException
     */
    protected function buildFilepath(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $file = $source->name;
        // absolute file ?
        if ($file[0] == '/' || $file[1] == ':') {
            $file = $source->smarty->_realpath($file, true);
            return is_file($file) ? $file : false;
        }
        // go relative to a given template?
        if ($file[0] == '.' && $_template && isset($_template->parent) && $_template->parent->_objType == 2 &&
            preg_match('#^[.]{1,2}[\\\/]#', $file)
        ) {
            if ($_template->parent->source->type != 'file' && $_template->parent->source->type != 'extends' &&
                !isset($_template->parent->_cache['allow_relative_path'])
            ) {
                throw new SmartyException("Template '{$file}' cannot be relative to template of resource type '{$_template->parent->source->type}'");
            }
            $path = dirname($_template->parent->source->filepath) . DS . $file;
            // normalize path
            $path = $source->smarty->_realpath($path);
            // files relative to a template only get one shot
            return is_file($path) ? $path : false;
        }
        // normalize DS
        if (strpos($file, DS == '/' ? '\\' : '/') !== false) {
            $file = str_replace(DS == '/' ? '\\' : '/', DS, $file);
        }

        $_directories = $source->smarty->getTemplateDir(null, $source->isConfig);
        // template_dir index?
        if ($file[0] == '[' && preg_match('#^\[([^\]]+)\](.+)$#', $file, $fileMatch)) {
            $file = $fileMatch[2];
            $_indices = explode(',', $fileMatch[1]);
            $_index_dirs = array();
            foreach ($_indices as $index) {
                $index = trim($index);
                // try string indexes
                if (isset($_directories[$index])) {
                    $_index_dirs[] = $_directories[$index];
                } elseif (is_numeric($index)) {
                    // try numeric index
                    $index = (int) $index;
                    if (isset($_directories[$index])) {
                        $_index_dirs[] = $_directories[$index];
                    } else {
                        // try at location index
                        $keys = array_keys($_directories);
                        if (isset($_directories[$keys[$index]])) {
                            $_index_dirs[] = $_directories[$keys[$index]];
                        }
                    }
                }
            }
            if (empty($_index_dirs)) {
                // index not found
                return false;
            } else {
                $_directories = $_index_dirs;
            }
        }

        // relative file name?
        foreach ($_directories as $_directory) {
            $path = $_directory . $file;
            if (is_file($path)) {
                return (strpos($path, '.' . DS) !== false) ? $source->smarty->_realpath($path) : $path;
            }
        }
        if (!isset($_index_dirs)) {
            // Could be relative to cwd
            $path = $source->smarty->_realpath($file, true);
            if (is_file($path)) {
                return $path;
            }
        }
        // Use include path ?
        if ($source->smarty->use_include_path) {
            return $source->smarty->ext->_getIncludePath->getIncludePath($_directories, $file, $source->smarty);
        }
        return false;
    }

    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
    {
        $source->filepath = $this->buildFilepath($source, $_template);

        if ($source->filepath !== false) {
            if (isset($source->smarty->security_policy) && is_object($source->smarty->security_policy)) {
                $source->smarty->security_policy->isTrustedResourceDir($source->filepath, $source->isConfig);
            }
            $source->exists = true;
            $source->uid = sha1($source->filepath);
            if ($source->smarty->compile_check == 1) {
                $source->timestamp = filemtime($source->filepath);
            }
        } else {
            $source->timestamp = false;
            $source->exists = false;
        }
    }

    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        if (!$source->exists) {
            $source->timestamp = $source->exists = is_file($source->filepath);
        }
        if ($source->exists) {
            $source->timestamp = filemtime($source->filepath);
        }
    }

    /**
     * Load template's source from file into current template object
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 template source
     * @throws SmartyException        if source cannot be loaded
     */
    public function getContent(Smarty_Template_Source $source)
    {
        if ($source->exists) {
            return file_get_contents($source->filepath);
        }
        throw new SmartyException('Unable to read ' . ($source->isConfig ? 'config' : 'template') .
                                  " {$source->type} '{$source->name}'");
    }

    /**
     * Determine basename for compiled filename
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 resource's basename
     */
    public function getBasename(Smarty_Template_Source $source)
    {
        return basename($source->filepath);
    }
}
