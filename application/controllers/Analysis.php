<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

session_start();

class Analysis extends CI_Controller {

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

	private function setDefaultFilterValues(&$data) {

		$DEFAULT_LABEL_CONTAINS_VALUE = "0-9; 2000-2017; 20000+";
		$DEFAULT_LABEL_LENGTH_VALUE = "5";

		$data['filterAtLeastOne'] = true;
		$data['filterLabelContainsChoice'] = "label";
		$data['filterLabelContainsValue'] = $DEFAULT_LABEL_CONTAINS_VALUE;
		$data['filterLabelLengthChoice'] = "label";
		$data['filterLabelLengthValue'] = $DEFAULT_LABEL_LENGTH_VALUE;
	}
	
	public function index($ezRefString = '') {
		if (!empty ($_GET)) {
			$fileId = $_GET['fileid'];

			$resultsClientGood = $this->Results_Client_model->get_by_fileId($fileId, 'false');
			$resultsClientPartial = $this->Results_Client_model->get_by_fileId($fileId, 'partial');
			$resultsClientCleanup = $this->Results_Client_model->get_by_fileId($fileId, 'cleanup');
			$sumOfTotal = count($resultsClientGood) + count($resultsClientCleanup) + count($resultsClientPartial);
			if ($sumOfTotal == 0) $sumOfTotal = 1;

			$data['resultsClientGood'] = $resultsClientGood;
			$data['resultsClientPartial'] = $resultsClientPartial;
			$data['resultsClientCleanup'] = $resultsClientCleanup;
			$data['goodPercent'] = round((count($resultsClientGood) / $sumOfTotal) * 100);
			$data['partialPercent'] = round((count($resultsClientPartial) / $sumOfTotal) * 100);
			$data['cleanupPercent'] = round((count($resultsClientCleanup) / $sumOfTotal) * 100);
			$data['fileId'] = $fileId;
			$this->setDefaultFilterValues($data);


			$this->load->view('templates/header');
			$this->load->view('pages/analysis', $data);
			$this->load->view('templates/footer');
		}
		else
		{
			redirect('home');
		}
	}

	public function analysis() {
		$action = $_POST['action'];
		$fileId = $_POST['fileId'];

		if ($action == 'Reset') {
			$this->Index($fileId);
		}

		$atLeastOne = false;
		if (array_key_exists("filter-atleast-one", $_POST)) {
			$atLeastOne = $_POST['filter-atleast-one'];
		}
		$labelContainsChoice = $_POST['filter-label-contains-choice'];
		$labelContainsValue = $_POST['filter-label-contains-value'];
//		$labelLengthChoice = $_POST['filter-label-length-choice'];
//		$labelLengthValue = $_POST['filter-label-length-value'];

		$resultsClientAll = $this->Results_Client_model->get_by_fileId ( $fileId, '');

		$resultsClientGood = array();
		$resultsClientCleanup = array();
		$resultsClientPartial = array();

		$invalidNumbersToCheck = $this->GetInvalidLabels($labelContainsValue);
		foreach ($resultsClientAll as $row)
		{
			$this->CheckLabelsContainsFilters($row, $invalidNumbersToCheck);
			//$this->CheckLabelsLengthFilters($row, $labelLengthValue);

			$pass = $this->CheckAtLeastOneLabelFilter($row, $atLeastOne);

			$row['LABELS_STRING'] = util::bibArrayToStringSlow($row['LABELS_ARRAY'], null, false);
			$row['LABELS_STRING_REMOVED'] = util::bibArrayToStringSlow($row['LABELS_ARRAY'], null, true);
			$row['UPDT'] = date("Y-m-d H:i:s");

			if (count(util::GoodLabels($row)) > 0 && count(util::BadLabels($row)) > 0 && $pass == true)
			{
				$row['CLEANUP'] = 'Partial';
				array_push($resultsClientPartial, $row);

			}
			else if (count(util::GoodLabels($row)) > 0 && count(util::BadLabels($row)) == 0 && $pass == true)
			{
				$row['CLEANUP'] = '';
				array_push($resultsClientGood, $row);

			}
			else if (count(util::GoodLabels($row)) <= 0)
			{
				$row['CLEANUP'] = 'Cleanup';
				array_push($resultsClientCleanup, $row);

			}

		}

		if ($action == 'Update')
		{
			$saveArray = array_merge($resultsClientGood, $resultsClientCleanup, $resultsClientPartial);
			$this->Results_Client_model->UpdateResultsClient ( $saveArray );
		}

		$data['resultsClientGood'] = $resultsClientGood;
		$data['resultsClientPartial'] = $resultsClientPartial;
		$data['resultsClientCleanup'] = $resultsClientCleanup;
		$data['goodPercent'] = round(count($resultsClientGood) / (count($resultsClientGood) + count($resultsClientCleanup) + count($resultsClientPartial)) * 100);
		$data['partialPercent'] = round(count($resultsClientPartial) / (count($resultsClientGood) + count($resultsClientCleanup) + count($resultsClientPartial)) * 100);
		$data['cleanupPercent'] = round(count($resultsClientCleanup) / (count($resultsClientGood) + count($resultsClientCleanup) + count($resultsClientPartial)) * 100);
		$data['fileId'] = $fileId;
		$data['filterAtLeastOne'] = $atLeastOne;
		$data['filterLabelContainsChoice'] = $labelContainsChoice;
		$data['filterLabelContainsValue'] = $labelContainsValue;
//		$data['filterLabelLengthChoice'] = $labelLengthChoice;
//		$data['filterLabelLengthValue'] = $labelLengthValue;

		$this->load->view ( 'templates/header' );
		$this->load->view ( 'pages/analysis', $data);
		$this->load->view ( 'templates/footer' );
	}

	private function CheckAtLeastOneLabelFilter($row, $atLeastOne)
	{
		$pass = true;

		if (filter_var($atLeastOne, FILTER_VALIDATE_BOOLEAN) == true)
		{
			if (count(util::GoodLabels($row)) <= 0)
			{
				$pass = false;
			}
		}

		return $pass;
	}

	private function CheckLabelsContainsFilters(&$row, $invalidNumbersToCheck) {

		$labelsArray = &$row['LABELS_ARRAY'];

		$greaterThanNumbers = array();
		foreach ($invalidNumbersToCheck as $number)
		{
			if (strpos($number, "+") !== false)
			{
				$newNumber = (int)str_replace("+", "", $number);
				array_push($greaterThanNumbers, $newNumber);
			}
		}

		if (count($invalidNumbersToCheck) > 0)
		{
			foreach ($labelsArray as &$label)
			{
				$labelNum = (int)$label['LABEL'];
				if (in_array($labelNum, $invalidNumbersToCheck))
				{
					$label['REMOVED'] = "1";
					$label['UPDT'] = date("Y-m-d H:i:s");
				}
				else {
					foreach ($greaterThanNumbers as $number) {
						if ($labelNum >= $number)
						{
							$label['REMOVED'] = "1";
							$label['UPDT'] = date("Y-m-d H:i:s");
						}
					}
				}
			}

		}

	}

	private function CheckLabelsLengthFilters(&$row, $labelLengthValue) {

		$labelsArray = &$row['LABELS_ARRAY'];

		if (strlen($labelLengthValue) > 0)
		{
			foreach ($labelsArray as &$label)
			{
				if (strlen($label['LABEL']) > (int)$labelLengthValue)
				{
					$label['REMOVED'] = "1";
					$label['UPDT'] = date("Y-m-d H:i:s");
				}
			}
		}

	}

	private function GetInvalidLabels($labelContainsValue) {
		$chars_to_remove = array(" ");
		$labelContainsValue = str_replace($chars_to_remove, "", $labelContainsValue);
		$numRangesArray = explode(";", $labelContainsValue);
		$numbersArray = array();

		foreach ($numRangesArray as $numRange)
		{
			if (strpos($numRange, "-") !== false)
			{
				$indexOfDash = strpos($numRange, "-");
				$startingNum = (int)substr($numRange, 0, $indexOfDash);
				$endingNum = (int)substr($numRange, $indexOfDash + 1, strlen($numRange));
				for ($i=$startingNum; $i<=$endingNum; $i++)
				{
					array_push($numbersArray, $i);
				}
			}
			else
			{
				array_push($numbersArray, $numRange);
			}
		}

		return $numbersArray;
	}


}