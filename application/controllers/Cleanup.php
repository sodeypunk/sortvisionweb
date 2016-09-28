<?php

include 'Utility.php';

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Cleanup extends CI_Controller {

	public function __construct() {
		parent::__construct ();
		
		$this->load->helper ( array (
				'url',
				'html',
				'form' 
		) );
		$this->load->model ( 'files_model' );
		$this->load->model ( 'Results_Client_model' );
	}

	
	public function index($ezRefString = '') {
		$resultCleanupImages = $this->Results_Client_model->get_by_ezRefString($ezRefString, 'true');
		$data['tiledCleanupResultImages'] = util::getImagesTiledFromDBForCleanup($resultCleanupImages, "assets/result_images/", $ezRefString, 'true');
		$data['ezRefString'] = $ezRefString;

		$this->load->view ( 'templates/header' );
		$this->load->view ( 'pages/cleanup', $data);
		$this->load->view ( 'templates/footer' );
	}


}