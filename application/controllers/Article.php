<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Article extends CI_Controller{
    public function __construct() {
        parent::__construct();
        $this->load->helper("url");
    }
    public function index() {
        $this->load->view("article/list");
    }
}

