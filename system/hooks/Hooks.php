<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hook class
 */
class Hooks {

    /**
     * Loading Controllers and Models for the application
     * If you want use a inheritence (CI_(...) MY_(...), Backoffice_(...), etc...)
     *
     * @return type
     */
    public function load_controllers_models() {
        //  CHAMANDO A FUNCAO DE AUTOLOAD DE CONTROLLERS E MODELS
        spl_autoload_register(array($this, 'autoload_controllers'));
        spl_autoload_register(array($this, 'autoload_models'));
    }

    /**
     * Carrega automaticamente todos os controllers
     * Load controllers automatically
     * If you want load files using require/include functions, it's verify if the file was inserted before
     *
     * @param string $className Controller's name
     * @return type
     */
    protected function autoload_controllers($className){

        $filename = APPPATH . "controllers/$className.php";
        $arrayIncludedFiles = get_included_files();

        $result = array();
        foreach ($arrayIncludedFiles as $item) {
            $result[] = ((bool)strpos($item, "$className.php") !== FALSE);
        }
        //  Verificando se o arquivo tem permissao de leitura e se ele ja nao foi previamente incluido
        if ( (file_exists($filename) and is_readable($filename)) and (!in_array(TRUE, $result))) {
            require_once $filename;
        }
    }

    /**
     * Carrega automaticamente todos os models
     * Load models automatically
     * If you want load files using require/include functions, it's verify if the file was inserted before
     *
     * @param string $className Model's name
     * @return type
     */
    protected function autoload_models($className){

        $filename = APPPATH . strtolower("models/$className.php");
        $arrayIncludedFiles = get_included_files();
        $result = array();
        foreach ($arrayIncludedFiles as $item) {
            $result[] = ((bool)strpos($item, "$className.php") !== FALSE);
        }
        //  Verificando se o arquivo tem permissao de leitura e se ele ja nao foi previamente incluido
        if ( (file_exists($filename) and is_readable($filename)) and (!in_array(TRUE, $result))) {
            require_once $filename;
        }
    }

    /**
     * Capture the output in cli mode
     * @return type
     */
    public function captureOutput()
    {
        $this->CI =& get_instance();
        $output = $this->CI->output->get_output();
        if (PHP_SAPI != 'cli') {
            echo $output;
        }
    }

}