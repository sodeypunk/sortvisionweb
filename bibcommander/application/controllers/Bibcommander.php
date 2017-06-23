<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

session_start();
date_default_timezone_set('America/Chicago');

class Bibcommander extends CI_Controller {
    public function __construct() {
        parent::__construct ();

        $this->load->helper ( array (
            'url',
            'html',
            'form'
        ) );
        $this->load->model ( 'files_model' );
        $this->load->model ( 'system_model' );
        $this->load->model ( 'Monitor_Status_model' );
        $this->load->model ( 'Results_Client_model' );
    }

    public function index() {
        $data['demo_server_online'] = false;

        if (isset($_SESSION['s3_bucket'])) { # Need to be changed to FILE ID

            $s3Bucket = $_SESSION['s3_bucket'];

            $files = $this->files_model->get_files_by_s3buck($s3Bucket, 100);
            $data['files'] = util::AddLinks($files);

            $demoMonitor = $this->Monitor_Status_model->get_demo_status();

            if ($demoMonitor != false)
            {
                $lastUpdate = strtotime($demoMonitor[0]['UPDT']);
                $nowDateTime = time();

                $dateDiffSecs = $nowDateTime - $lastUpdate;

                if ($dateDiffSecs < 120)
                {
                    $data['demo_server_online'] = true;
                }

            }

            $this->load->view('templates/header', $data);
            $this->load->view('pages/bibcommander', $data);
            $this->load->view('templates/footer');
        }
        else
        {
            redirect('home');
        }

    }

}