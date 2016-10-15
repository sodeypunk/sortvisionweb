<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

class Results_Client_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
        $this->load->model ( 'system_model' );
    }

    public function get_by_ezRefString($ezRefString, $cleanUp = '', $numberOfRecords = 0) {

        $imageSql = "SELECT * FROM RESULTS_CLIENT c " .
                "INNER JOIN FILES f " .
                "ON f.IDFILE = c.IDFILE " .
                "WHERE f.EZ_REF_STRING = '" . $ezRefString . "' ";


        if ($cleanUp == 'true') {
            $imageSql .= "AND " .
                    "(c.CLEANUP = 'Cleanup') ";
        }
        elseif ($cleanUp == 'false') {
            $imageSql .= "AND (c.CLEANUP = '' || c.CLEANUP IS NULL) ";
        }

        $imageSql .= "ORDER BY c.CLEANUP, c.IMAGE ASC ";

        if ($numberOfRecords > 0)
        {
            $imageSql .= "LIMIT " . $numberOfRecords . " ";
        }

        $labelSql = "SELECT * FROM RESULTS_LABELS l " .
            "INNER JOIN FILES f " .
            "ON f.IDFILE = l.IDFILE " .
            "WHERE f.EZ_REF_STRING = '" . $ezRefString . "' ";

        $imageQuery = $this->db->query($imageSql);
        $LabelsQuery = $this->db->query($labelSql);
        $labelsResults = $LabelsQuery->result_array();

        if($imageQuery->num_rows() != 0)
        {
            $results = $imageQuery->result_array();
            $index = 0;
            foreach ($results as &$row)
            {
                $row['LABEL_ARRAY'] = util::bibStringToArray($labelsResults);
                $row['LABELS'] = util::bibArrayToString($labelsResults, $row['IMAGE'], false);
                $row['LABELS_REMOVED'] = util::bibArrayToString($labelsResults, $row['IMAGE'], true);
                $row['IMAGE'] = util::flatten($row['IMAGE']);
                $row['INDEX'] = $index;
                $index++;
            }
            return $results;
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