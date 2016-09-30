<?php 
require(APPPATH.'/libraries/REST_Controller.php');
/**
 * summary
 */
class CD_Arduino extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('CDAModel');
        $this->load->helper('url');
    }

}