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
            $imageSql .= "AND (c.CLEANUP = 'Cleanup' || c.CLEANUP = 'Partial')";
        }
        elseif ($cleanUp == 'false') {
            $imageSql .= "AND (c.CLEANUP = '' || c.CLEANUP IS NULL) ";
        }
        elseif ($cleanUp == 'cleanup') {
            $imageSql .= "AND c.CLEANUP = 'Cleanup' ";
        }
        elseif ($cleanUp == 'partial') {
            $imageSql .= "AND c.CLEANUP = 'Partial' ";
        }

        $imageSql .= "ORDER BY c.CLEANUP, c.IMAGE ASC ";

        if ($numberOfRecords > 0)
        {
            $imageSql .= "LIMIT " . $numberOfRecords . " ";
        }

        $imageQuery = $this->db->query($imageSql);
        $imageResults = $imageQuery->result_array();

        if($imageQuery->num_rows() > 0)
        {
            $hashArray = array();
            foreach ($imageResults as $row) {
                array_push($hashArray, "'" . $row['HASH'] . "'");
            }
            $hashList = implode(",", $hashArray);

            $labelSql = "SELECT * FROM RESULTS_LABELS l " .
                "INNER JOIN FILES f " .
                "ON f.IDFILE = l.IDFILE " .
                "WHERE f.EZ_REF_STRING = '" . $ezRefString . "' " .
                "AND l.HASH IN (" . $hashList . ") " .
                "ORDER BY REMOVED DESC, LABEL";


            $LabelsQuery = $this->db->query($labelSql);
            $labelsResults = $LabelsQuery->result_array();

            $labelHashDict = array();
            foreach ($labelsResults as $row) {
                $hash = $row['HASH'];
                if (array_key_exists($hash, $labelHashDict))
                {
                    array_push($labelHashDict[$hash], $row);
                }
                else
                {
                    $labelHashDict[$hash] = array();
                    array_push($labelHashDict[$hash], $row);
                }
            }

            $index = 0;
            foreach ($imageResults as &$row) {

                $row['LABELS_ARRAY'] = util::labelsArrayFromAllArray($labelHashDict, $row['HASH']);
                $row['LABELS_STRING'] = util::bibArrayToString($labelHashDict, $row['HASH'], false);
                $row['LABELS_STRING_REMOVED'] = util::bibArrayToString($labelHashDict, $row['HASH'], true);

                $row['IMAGE_FLATTENED'] = util::flatten($row['IMAGE']);
                $row['INDEX'] = $index;
                $index++;
            }
            return $imageResults;
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
                'CLEANUP_STATUS' => $row['CLEANUP_STATUS'],
                'UPDT' => util::CurrentDateTime()
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
                    'UPDT' => util::CurrentDateTime()
                );

                array_push($saveLabelsArray, $labelsData);
            }

            array_push($saveClientArray, $clientData);
        }

        $rowsAffected = $this->db->update_batch('RESULTS_CLIENT', $saveClientArray, 'ID');
        $rowsAffected2 = $this->db->update_batch('RESULTS_LABELS', $saveLabelsArray, 'ID');

        if ($rowsAffected > 0 && $rowsAffected2 > 0)
        {
            return true;
        }

        return false;
    }

}