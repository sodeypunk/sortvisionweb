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
		if (!empty ($_GET)) {

			$fileId = $_GET['fileid'];

			$resultCleanupImages = $this->Results_Client_model->get_by_fileId($fileId, 'true', 100);
			$imageCount = $this->Results_Client_model->get_count_by_fileId($fileId, 'true');
			$data['tiledCleanupResultImages'] = util::getImagesTiledFromDBForCleanup($resultCleanupImages, "assets/result_images/");
			$data['fileId'] = $fileId;
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
		$fileId = $_GET['fileid'];
		$objectString = "";
		if (!empty ( $fileId )) {

			$objectString = $this->Results_Client_model->get_by_fileId($fileId, 'true', 100);
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
			array_push($pagesArray, $x);
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
		$cleaned = $_POST['cleaned'];
		$saveArray = array();
		if ($cleaned == "true")
		{
			$bibsArray['CLEANUP_STATUS'] = 'CLEANED';
		}
		else
		{
			$bibsArray['CLEANUP_STATUS'] = null;
		}
		array_push($saveArray, $bibsArray);

		if (!is_null($bibsArray)) {
			if ($this->Results_Client_model->UpdateResultsClient($saveArray)) {
				$resultArray['success'] = true;
				$resultArray['bib'] = $bibsArray;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($resultArray);
	}


}