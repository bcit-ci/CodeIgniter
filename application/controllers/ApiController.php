<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Required Files
 * 
 * application/language/english/rest_controller_lang.php
 * https://gist.githubusercontent.com/SyedMuradAliShah/c2b4003128f8ec263a7253efda29f29f/raw/edfd1f9e13504a837095a5e57aa8489ab29667ad/rest_controller_lang.php
 * 
 * application/libraries/REST_Controller.php
 * https://gist.githubusercontent.com/SyedMuradAliShah/c2b4003128f8ec263a7253efda29f29f/raw/edfd1f9e13504a837095a5e57aa8489ab29667ad/REST_Controller.php
 * 
 * application/libraries/Format.php
 * https://gist.githubusercontent.com/SyedMuradAliShah/c2b4003128f8ec263a7253efda29f29f/raw/edfd1f9e13504a837095a5e57aa8489ab29667ad/Format.php
 * 
 * application/config/rest.php
 * https://gist.githubusercontent.com/SyedMuradAliShah/c2b4003128f8ec263a7253efda29f29f/raw/edfd1f9e13504a837095a5e57aa8489ab29667ad/rest.php
 * 
 * 
 * PLEASE NOTE
 * Alway declear request method name with the function.
 * 
 * For example
 * If you want to get access apiController/apiController_get with GET then simply go for apiController/apiController.
 * If you used POST method it will access apiController_post.
 * 
 * In case you want to define your own then use methodname_requestmethod i.e apiController_career_post
 * 
 * Default is index so you don't need to define the function name in your uri just change the
 * request method name.
 * 
 * In below case GET example.com/apiController/1 it will call index_get.  
 * Also if PUT example.com/apiController/1 it will call index_put.
 * 
 */


class ApiController extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Plat_model');
    }

    public function random()
    {
        $day = date('l');

        if ($day != 'Sunday') {
            $id_day = 0;
        } else {
            $id_day = 1;
        }

        $plat = new Plat_model();
        $result = $plat->get_plat_from_day($id_day);

        shuffle($result);

        $random_index = rand(0, count($result) - 1);
        $plat_randomized = $result[$random_index];

        return $this->json($plat_randomized, Response::HTTP_OK);
    }


}

/* End of file ApiController.php and path /application/controllers/ApiController.php */
