<?php
class System_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
    }

    public function get_all() {

        $this->db->select('*');
        $this->db->from('SYSTEM s');
        $this->db->order_by('s.HOSTNAME', 'asc');
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

    public function get_system_by_id($id) {
        $this->db->select('*');
        $this->db->from('SYSTEM s');
        $this->db->where('s.IDSYSTEM', $id);
        $query = $this->db->get();

        if($query->num_rows() != 0)
        {
            return $query->row();
        }
        else
        {
            return false;
        }
    }
}