<?php

// For Dropzone
if (array_key_exists('HTTP_ORIGIN' , $_SERVER)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Headers: Cache-Control, X-Requested-With');
}

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('url','html','form'));
        $this->load->model('files_model');
    }

    public function index() {
        $this->load->view('templates/header');
        $this->load->view('pages/home');
        $this->load->view('templates/footer');
    }

    public function resultImage() {

        if (!empty($_FILES)) {

            $tempFile = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileFolder = $_POST['folder'];
            $targetPath = getcwd() . '/assets/result_images/' . $fileFolder . '/';

            if (!file_exists($targetPath))
            {
                mkdir($targetPath, 0777, true);
            }

            $targetFile = $targetPath . $fileName ;
            move_uploaded_file($tempFile, $targetFile);
        }

        echo "SUCCESS";
    }

    public function cleanupResultImage() {

        if (!empty($_FILES)) {

            $tempFile = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileFolder = $_POST['folder'];
            $targetPath = getcwd() . '/assets/result_images/' . $fileFolder . '/cleanup/';

            if (!file_exists($targetPath))
            {
                mkdir($targetPath, 0777);
            }

            $targetFile = $targetPath . $fileName ;
            move_uploaded_file($tempFile, $targetFile);
        }

        echo "SUCCESS";
    }

    public function dropzone() {
        $jsonResult = array();
        try
        {

            if (!empty($_FILES)) {

                $tempFile = $_FILES['file']['tmp_name'];
                $fileName = str_replace(" ", "_", $_FILES['file']['name']);
                $fileExt = strtolower(pathinfo($fileName)['extension']);
                $userSystem = $_POST['UserSystem'];
                $unzipAfterUpload = $_POST['UnzipAfterUpload'];

                $ezRefString = $this->readableRandomString(6);
                $targetPath = getcwd() . '/assets/uploads/' . $ezRefString . '/';
                if (!file_exists($targetPath))
                {
                    mkdir($targetPath, 0777);
                }
                $targetFile = $targetPath . $fileName ;

                move_uploaded_file($tempFile, $targetFile);

                if ($fileExt == "zip")
                {
                    if ($unzipAfterUpload != "false") {
                        $zip = new ZipArchive();
                        if ($zip->open($targetFile) == TRUE) {
                            $zip->extractTo($targetPath);
                            $zip->close();
                        }
                    }
                }
                else
                {
                    $this->correctImageOrientation($targetFile);
                }

                $result = $this->files_model->insert($fileName, $ezRefString, $userSystem);
                $statusURL = site_url('/files/status') . "/" . $ezRefString;


                if ($result == true)
                {
                    $status = "SUCCESS";
                }

                $jsonResult[$fileName] = (object)array('STATUS' => $status, 'EZ_REF_STRING' => $ezRefString, 'STATUS_URL' => $statusURL, 'SYSTEM' => $userSystem);
            }
            else
            {
                $jsonResult["RESULT"] = (object)array('MESSAGE' => "No File Uploaded!");
            }

        }
        catch (Exception $e)
        {
            $status = $e->getMessage();
            $jsonResult["RESULT"] = (object)array('MESSAGE' => $status);
        }
        finally
        {
            echo json_encode($jsonResult);
        }
    }

    /**
     * Generates human-readable string.
     */
    private function readableRandomString($length = 6) {
        $string     = '';
        $vowels     = array("a","e","i","o","u");
        $consonants = array(
            'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
            'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
        );
        // Seed it
        srand((double) microtime() * 1000000);
        $max = $length/2;
        for ($i = 1; $i <= $max; $i++)
        {
            $string .= $consonants[rand(0,19)];
            $string .= $vowels[rand(0,4)];
        }
        return $string;
    }

    private function correctImageOrientation($filename) {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }
}