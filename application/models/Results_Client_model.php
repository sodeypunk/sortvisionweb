<?php
class Results_Client_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
        $this->load->model ( 'system_model' );
    }

    public function get_by_ezRefString($ezRefString, $cleanUp = '', $numberOfRecords = 0) {

        $this->db->select('*');
        $this->db->from('RESULTS_CLIENT c');
        $this->db->join('FILES f', 'f.IDFILE = c.IDFILE', 'inner');
        $this->db->where('f.EZ_REF_STRING', $ezRefString);

        if ($cleanUp == 'true') {
            $this->db->where('c.CLEANUP', 'Cleanup');
        }
        elseif ($cleanUp == 'false') {
            $this->db->where('c.CLEANUP', '');
        }

        $this->db->order_by('c.CLEANUP, c.IMAGE', 'asc');
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
            return array();
        }

    }

    public function UpdateResultsClient($rows) {

        $saveArray = array();
        foreach ($rows as $row)
        {
            $data = array(
                'ID' => $row['ID'],
                'IDFILE' => $row['IDFILE'],
                'IMAGE' => $row['IMAGE'],
                'LABEL' => $row['LABEL'],
                'COORDINATES' => $row['COORDINATES'],
                'IMAGE_SIZE' => $row['IMAGE_SIZE'],
                'CLEANUP' => $row['CLEANUP'],
                'UPDT' => $row['UPDT'],
                'LABEL_REMOVED' => $row['LABEL_REMOVED']

            );

                array_push($saveArray, $data);
        }

        $this->db->update_batch('RESULTS_CLIENT', $saveArray, 'ID');
    }

}