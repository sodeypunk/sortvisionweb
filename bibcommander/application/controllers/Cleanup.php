<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

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

	
	public function index() {

		$fileId = 0;
		$batch = 100;
		$page = 1;

		if (!empty ($_GET)) {

			if (!empty ($_GET['fileid'])) {
				$fileId = $_GET['fileid'];
			}
			if (!empty ($_GET['batch'])) {
				$batch = $_GET['batch'];
			}
			if (!empty ($_GET['page'])) {
				$page = $_GET['page'];
			}

			$resultCleanupImages = $this->Results_Client_model->get_by_fileId($fileId, 'true', 100);
			$imageCount = $this->Results_Client_model->get_count_by_fileId($fileId, 'true');
			$data['tiledCleanupResultImages'] = util::getImagesTiledFromDBForCleanup($resultCleanupImages, "assets/result_images/");
			$data['fileId'] = $fileId;
			$data['batch'] = $batch;
			$data['page'] = $page;
			$data['imageCount'] = $imageCount;

			$this->load->view('templates/header');
			$this->load->view('pages/cleanup', $data);
			$this->load->view('templates/footer');
		}
		else
		{
			redirect('home');
		}
	}

	public function bibs() {
		$fileId = -1;
		$page = 1;
		$batch = 100;
		$objectString = "";

		if (!empty($_GET['batch']))
		{
			$batch = $_GET['batch'];
		}
		if (!empty($_GET['page']))
		{
			$page = $_GET['page'];
		}
		if (!empty ($_GET['fileid']))
		{
			$fileId = $_GET['fileid'];
			$objectString = $this->Results_Client_model->get_by_fileId($fileId, 'true', $batch, $page);
		}

		header('Content-Type: application/json');
		echo json_encode($objectString);
	}

	public function getTotalCleanupImageCount() {
		$countArray = array();
		$pagesArray = array();
		$fileId = $_GET['fileid'];
		$batchSize = (int)$_GET['batch'];
		$imageCount = $this->Results_Client_model->get_count_by_fileId($fileId, 'true');

		$pages = ceil($imageCount / $batchSize);

		for ($x=1; $x<=$pages; $x++)
		{
			$newArray = array();
			$newArray['id'] = $x;
			$newArray['name'] = $x;
			array_push($pagesArray, $newArray);
		}

		$countArray['COUNT'] = $imageCount;
		$countArray['PAGES'] = $pagesArray;

		header('Content-Type: application/json');
		echo json_encode($countArray);
	}

	public function update() {
		$resultArray = array();
		$resultArray['success'] = false;
		$bibsArray = $_POST['bibsArray'];

		if (!is_null($bibsArray)) {
			if ($this->Results_Client_model->UpdateResultsClient($bibsArray)) {
				$resultArray['success'] = true;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($resultArray);
	}


}