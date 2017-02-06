<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

session_start();

class Cleanup extends CI_Controller {

	public function __construct() {
		parent::__construct ();
		
		$this->load->helper ( array (
				'url',
				'html',
				'form' 
		) );
		$this->load->model ( 'Users_model' );
		$this->load->model ( 'files_model' );
		$this->load->model ( 'Results_Client_model' );
	}

	
	public function index($fileid = 0, $batch = 100, $page = 1) {

		if (!empty ($_GET) || $fileid > 0) {

			if (!empty ($_GET['fileid'])) {
				$fileid = $_GET['fileid'];
			}
			if (!empty ($_GET['batch'])) {
				$batch = $_GET['batch'];
			}
			if (!empty ($_GET['page'])) {
				$page = $_GET['page'];
			}

			$resultCleanupImages = $this->Results_Client_model->get_by_fileId($fileid, 'true', 100);
			$imageCount = $this->Results_Client_model->get_count_by_fileId($fileid, 'true');
			$reviewedCount = $this->Results_Client_model->get_cleanup_status_count($fileid, 'true', 'REVIEWED');
			$users = $this->Users_model->get_all_users();
			$reviewingUsers = $this->Results_Client_model->get_reviewers($fileid);
			$data['tiledCleanupResultImages'] = util::getImagesTiledFromDBForCleanup($resultCleanupImages, "assets/result_images/");
			$data['fileid'] = $fileid;
			$data['batch'] = $batch;
			$data['page'] = $page;
			$data['imageCount'] = $imageCount;
			$data['reviewedCount'] = $reviewedCount;
			$data['reviewedPercent'] = round(($reviewedCount / $imageCount) * 100, 2);
			$data['users'] = $users;
			$data['reviewingUsers'] = $reviewingUsers;
			$data['loggedInUser'] = $_SESSION['id_user'];
			$data['loggedInUserEmail'] = $_SESSION['email'];


			$data['breadcrumb'] = '<li><a href="' . site_url('bibcommander') . '">Dashboard</a></li>' .
				'<li><a href="' . base_url("index.php/files/status") . '?fileid=' . $fileid . '">Status</a></li>' .
				'<li><a href="' . base_url("index.php/analysis/index") . '?fileid=' . $fileid . '">Analysis</a></li>' .
				'<li class="active">Cleanup</li>';

			$this->load->view('templates/header', $data);
			$this->load->view('pages/cleanup', $data);
			$this->load->view('templates/footer');
		}
		else
		{
			redirect('home');
		}
	}

	public function bibs() {
		$fileid = -1;
		$page = 1;
		$batch = 100;
		$objectString = "";
		$showOnlyAssigned = true;
		$reviewerIdList = array();

		if (!empty($_GET['batch']))
		{
			$batch = $_GET['batch'];
		}
		if (!empty($_GET['page']))
		{
			$page = $_GET['page'];
		}
		if (!empty($_GET['assignedonly']))
		{
			$showOnlyAssigned = $_GET['assignedonly'];
		}
		if (!empty ($_GET['fileid']))
		{
			$fileid = $_GET['fileid'];

			if ($showOnlyAssigned)
			{
				array_push($reviewerIdList, $_SESSION['id_user']);
			}

			$objectString = $this->Results_Client_model->get_by_fileId($fileid, 'true', $batch, $page, $reviewerIdList);
		}

		header('Content-Type: application/json');
		echo json_encode($objectString);
	}

	public function getTotalCleanupImageCount() {
		$countArray = array();
		$pagesArray = array();
		$fileid = $_GET['fileid'];
		$batchSize = (int)$_GET['batch'];
		$showOnlyAssigned = true;
		$reviewerIdList = array();

		if (!empty($_GET['assignedonly']))
		{
			$showOnlyAssigned = $_GET['assignedonly'];
		}

		if ($showOnlyAssigned)
		{
			array_push($reviewerIdList, $_SESSION['id_user']);
		}
		$imageCount = $this->Results_Client_model->get_count_by_fileId($fileid, 'true', 0, $reviewerIdList);

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
		$bibsArrayString = $_POST['bibsArray'];
		$bibsArray = json_decode($bibsArrayString, true);

		if (!is_null($bibsArray)) {
			if ($this->Results_Client_model->UpdateResultsClient($bibsArray)) {
				$resultArray['success'] = true;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($resultArray);
	}

	public function addreviewer() {
		$action = '';
		$fileid = 0;
		$userPercent = 0;
		$userid = 0;
		$batch = 100;
		$page = 1;

		if (!empty($_POST['fileid']))
		{
			$fileid = $_POST['fileid'];
		}
		if (!empty($_POST['action']))
		{
			$action = $_POST['action'];
		}
		if (!empty($_POST['user-percent']))
		{
			$userPercent = $_POST['user-percent'] / 100;
		}
		if (!empty($_POST['userid']))
		{
			$userid = $_POST['userid'];
		}
		if (!empty($_POST['batch']))
		{
			$batch = $_POST['batch'];
		}
		if (!empty($_POST['page']))
		{
			$page = $_POST['page'];
		}

		if ($fileid > 0 && $userid > 0 && $userPercent > 0)
		{
			$currentUserReviewCount = $this->Results_Client_model->get_review_count_for_user($fileid, $userid);
			$imageCount = $this->Results_Client_model->get_count_by_fileId($fileid, 'true');

			$currentUserReviewPercent = $currentUserReviewCount / $imageCount;

			if ($currentUserReviewPercent > $userPercent)
			{
				$data['errorMsg'] = "User currently has percent higher than assigned. Remove the user's percent before assigning again.";
			}
			else
			{
				$percentToAssign = $userPercent - $currentUserReviewPercent;
				$imagesToAssign = ceil($imageCount * $percentToAssign);

				if ($imagesToAssign > 0)
				{
					$rowsToSave = array();
					$resultsClientCleanup = $this->Results_Client_model->get_by_fileId($fileid, 'true');

					foreach ($resultsClientCleanup as $row)
					{
						if ($imagesToAssign > 0) {
							if (empty($row['REVIEWER_ID'])) {
								$row['REVIEWER_ID'] = $userid;
								$imagesToAssign -= 1;

								array_push($rowsToSave, $row);
							}
						}
						else
						{
							break;
						}
					}
				}

				$result = $this->Results_Client_model->UpdateResultsClient($rowsToSave);
				redirect('cleanup?fileid=244');
			}


		}

	}


}