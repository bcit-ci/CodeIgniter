<?php
/**
 * Smarty Internal TestInstall
 * Test Smarty installation
 *
 * @package    Smarty
 * @subpackage Utilities
 * @author     Uwe Tews
 */

/**
 * TestInstall class
 *
 * @package    Smarty
 * @subpackage Utilities
 */
class Smarty_Internal_TestInstall
{
    /**
     * diagnose Smarty setup
     * If $errors is secified, the diagnostic report will be appended to the array, rather than being output.
     *
     * @param  array $errors array to push results into rather than outputting them
     *
     * @return bool   status, true if everything is fine, false else
     */
    public static function testInstall(Smarty $smarty, &$errors = null)
    {
        $status = true;

        if ($errors === null) {
            echo "<PRE>\n";
            echo "Smarty Installation test...\n";
            echo "Testing template directory...\n";
        }

        $_stream_resolve_include_path = function_exists('stream_resolve_include_path');

        // test if all registered template_dir are accessible
        foreach ($smarty->getTemplateDir() as $template_dir) {
            $_template_dir = $template_dir;
            $template_dir = realpath($template_dir);
            // resolve include_path or fail existence
            if (!$template_dir) {
                if ($smarty->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_template_dir)) {
                    // try PHP include_path
                    if ($_stream_resolve_include_path) {
                        $template_dir = stream_resolve_include_path($_template_dir);
                    } else {
                        $template_dir = $smarty->ext->_getIncludePath->getIncludePath($_template_dir, null, $smarty);
                    }

                    if ($template_dir !== false) {
                        if ($errors === null) {
                            echo "$template_dir is OK.\n";
                        }

                        continue;
                    } else {
                        $status = false;
                        $message =
                            "FAILED: $_template_dir does not exist (and couldn't be found in include_path either)";
                        if ($errors === null) {
                            echo $message . ".\n";
                        } else {
                            $errors[ 'template_dir' ] = $message;
                        }

                        continue;
                    }
                } else {
                    $status = false;
                    $message = "FAILED: $_template_dir does not exist";
                    if ($errors === null) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'template_dir' ] = $message;
                    }

                    continue;
                }
            }

            if (!is_dir($template_dir)) {
                $status = false;
                $message = "FAILED: $template_dir is not a directory";
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'template_dir' ] = $message;
                }
            } elseif (!is_readable($template_dir)) {
                $status = false;
                $message = "FAILED: $template_dir is not readable";
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'template_dir' ] = $message;
                }
            } else {
                if ($errors === null) {
                    echo "$template_dir is OK.\n";
                }
            }
        }

        if ($errors === null) {
            echo "Testing compile directory...\n";
        }

        // test if registered compile_dir is accessible
        $__compile_dir = $smarty->getCompileDir();
        $_compile_dir = realpath($__compile_dir);
        if (!$_compile_dir) {
            $status = false;
            $message = "FAILED: {$__compile_dir} does not exist";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'compile_dir' ] = $message;
            }
        } elseif (!is_dir($_compile_dir)) {
            $status = false;
            $message = "FAILED: {$_compile_dir} is not a directory";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'compile_dir' ] = $message;
            }
        } elseif (!is_readable($_compile_dir)) {
            $status = false;
            $message = "FAILED: {$_compile_dir} is not readable";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'compile_dir' ] = $message;
            }
        } elseif (!is_writable($_compile_dir)) {
            $status = false;
            $message = "FAILED: {$_compile_dir} is not writable";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'compile_dir' ] = $message;
            }
        } else {
            if ($errors === null) {
                echo "{$_compile_dir} is OK.\n";
            }
        }

        if ($errors === null) {
            echo "Testing plugins directory...\n";
        }

        // test if all registered plugins_dir are accessible
        // and if core plugins directory is still registered
        $_core_plugins_dir = realpath(dirname(__FILE__) . '/../plugins');
        $_core_plugins_available = false;
        foreach ($smarty->getPluginsDir() as $plugin_dir) {
            $_plugin_dir = $plugin_dir;
            $plugin_dir = realpath($plugin_dir);
            // resolve include_path or fail existence
            if (!$plugin_dir) {
                if ($smarty->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {
                    // try PHP include_path
                    if ($_stream_resolve_include_path) {
                        $plugin_dir = stream_resolve_include_path($_plugin_dir);
                    } else {
                        $plugin_dir = $smarty->ext->_getIncludePath->getIncludePath($_plugin_dir, null, $smarty);
                    }

                    if ($plugin_dir !== false) {
                        if ($errors === null) {
                            echo "$plugin_dir is OK.\n";
                        }

                        continue;
                    } else {
                        $status = false;
                        $message = "FAILED: $_plugin_dir does not exist (and couldn't be found in include_path either)";
                        if ($errors === null) {
                            echo $message . ".\n";
                        } else {
                            $errors[ 'plugins_dir' ] = $message;
                        }

                        continue;
                    }
                } else {
                    $status = false;
                    $message = "FAILED: $_plugin_dir does not exist";
                    if ($errors === null) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'plugins_dir' ] = $message;
                    }

                    continue;
                }
            }

            if (!is_dir($plugin_dir)) {
                $status = false;
                $message = "FAILED: $plugin_dir is not a directory";
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'plugins_dir' ] = $message;
                }
            } elseif (!is_readable($plugin_dir)) {
                $status = false;
                $message = "FAILED: $plugin_dir is not readable";
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'plugins_dir' ] = $message;
                }
            } elseif ($_core_plugins_dir && $_core_plugins_dir == realpath($plugin_dir)) {
                $_core_plugins_available = true;
                if ($errors === null) {
                    echo "$plugin_dir is OK.\n";
                }
            } else {
                if ($errors === null) {
                    echo "$plugin_dir is OK.\n";
                }
            }
        }
        if (!$_core_plugins_available) {
            $status = false;
            $message = "WARNING: Smarty's own libs/plugins is not available";
            if ($errors === null) {
                echo $message . ".\n";
            } elseif (!isset($errors[ 'plugins_dir' ])) {
                $errors[ 'plugins_dir' ] = $message;
            }
        }

        if ($errors === null) {
            echo "Testing cache directory...\n";
        }

        // test if all registered cache_dir is accessible
        $__cache_dir = $smarty->getCacheDir();
        $_cache_dir = realpath($__cache_dir);
        if (!$_cache_dir) {
            $status = false;
            $message = "FAILED: {$__cache_dir} does not exist";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'cache_dir' ] = $message;
            }
        } elseif (!is_dir($_cache_dir)) {
            $status = false;
            $message = "FAILED: {$_cache_dir} is not a directory";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'cache_dir' ] = $message;
            }
        } elseif (!is_readable($_cache_dir)) {
            $status = false;
            $message = "FAILED: {$_cache_dir} is not readable";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'cache_dir' ] = $message;
            }
        } elseif (!is_writable($_cache_dir)) {
            $status = false;
            $message = "FAILED: {$_cache_dir} is not writable";
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'cache_dir' ] = $message;
            }
        } else {
            if ($errors === null) {
                echo "{$_cache_dir} is OK.\n";
            }
        }

        if ($errors === null) {
            echo "Testing configs directory...\n";
        }

        // test if all registered config_dir are accessible
        foreach ($smarty->getConfigDir() as $config_dir) {
            $_config_dir = $config_dir;
            // resolve include_path or fail existence
            if (!$config_dir) {
                if ($smarty->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_config_dir)) {
                    // try PHP include_path
                    if ($_stream_resolve_include_path) {
                        $config_dir = stream_resolve_include_path($_config_dir);
                    } else {
                        $config_dir = $smarty->ext->_getIncludePath->getIncludePath($_config_dir, null, $smarty);
                    }

                    if ($config_dir !== false) {
                        if ($errors === null) {
                            echo "$config_dir is OK.\n";
                        }

                        continue;
                    } else {
                        $status = false;
                        $message = "FAILED: $_config_dir does not exist (and couldn't be found in include_path either)";
                        if ($errors === null) {
                            echo $message . ".\n";
                        } else {
                            $errors[ 'config_dir' ] = $message;
                        }

                        continue;
                    }
                } else {
                    $status = false;
                    $message = "FAILED: $_config_dir does not exist";
                    if ($errors === null) {
                        echo $message . ".\n";
                    } else {
                        $errors[ 'config_dir' ] = $message;
                    }

                    continue;
                }
            }

            if (!is_dir($config_dir)) {
                $status = false;
                $message = "FAILED: $config_dir is not a directory";
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'config_dir' ] = $message;
                }
            } elseif (!is_readable($config_dir)) {
                $status = false;
                $message = "FAILED: $config_dir is not readable";
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'config_dir' ] = $message;
                }
            } else {
                if ($errors === null) {
                    echo "$config_dir is OK.\n";
                }
            }
        }

        if ($errors === null) {
            echo "Testing sysplugin files...\n";
        }
        // test if sysplugins are available
        $source = SMARTY_SYSPLUGINS_DIR;
        if (is_dir($source)) {
            $expectedSysplugins = array('smartycompilerexception.php'                               => true,
                                        'smartyexception.php'                                       => true,
                                        'smarty_cacheresource.php'                                  => true,
                                        'smarty_cacheresource_custom.php'                           => true,
                                        'smarty_cacheresource_keyvaluestore.php'                    => true,
                                        'smarty_data.php'                                           => true,
                                        'smarty_internal_cacheresource_file.php'                    => true,
                                        'smarty_internal_compilebase.php'                           => true,
                                        'smarty_internal_compile_append.php'                        => true,
                                        'smarty_internal_compile_assign.php'                        => true,
                                        'smarty_internal_compile_block.php'                         => true,
                                        'smarty_internal_compile_break.php'                         => true,
                                        'smarty_internal_compile_call.php'                          => true,
                                        'smarty_internal_compile_capture.php'                       => true,
                                        'smarty_internal_compile_config_load.php'                   => true,
                                        'smarty_internal_compile_continue.php'                      => true,
                                        'smarty_internal_compile_debug.php'                         => true,
                                        'smarty_internal_compile_eval.php'                          => true,
                                        'smarty_internal_compile_extends.php'                       => true,
                                        'smarty_internal_compile_for.php'                           => true,
                                        'smarty_internal_compile_foreach.php'                       => true,
                                        'smarty_internal_compile_function.php'                      => true,
                                        'smarty_internal_compile_if.php'                            => true,
                                        'smarty_internal_compile_include.php'                       => true,
                                        'smarty_internal_compile_include_php.php'                   => true,
                                        'smarty_internal_compile_insert.php'                        => true,
                                        'smarty_internal_compile_ldelim.php'                        => true,
                                        'smarty_internal_compile_nocache.php'                       => true,
                                        'smarty_internal_compile_private_block_plugin.php'          => true,
                                        'smarty_internal_compile_private_foreachsection.php'        => true,
                                        'smarty_internal_compile_private_function_plugin.php'       => true,
                                        'smarty_internal_compile_private_modifier.php'              => true,
                                        'smarty_internal_compile_private_object_block_function.php' => true,
                                        'smarty_internal_compile_private_object_function.php'       => true,
                                        'smarty_internal_compile_private_php.php'                   => true,
                                        'smarty_internal_compile_private_print_expression.php'      => true,
                                        'smarty_internal_compile_private_registered_block.php'      => true,
                                        'smarty_internal_compile_private_registered_function.php'   => true,
                                        'smarty_internal_compile_private_special_variable.php'      => true,
                                        'smarty_internal_compile_rdelim.php'                        => true,
                                        'smarty_internal_compile_section.php'                       => true,
                                        'smarty_internal_compile_setfilter.php'                     => true,
                                        'smarty_internal_compile_shared_inheritance.php'            => true,
                                        'smarty_internal_compile_while.php'                         => true,
                                        'smarty_internal_configfilelexer.php'                       => true,
                                        'smarty_internal_configfileparser.php'                      => true,
                                        'smarty_internal_config_file_compiler.php'                  => true,
                                        'smarty_internal_data.php'                                  => true,
                                        'smarty_internal_debug.php'                                 => true,
                                        'smarty_internal_extension_clear.php'                       => true,
                                        'smarty_internal_extension_handler.php'                     => true,
                                        'smarty_internal_method_addautoloadfilters.php'             => true,
                                        'smarty_internal_method_adddefaultmodifiers.php'            => true,
                                        'smarty_internal_method_append.php'                         => true,
                                        'smarty_internal_method_appendbyref.php'                    => true,
                                        'smarty_internal_method_assignbyref.php'                    => true,
                                        'smarty_internal_method_assignglobal.php'                   => true,
                                        'smarty_internal_method_clearallassign.php'                 => true,
                                        'smarty_internal_method_clearallcache.php'                  => true,
                                        'smarty_internal_method_clearassign.php'                    => true,
                                        'smarty_internal_method_clearcache.php'                     => true,
                                        'smarty_internal_method_clearcompiledtemplate.php'          => true,
                                        'smarty_internal_method_clearconfig.php'                    => true,
                                        'smarty_internal_method_compileallconfig.php'               => true,
                                        'smarty_internal_method_compilealltemplates.php'            => true,
                                        'smarty_internal_method_configload.php'                     => true,
                                        'smarty_internal_method_createdata.php'                     => true,
                                        'smarty_internal_method_getautoloadfilters.php'             => true,
                                        'smarty_internal_method_getconfigvars.php'                  => true,
                                        'smarty_internal_method_getdebugtemplate.php'               => true,
                                        'smarty_internal_method_getdefaultmodifiers.php'            => true,
                                        'smarty_internal_method_getregisteredobject.php'            => true,
                                        'smarty_internal_method_getstreamvariable.php'              => true,
                                        'smarty_internal_method_gettags.php'                        => true,
                                        'smarty_internal_method_gettemplatevars.php'                => true,
                                        'smarty_internal_method_loadfilter.php'                     => true,
                                        'smarty_internal_method_loadplugin.php'                     => true,
                                        'smarty_internal_method_mustcompile.php'                    => true,
                                        'smarty_internal_method_registercacheresource.php'          => true,
                                        'smarty_internal_method_registerclass.php'                  => true,
                                        'smarty_internal_method_registerdefaultconfighandler.php'   => true,
                                        'smarty_internal_method_registerdefaultpluginhandler.php'   => true,
                                        'smarty_internal_method_registerdefaulttemplatehandler.php' => true,
                                        'smarty_internal_method_registerfilter.php'                 => true,
                                        'smarty_internal_method_registerobject.php'                 => true,
                                        'smarty_internal_method_registerplugin.php'                 => true,
                                        'smarty_internal_method_registerresource.php'               => true,
                                        'smarty_internal_method_setautoloadfilters.php'             => true,
                                        'smarty_internal_method_setdebugtemplate.php'               => true,
                                        'smarty_internal_method_setdefaultmodifiers.php'            => true,
                                        'smarty_internal_method_unloadfilter.php'                   => true,
                                        'smarty_internal_method_unregistercacheresource.php'        => true,
                                        'smarty_internal_method_unregisterfilter.php'               => true,
                                        'smarty_internal_method_unregisterobject.php'               => true,
                                        'smarty_internal_method_unregisterplugin.php'               => true,
                                        'smarty_internal_method_unregisterresource.php'             => true,
                                        'smarty_internal_nocache_insert.php'                        => true,
                                        'smarty_internal_parsetree.php'                             => true,
                                        'smarty_internal_parsetree_code.php'                        => true,
                                        'smarty_internal_parsetree_dq.php'                          => true,
                                        'smarty_internal_parsetree_dqcontent.php'                   => true,
                                        'smarty_internal_parsetree_tag.php'                         => true,
                                        'smarty_internal_parsetree_template.php'                    => true,
                                        'smarty_internal_parsetree_text.php'                        => true,
                                        'smarty_internal_resource_eval.php'                         => true,
                                        'smarty_internal_resource_extends.php'                      => true,
                                        'smarty_internal_resource_file.php'                         => true,
                                        'smarty_internal_resource_php.php'                          => true,
                                        'smarty_internal_resource_registered.php'                   => true,
                                        'smarty_internal_resource_stream.php'                       => true,
                                        'smarty_internal_resource_string.php'                       => true,
                                        'smarty_internal_runtime_cachemodify.php'                   => true,
                                        'smarty_internal_runtime_codeframe.php'                     => true,
                                        'smarty_internal_runtime_filterhandler.php'                 => true,
                                        'smarty_internal_runtime_foreach.php'                       => true,
                                        'smarty_internal_runtime_getincludepath.php'                => true,
                                        'smarty_internal_runtime_hhvm.php'                          => true,
                                        'smarty_internal_runtime_inheritance.php'                   => true,
                                        'smarty_internal_runtime_subtemplate.php'                   => true,
                                        'smarty_internal_runtime_tplfunction.php'                   => true,
                                        'smarty_internal_runtime_updatecache.php'                   => true,
                                        'smarty_internal_runtime_updatescope.php'                   => true,
                                        'smarty_internal_runtime_validatecompiled.php'              => true,
                                        'smarty_internal_runtime_var.php'                           => true,
                                        'smarty_internal_runtime_writefile.php'                     => true,
                                        'smarty_internal_smartytemplatecompiler.php'                => true,
                                        'smarty_internal_template.php'                              => true,
                                        'smarty_internal_templatebase.php'                          => true,
                                        'smarty_internal_templatecompilerbase.php'                  => true,
                                        'smarty_internal_templatelexer.php'                         => true,
                                        'smarty_internal_templateparser.php'                        => true,
                                        'smarty_internal_testinstall.php'                           => true,
                                        'smarty_internal_undefined.php'                             => true,
                                        'smarty_resource.php'                                       => true,
                                        'smarty_resource_custom.php'                                => true,
                                        'smarty_resource_recompiled.php'                            => true,
                                        'smarty_resource_uncompiled.php'                            => true,
                                        'smarty_security.php'                                       => true,
                                        'smarty_template_cached.php'                                => true,
                                        'smarty_template_compiled.php'                              => true,
                                        'smarty_template_config.php'                                => true,
                                        'smarty_template_resource_base.php'                         => true,
                                        'smarty_template_source.php'                                => true,
                                        'smarty_undefined_variable.php'                             => true,
                                        'smarty_variable.php'                                       => true,);
            $iterator = new DirectoryIterator($source);
            foreach ($iterator as $file) {
                if (!$file->isDot()) {
                    $filename = $file->getFilename();
                    if (isset($expectedSysplugins[ $filename ])) {
                        unset($expectedSysplugins[ $filename ]);
                    }
                }
            }
            if ($expectedSysplugins) {
                $status = false;
                $message = "FAILED: files missing from libs/sysplugins: " . join(', ', array_keys($expectedSysplugins));
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'sysplugins' ] = $message;
                }
            } elseif ($errors === null) {
                echo "... OK\n";
            }
        } else {
            $status = false;
            $message = "FAILED: " . SMARTY_SYSPLUGINS_DIR . ' is not a directory';
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'sysplugins_dir_constant' ] = $message;
            }
        }

        if ($errors === null) {
            echo "Testing plugin files...\n";
        }
        // test if core plugins are available
        $source = SMARTY_PLUGINS_DIR;
        if (is_dir($source)) {
            $expectedPlugins =
                array('block.textformat.php'                  => true, 'function.counter.php' => true,
                      'function.cycle.php'                    => true, 'function.fetch.php' => true,
                      'function.html_checkboxes.php'          => true, 'function.html_image.php' => true,
                      'function.html_options.php'             => true, 'function.html_radios.php' => true,
                      'function.html_select_date.php'         => true, 'function.html_select_time.php' => true,
                      'function.html_table.php'               => true, 'function.mailto.php' => true,
                      'function.math.php'                     => true, 'modifier.capitalize.php' => true,
                      'modifier.date_format.php'              => true, 'modifier.debug_print_var.php' => true,
                      'modifier.escape.php'                   => true, 'modifier.regex_replace.php' => true,
                      'modifier.replace.php'                  => true, 'modifier.spacify.php' => true,
                      'modifier.truncate.php'                 => true, 'modifiercompiler.cat.php' => true,
                      'modifiercompiler.count_characters.php' => true, 'modifiercompiler.count_paragraphs.php' => true,
                      'modifiercompiler.count_sentences.php'  => true, 'modifiercompiler.count_words.php' => true,
                      'modifiercompiler.default.php'          => true, 'modifiercompiler.escape.php' => true,
                      'modifiercompiler.from_charset.php'     => true, 'modifiercompiler.indent.php' => true,
                      'modifiercompiler.lower.php'            => true, 'modifiercompiler.noprint.php' => true,
                      'modifiercompiler.string_format.php'    => true, 'modifiercompiler.strip.php' => true,
                      'modifiercompiler.strip_tags.php'       => true, 'modifiercompiler.to_charset.php' => true,
                      'modifiercompiler.unescape.php'         => true, 'modifiercompiler.upper.php' => true,
                      'modifiercompiler.wordwrap.php'         => true, 'outputfilter.trimwhitespace.php' => true,
                      'shared.escape_special_chars.php'       => true, 'shared.literal_compiler_param.php' => true,
                      'shared.make_timestamp.php'             => true, 'shared.mb_str_replace.php' => true,
                      'shared.mb_unicode.php'                 => true, 'shared.mb_wordwrap.php' => true,
                      'variablefilter.htmlspecialchars.php'   => true,);
            $iterator = new DirectoryIterator($source);
            foreach ($iterator as $file) {
                if (!$file->isDot()) {
                    $filename = $file->getFilename();
                    if (isset($expectedPlugins[ $filename ])) {
                        unset($expectedPlugins[ $filename ]);
                    }
                }
            }
            if ($expectedPlugins) {
                $status = false;
                $message = "FAILED: files missing from libs/plugins: " . join(', ', array_keys($expectedPlugins));
                if ($errors === null) {
                    echo $message . ".\n";
                } else {
                    $errors[ 'plugins' ] = $message;
                }
            } elseif ($errors === null) {
                echo "... OK\n";
            }
        } else {
            $status = false;
            $message = "FAILED: " . SMARTY_PLUGINS_DIR . ' is not a directory';
            if ($errors === null) {
                echo $message . ".\n";
            } else {
                $errors[ 'plugins_dir_constant' ] = $message;
            }
        }

        if ($errors === null) {
            echo "Tests complete.\n";
            echo "</PRE>\n";
        }

        return $status;
    }
}
