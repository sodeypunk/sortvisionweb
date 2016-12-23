<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

class Results_Client_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
        $this->load->model ( 'system_model' );
    }

    public function get_count_by_ezRefString($ezRefString, $cleanUp = '', $numberOfRecords = 0)
    {
        $sql = "SELECT COUNT(*) as COUNT FROM RESULTS_CLIENT c " .
            "INNER JOIN FILES f " .
            "ON f.IDFILE = c.IDFILE " .
            "WHERE f.EZ_REF_STRING = '" . $ezRefString . "' ";


        $sql = $this->AddWhereClause($sql, $cleanUp, $numberOfRecords);
        $countQuery = $this->db->query($sql);
        $countResults = $countQuery->result_array();

        return (int)$countResults[0]['COUNT'];
    }
    public function get_by_s3Path($ezRefString, $cleanUp = '', $numberOfRecords = 0) {

        $imageSql = "SELECT * FROM RESULTS_CLIENT c " .
                "INNER JOIN FILES f " .
                "ON f.IDFILE = c.IDFILE " .
                "WHERE f.EZ_REF_STRING = '" . $ezRefString . "' ";


        $imageSql = $this->AddWhereClause($imageSql, $cleanUp, $numberOfRecords);

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

    private function AddWhereClause($sql, $cleanUp, $numberOfRecords)
    {
        if ($cleanUp == 'true') {
            $sql .= "AND (c.CLEANUP = 'Cleanup' || c.CLEANUP = 'Partial')";
        }
        elseif ($cleanUp == 'false') {
            $sql .= "AND (c.CLEANUP = '' || c.CLEANUP IS NULL) ";
        }
        elseif ($cleanUp == 'cleanup') {
            $sql .= "AND c.CLEANUP = 'Cleanup' ";
        }
        elseif ($cleanUp == 'partial') {
            $sql .= "AND c.CLEANUP = 'Partial' ";
        }

        $sql .= "ORDER BY c.CLEANUP, c.IMAGE ASC ";

        if ($numberOfRecords > 0)
        {
            $sql .= "LIMIT " . $numberOfRecords . " ";
        }

        return $sql;
    }

    public function UpdateResultsClient($rows) {

        $saveClientArray = array();
        $saveLabelsArray = array();
        $insertLabelsArray = array();

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

            if (array_key_exists('LABELS_ARRAY', $row)) {
                $labelsArray = $row['LABELS_ARRAY'];
                foreach ($labelsArray as $label) {
                    $labelsData = array(
                        'ID' => $label['ID'],
                        'IDFILE' => $label['IDFILE'],
                        'LABEL' => $label['LABEL'],
                        'COORDINATE' => $label['COORDINATE'],
                        'REMOVED' => $label['REMOVED'],
                        'UPDT' => util::CurrentDateTime()
                    );

                    if ((int)$labelsData['ID'] > 0)
                    {
                        array_push($saveLabelsArray, $labelsData);
                    }
                    else
                    {
                        array_push($insertLabelsArray, $labelsData);
                    }


                }
            }

            array_push($saveClientArray, $clientData);
        }

        $rowsAffected = 0;
        $rowsAffected2 = 0;
        if (count($saveClientArray)> 0) {
            $rowsAffected = $this->db->update_batch('RESULTS_CLIENT', $saveClientArray, 'ID');
        }
        if (count($saveLabelsArray)> 0) {
            $this->db->update_batch('RESULTS_LABELS', $saveLabelsArray, 'ID');
        }
        if (count($insertLabelsArray)> 0) {
            $this->db->insert('RESULTS_LABELS', $insertLabelsArray);
        }

        if ($rowsAffected > 0)
        {
            return true;
        }

        return false;
    }

}