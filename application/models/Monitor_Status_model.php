<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

class Monitor_Status_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    public function get_demo_status()
    {
        $sql = "SELECT * FROM MONITOR_STATUS " .
            "WHERE ID = 1 AND TYPE = 'DEMO'";


        $query = $this->db->query($sql);

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