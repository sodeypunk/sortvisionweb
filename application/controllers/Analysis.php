<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

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

		$DEFAULT_LABEL_CONTAINS_VALUE = "1-9; 2000-2017";
		$DEFAULT_LABEL_LENGTH_VALUE = "5";

		$data['filterAtLeastOne'] = true;
		$data['filterLabelContainsChoice'] = "label";
		$data['filterLabelContainsValue'] = $DEFAULT_LABEL_CONTAINS_VALUE;
		$data['filterLabelLengthChoice'] = "label";
		$data['filterLabelLengthValue'] = $DEFAULT_LABEL_LENGTH_VALUE;
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
			$data['ezRefString'] = $ezRefString;
			$this->setDefaultFilterValues($data);

		}

		$this->load->view ( 'templates/header' );
		$this->load->view ( 'pages/analysis', $data);
		$this->load->view ( 'templates/footer' );
	}

	public function analysis() {
		$action = $_POST['action'];
		$ezRefString = $_POST['ezRefString'];

		if ($action == 'Reset') {
			$this->Index($ezRefString);
		}

		$atLeastOne = false;
		if (array_key_exists("filter-atleast-one", $_POST)) {
			$atLeastOne = $_POST['filter-atleast-one'];
		}
		$labelContainsChoice = $_POST['filter-label-contains-choice'];
		$labelContainsValue = $_POST['filter-label-contains-value'];
		$labelLengthChoice = $_POST['filter-label-length-choice'];
		$labelLengthValue = $_POST['filter-label-length-value'];

		$resultsClientAll = $this->Results_Client_model->get_by_ezRefString ( $ezRefString );

		$chars_to_remove = array("[", "]", "'", " ");
		$resultsClientGood = array();
		$resultsClientCleanup = array();

		$invalidNumbersToCheck = $this->GetInvalidLabels($labelContainsValue);
		foreach ($resultsClientAll as $row)
		{
			$row['LABEL_REMOVED'] = "";
			$row['NEW_CLEANUP'] = "";
			$labelString = $row['LABEL'];
			$labelString = str_replace($chars_to_remove, "", $labelString);

			$invalidContainsLabels = $this->CheckLabelsContainsFilters($labelString, $invalidNumbersToCheck, $labelContainsValue);
			$invalidLengthLabels = $this->CheckLabelsLengthFilters($labelString, $labelLengthValue);

			$combinedInvalidLabels = array_merge($invalidContainsLabels, $invalidLengthLabels);
			$combinedInvalidLabels = array_unique($combinedInvalidLabels);
			$pass = $this->CheckAtLeastOneLabelFilter($labelString, $atLeastOne, $combinedInvalidLabels);

			if (count($invalidContainsLabels) > 0)
			{
				$row['LABEL_REMOVED'] = implode(",", $invalidContainsLabels);
				$row['NEW_CLEANUP'] = 'Partial';

				if ($labelContainsChoice == 'image') {
					$pass = false;
				}
			}

			if (count($invalidLengthLabels) > 0)
			{
				$row['LABEL_REMOVED'] = $row['LABEL_REMOVED'] . "," . implode(",", $invalidLengthLabels);
				$row['NEW_CLEANUP'] = 'Partial';

				if ($labelLengthChoice == 'image') {
					$pass = false;
				}
			}

			if ($pass == true)
			{
				array_push($resultsClientGood, $row);
			}
			else
			{
				$row['NEW_CLEANUP'] = 'Cleanup';
				array_push($resultsClientCleanup, $row);

			}
		}

		if ($action == 'Update')
		{
			$saveArray = array_merge($resultsClientGood, $resultsClientCleanup);
			$this->Results_Client_model->UpdateResultsClient ( $saveArray );
		}

		$data['resultsClientGood'] = $resultsClientGood;
		$data['resultsClientCleanup'] = $resultsClientCleanup;
		$data['goodPercent'] = round(count($resultsClientGood) / (count($resultsClientGood) + count($resultsClientCleanup)) * 100);
		$data['cleanupPercent'] = round(count($resultsClientCleanup) / (count($resultsClientGood) + count($resultsClientCleanup)) * 100);
		$data['ezRefString'] = $ezRefString;
		$data['filterAtLeastOne'] = $atLeastOne;
		$data['filterLabelContainsChoice'] = $labelContainsChoice;
		$data['filterLabelContainsValue'] = $labelContainsValue;
		$data['filterLabelLengthChoice'] = $labelLengthChoice;
		$data['filterLabelLengthValue'] = $labelLengthValue;

		$this->load->view ( 'templates/header' );
		$this->load->view ( 'pages/analysis', $data);
		$this->load->view ( 'templates/footer' );
	}

	private function CheckAtLeastOneLabelFilter($labelString, $atLeastOne, $combinedInvalidLabels)
	{
		$pass = true;
		$labelsArray = explode(",", $labelString);

		if (filter_var($atLeastOne, FILTER_VALIDATE_BOOLEAN) == true)
		{
			if (strlen($labelString) <= 0)
			{
				$pass = false;
			}

			if (count($combinedInvalidLabels) == count($labelsArray))
			{
				$pass = false;
			}
		}

		return $pass;
	}

	private function CheckLabelsContainsFilters($labelString, $invalidNumbersToCheck, $labelContainsValue) {

		$invalidLabels = array();

		if (strlen($labelContainsValue) > 0)
		{

			$labelsArray = explode(",", $labelString);

			foreach ($labelsArray as $label)
			{
				$labelNum = (int)$label;
				if (in_array($labelNum, $invalidNumbersToCheck))
				{
					array_push($invalidLabels, $label);
				}
			}
		}

		return $invalidLabels;
	}

	private function CheckLabelsLengthFilters($labelString, $labelLengthValue) {

		$invalidLabels = array();

		if (strlen($labelLengthValue) > 0)
		{
			$labelsArray = explode(",", $labelString);

			foreach ($labelsArray as $label)
			{
				if (strlen($label) > (int)$labelLengthValue)
				{
					array_push($invalidLabels, $label);
				}
			}
		}

		return $invalidLabels;
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