<?php

class MyController extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        // $this->load->database();
        $this->load->library('form_validation');
        header('Content-Type: application/json');
    }
}