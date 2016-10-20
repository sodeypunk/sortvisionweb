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
                    "(c.CLEANUP = 'Cleanup') || (c.CLEANUP = 'PARTIAL')";
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
            "WHERE f.EZ_REF_STRING = '" . $ezRefString . "' " .
            "ORDER BY REMOVED DESC, LABEL";

        $imageQuery = $this->db->query($imageSql);
        $LabelsQuery = $this->db->query($labelSql);
        $labelsResults = $LabelsQuery->result_array();

        if($imageQuery->num_rows() != 0)
        {
            $results = $imageQuery->result_array();
            $index = 0;
            foreach ($results as &$row)
            {
                $row['LABELS_ARRAY'] = util::labelsArrayFromAllArray($labelsResults, $row['IMAGE']);
                $row['LABELS_STRING'] = util::bibArrayToString($labelsResults, $row['IMAGE'], false);
                $row['LABELS_STRING_REMOVED'] = util::bibArrayToString($labelsResults, $row['IMAGE'], true);
                $row['IMAGE_FLATTENED'] = util::flatten($row['IMAGE']);
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

        $saveClientArray = array();
        $saveLabelsArray = array();
        foreach ($rows as $row)
        {
            $clientData = array(
                'ID' => $row['ID'],
                'IDFILE' => $row['IDFILE'],
                'IMAGE' => $row['IMAGE'],
                'IMAGE_SIZE' => $row['IMAGE_SIZE'],
                'CLEANUP' => $row['CLEANUP'],
                'UPDT' => $row['UPDT']
            );

            $labelsArray = $row['LABELS_ARRAY'];
            foreach ($labelsArray as $label)
            {
                $labelsData = array(
                    'ID' => $label['ID'],
                    'IDFILE' => $label['IDFILE'],
                    'LABEL' => $label['LABEL'],
                    'COORDINATE' => $label['COORDINATE'],
                    'REMOVED' => $label['REMOVED'],
                    'UPDT' => $label['UPDT']
                );

                array_push($saveLabelsArray, $labelsData);
            }

            array_push($saveClientArray, $clientData);
        }

        $this->db->update_batch('RESULTS_CLIENT', $saveClientArray, 'ID');
        $this->db->update_batch('RESULTS_LABELS', $saveLabelsArray, 'ID');
    }

}