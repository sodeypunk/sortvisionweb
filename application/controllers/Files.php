<?php

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
	
	public function Status($ezRefString = '') {
		if (empty ( $ezRefString )) {
			$data ['status'] = null;
		} 
		else {
			$result = $this->files_model->get_by_ezRefString ( $ezRefString );
			
			if ($result != false) {
                $data ['fileNm'] = $result[0]['FILE_NAME'];
				$data ['status'] = $result[0]['STATUS'];
				$data ['uploadedDt'] = $result[0]['UPDT'];
				$data ['ezRefString'] = $ezRefString;
				$data ['filesHistory'] = $result;

                $uploadedFiles = $this->getImageFilePaths($ezRefString, "assets/uploads/");
                $resultFiles = $this->getImageFilePaths($ezRefString, "assets/result_images/");

                $data ['tiledUploadedImages'] = $this->getImagesTiled("assets/uploads/", $uploadedFiles);
                $data ['tiledResultImages'] = $this->getImagesTiledFromDB("assets/result_images/", $ezRefString, 10);
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
                $resultFiles = $this->getImageFilePaths($ezRefString, "assets/result_images/");
                $imageHTML = $this->getImagesTiledFromDB("assets/result_images/", $ezRefString, 10);
			}
			
			$jsonResult = (object) array (
				'PERCENT' => $completedPercent,
				'STATUS' => $status,
				'STATUS_TABLE_HTML' => $statusHTML,
				'IMAGE_HTML' => $imageHTML
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

    function getImagesTiled($sourcePath, $filePaths)
    {
        $resultHTML = "";
        $count = 0;
        foreach ($filePaths as $filePath) {
            $fileNm = pathinfo($filePath)['basename'];

            $image_properties = array(
                'src' => $sourcePath . $filePath,
                'alt' => $fileNm,
                'class' => 'img-responsive',
                //'width' => '200',
                //'height' => '200',
                'title' => $fileNm,
                'rel' => 'lightbox',
            );

            $resultHTML .= '<div class="row">';
            $resultHTML .= '<div class="col-lg-12">';
            $resultHTML .=  img($image_properties);
            $resultHTML .=  '</div>';
            $resultHTML .=  '</div>';

            $count++;
        }

        return $resultHTML;
    }

	function getImagesTiledFromDB($sourcePath, $ezRefString, $numberOfRecords)
	{

		$resultHTML = "";
		$result = $this->Results_Client_model->get_by_ezRefString($ezRefString, $numberOfRecords);
		$count = 0;
		if ($result != false) {

			foreach ($result as $row) {

				$image_properties = array(
					'src' => $sourcePath . $ezRefString . "/" . $row["IMAGE"],
					'alt' => $row["IMAGE"],
					'class' => 'img-responsive',
					//'width' => '200',
					//'height' => '200',
					'title' => $row["IMAGE"],
					'rel' => 'lightbox',
				);

				if ($count % 4 == 0 || $count == 0) {
					$resultHTML .= '<div class="row">';
				}

				$resultHTML .= '<div class="col-md-3">';
				$resultHTML .= img($image_properties);
				$resultHTML .= '</div>';

				if (($count + 1) % 4 == 0) {
					$resultHTML .= '</div>';
				}

				$count++;
			}
		}

		return $resultHTML;
	}


}