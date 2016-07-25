<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

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
        $data ['systems'] = $this->GetAllSystems();
        $data ['files'] = $this->GetFiles(10);

        $this->load->view ( 'templates/header' );
        $this->load->view ( 'pages/bibcommander', $data);
        $this->load->view ( 'templates/footer' );
    }

    private function GetAllSystems()
    {
        return $this->system_model->get_all();
    }

    private function GetFiles($numberOfFiles)
    {
        return $this->files_model->get_files($numberOfFiles);
    }

}