<?php

include 'Utility.php';

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Files extends CI_Controller {
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
		$this->load->view ( 'templates/header' );
		$this->load->view ( 'pages/home' );
		$this->load->view ( 'templates/footer' );
	}
	
	public function Status() {
		if (empty ( $_GET )) {
			$data ['status'] = null;
		} 
		else {
			$fileId = $_GET['fileid'];
			$result = $this->files_model->get_by_fileId( $fileId );
			
			if ($result != false) {
				$s3Bucket = $result[0]['S3_BUCKET'];
				$jobId = $result[0]['IDJOB'];
				$fileNameWithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $result[0]['FILE_NAME']);

                $data ['fileNm'] = $result[0]['FILE_NAME'];
				$data ['status'] = $result[0]['STATUS'];
				$data ['uploadedDt'] = $result[0]['UPDT'];
				$data ['fileId'] = $fileId;
				$data ['filesHistory'] = $result;
				$data ['s3Bucket'] = $result[0]['S3_BUCKET'];
				$data ['fileName'] = $result[0]['FILE_NAME'];
				$data ['s3Path'] = $result[0]['S3_BUCKET'] . "/" . $result[0]['FILE_NAME'];

				$resultImages = $this->Results_Client_model->get_by_fileId($fileId, 'false', 100);

				$resultImagePath = sprintf("http://www.sortvision.com/bibcommander/assets/result_images/%s/%s/%s/%s/recognition_images/", $s3Bucket, $fileId, $jobId, $fileNameWithoutExt);
                $data ['tiledResultImages'] =  util::getImagesTiledFromDB($resultImages, $resultImagePath, $fileId);
			}
		}
		
		$this->load->view ( 'templates/header', $data );
		$this->load->view ( 'pages/files', $data );
		$this->load->view ( 'templates/footer' );
	}
	
	public function GetUpdate() {

		$ezRefString = $_POST['ezRefString'];
		$result = $this->files_model->get_by_ezRefString($ezRefString);

		if ($result != false) {

			$status = $result[0]['STATUS'];
			$maxStatus = 0;
			$statusHTML = "";
			$imageHTML = "";

			foreach ($result as $row) {
				if ((int)$row['STATUS_CODE'] > $maxStatus) {
					$maxStatus = (int)$row['STATUS_CODE'];
				}
			}

			$completedPercent = round(($maxStatus / 7) * 100);

			// Get the table data
			$statusHTML .= '<tr><td>Initializing...</td><td></td></tr>';
			if (!empty($result))
			{
				foreach ($result as $row)
				{
					if ($row['DESCR'] != "")
					{
						$statusHTML .= "<tr>";
						$statusHTML .= "<td>" . $row['DESCR'] . "</td>";
						$statusHTML .= "<td>" . $row['UPDT'] . "</td>";
						$statusHTML .= "</tr>";
					}
				}
			}

			// Get the result image if available
			if ($completedPercent >= 100)
			{
				$tiledResultImages = util::getImagesTiledFromDB("assets/result_images/", $ezRefString, 'false', 10);
				$tiledCleanupResultImages =  util::getImagesTiledFromDB("assets/result_images/", $ezRefString, 'true', 10);
			}

			$jsonResult = (object) array (
				'PERCENT' => $completedPercent,
				'STATUS' => $status,
				'STATUS_TABLE_HTML' => $statusHTML,
				'IMAGE_HTML' => $tiledResultImages,
				'IMAGE_HTML_CLEANUP' => $tiledCleanupResultImages
			);

			echo json_encode ( $jsonResult );
		}
	}

    function getDirContents($dir, $fileExtensions, &$results = array()){

        if (file_exists($dir) == false) return $results;

        $files = scandir($dir);

        if (empty($files) == false) {
            foreach ($files as $key => $value) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

                if (!is_dir($path)) {
                    $ext = strtolower(pathinfo($path)['extension']);
                    if (in_array($ext, $fileExtensions)) {
                        $results[] = $path;
                    }
                } else if ($value != "." && $value != "..") {
                    $this->getDirContents($path, $fileExtensions, $results);
                }
            }
        }

        return $results;
    }

    function getImageFilePaths($ezRefString, $assetsLocations)
    {
        $sourcePath = getcwd() . "/" . $assetsLocations . $ezRefString;
        $fileExtensions = array("jpg", "png", "jpeg");
        $files = $this->getDirContents($sourcePath, $fileExtensions);

        $replacePathBase = getcwd() . "/" . $assetsLocations;

        foreach ($files as &$filePath) {
            $filePath = str_replace($replacePathBase, "", $filePath);
        }

        return $files;
    }


}