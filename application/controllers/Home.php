<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

session_start();

class Home extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		
		$this->load->helper ( array (
				'url',
				'html',
				'form' 
		) );
		$this->load->model ( 'files_model' );
	}
	
	public function index() {
		$data ['test'] = "test";

		$this->load->view ( 'templates/header' );
		$this->load->view ( 'pages/home', $data);
		$this->load->view ( 'templates/footer' );
	}

}