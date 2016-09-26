<?php
class Results_Client_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
        $this->load->model ( 'system_model' );
    }

    public function get_by_ezRefString($ezRefString, $cleanUp = '', $numberOfRecords = 0) {

        $sql = "SELECT * FROM RESULTS_CLIENT c " .
                "INNER JOIN FILES f " .
                "ON f.IDFILE = c.IDFILE " .
                "WHERE f.EZ_REF_STRING = '" . $ezRefString . "' ";


        if ($cleanUp == 'true') {
            $sql .= "AND c.CLEANUP = 'Cleanup' " .
                    "OR c.NEW_CLEANUP = 'Cleanup' " .
                    "OR c.NEW_CLEANUP = 'Partial' ";
        }
        elseif ($cleanUp == 'false') {
            $sql .= "AND (c.CLEANUP = '' || c.CLEANUP IS NULL) " .
                    "AND (c.NEW_CLEANUP = '' || c.NEW_CLEANUP IS NULL) ";
        }

        $sql .= "ORDER BY c.CLEANUP, c.IMAGE ASC ";

        if ($numberOfRecords > 0)
        {
            $sql .= "LIMIT " . $numberOfRecords . " ";
        }

        $query = $this->db->query($sql);

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
                'NEW_CLEANUP' => $row['NEW_CLEANUP'],
                'UPDT' => $row['UPDT'],
                'LABEL_REMOVED' => $row['LABEL_REMOVED']

            );

                array_push($saveArray, $data);
        }

        $this->db->update_batch('RESULTS_CLIENT', $saveArray, 'ID');
    }

}