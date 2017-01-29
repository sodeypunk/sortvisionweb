<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

class Users_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
        $this->load->model ( 'system_model' );
    }

    public function check_user($email, $password)
    {
        $this->db->select('*');
        $this->db->from('USERS u');
        $this->db->where('u.email', $email);
        $this->db->where('u.password', $password);

        $query = $this->db->get();

        if($query->num_rows() != 0)
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    }

    public function get_all_users()
    {
        $this->db->select('IDUSERS, EMAIL, S3_BUCKET');
        $this->db->from('USERS u');

        $query = $this->db->get();

        if($query->num_rows() != 0)
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    }

}