<?php
class Results_Client_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
        $this->load->model ( 'system_model' );
    }

    public function get_by_ezRefString($ezRefString, $cleanUp = false, $numberOfRecords = 0) {

        $this->db->select('*');
        $this->db->from('RESULTS_CLIENT c');
        $this->db->join('FILES f', 'f.IDFILE = c.IDFILE', 'inner');
        $this->db->where('f.EZ_REF_STRING', $ezRefString);

        if ($cleanUp)
            $this->db->where('c.CLEANUP', 'Cleanup');
        else
            $this->db->where('c.CLEANUP', '');

        $this->db->order_by('c.IMAGE', 'asc');
        if ($numberOfRecords > 0)
        {
            $this->db->limit($numberOfRecords);
        }

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