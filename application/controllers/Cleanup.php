<?php

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
		if (empty ( $ezRefString )) {
			$data['status'] = null;
		}
		else {
			$resultsClientGood = $this->Results_Client_model->get_by_ezRefString ( $ezRefString, 'false' );
			$resultsClientCleanup = $this->Results_Client_model->get_by_ezRefString ( $ezRefString, 'true' );

			$data['resultsClientGood'] = $resultsClientGood;
			$data['resultsClientCleanup'] = $resultsClientCleanup;
			$data['goodPercent'] = round(count($resultsClientGood) / (count($resultsClientGood) + count($resultsClientCleanup)) * 100);
			$data['cleanupPercent'] = round(count($resultsClientCleanup) / (count($resultsClientGood) + count($resultsClientCleanup)) * 100);

		}

		$this->load->view ( 'templates/header' );
		$this->load->view ( 'pages/cleanup', $data);
		$this->load->view ( 'templates/footer' );
	}


}