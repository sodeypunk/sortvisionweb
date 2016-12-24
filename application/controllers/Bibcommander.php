<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

session_start();

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
    }

    public function index() {
        if (isset($_SESSION['s3_bucket'])) {

            $s3Bucket = $_SESSION['s3_bucket'];

            $data ['files'] = $this->files_model->get_files_by_s3bucket($s3Bucket, 100);

            $this->load->view('templates/header', $data);
            $this->load->view('pages/bibcommander', $data);
            $this->load->view('templates/footer');
        }

    }

}