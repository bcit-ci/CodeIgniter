<?php

class Dwoowelcome extends Controller {

    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
    	$this->load->library('Dwootemplate');
    	$this->dwootemplate->assign('itshowlate', date('H:i:s'));
    	$this->dwootemplate->display('dwoowelcome.tpl');
    }
}