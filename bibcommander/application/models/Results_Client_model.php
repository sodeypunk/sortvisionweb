<?php

require_once(dirname(__DIR__)."/controllers/Utility.php");

class Results_Client_model extends CI_Model {

    public function __construct() {
        $this->load->database ();
        $this->load->model ( 'system_model' );
    }

    public function get_count_by_fileId($fileId, $cleanUp = '', $numberOfRecords = 0)
    {
        $sql = "SELECT COUNT(*) as COUNT FROM RESULTS_CLIENT c " .
            "INNER JOIN FILES f " .
            "ON f.IDFILE = c.IDFILE " .
            "INNER JOIN SPARK_JOBS j " .
            "ON f.IDFILE = j.IDFILE " .
            "WHERE f.IDFILE = '" . $fileId . "' ";


        $sql = $this->AddWhereClause($sql, $cleanUp, $numberOfRecords);
        $countQuery = $this->db->query($sql);
        $countResults = $countQuery->result_array();

        return (int)$countResults[0]['COUNT'];
    }

    public function get_cleanup_status_count($fileId, $cleanUp = '', $status)
    {
        $sql = "SELECT COUNT(*) as COUNT FROM RESULTS_CLIENT c " .
            "INNER JOIN FILES f " .
            "ON f.IDFILE = c.IDFILE " .
            "INNER JOIN SPARK_JOBS j " .
            "ON f.IDFILE = j.IDFILE " .
            "WHERE f.IDFILE = '" . $fileId . "' " .
            "AND CLEANUP_STATUS = '" . $status . "' ";


        $sql = $this->AddWhereClause($sql, $cleanUp);
        $countQuery = $this->db->query($sql);
        $countResults = $countQuery->result_array();

        return (int)$countResults[0]['COUNT'];
    }

    public function get_by_fileId($fileId, $cleanUp = '', $batch = 0, $page = 1) {

        $imageSql = "SELECT * FROM RESULTS_CLIENT c " .
                "INNER JOIN FILES f " .
                "ON f.IDFILE = c.IDFILE " .
                "INNER JOIN SPARK_JOBS j " .
                "ON f.IDFILE = j.IDFILE " .
                "WHERE f.IDFILE = '" . $fileId . "' ";


        $imageSql = $this->AddWhereClause($imageSql, $cleanUp, $batch, $page);

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
                "WHERE f.IDFILE = '" . $fileId . "' " .
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

            // Construct the image path
            $fileNameWithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $imageResults[0]['FILE_NAME']);
            $sourcePath = Util::GetResultImagePath($imageResults[0]['S3_BUCKET'], $imageResults[0]['IDFILE'], $imageResults[0]['IDJOB'], $fileNameWithoutExt);
            foreach ($imageResults as &$row) {

                $row['LABELS_ARRAY'] = util::labelsArrayFromAllArray($labelHashDict, $row['HASH']);
                $row['LABELS_STRING'] = util::bibArrayToString($labelHashDict, $row['HASH'], false);
                $row['LABELS_STRING_REMOVED'] = util::bibArrayToString($labelHashDict, $row['HASH'], true);

                $row['IMAGE_FLATTENED'] = util::flatten($row['IMAGE']);

                $imagePath = $sourcePath . $row["IMAGE_FLATTENED"];
                $imagePathLightBox = '<a href="' . $imagePath . '" data-toggle="lightbox" data-gallery="image-gallery" class="col-sm-4">';
                $imagePathLightBox .= $row['IMAGE_FLATTENED'];
                $imagePathLightBox .= '</a>';

                $row['IMAGE_PATH'] = $imagePath;
                $row['IMAGE_PATH_LIGHTBOX'] = $imagePathLightBox;
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

    private function AddWhereClause($sql, $cleanUp, $batch = 0, $page = 1)
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

        $offset = ($page * $batch) - $batch;
        if ($batch > 0)
        {
            $sql .= "LIMIT " . $batch . " OFFSET " . $offset . " ";
        }

        return $sql;
    }

    public function UpdateResultsClient($rows) {

        $saveClientArray = array();
        $saveLabelsArray = array();
        $insertLabelsArray = array();

        foreach ($rows as $row)
        {
            $hash = $row['HASH'];
            $clientData = array(
                'ID' => $row['ID'],
                'CLEANUP_STATUS' => $row['CLEANUP_STATUS'],
                'REVIEWER_ID' => $row['REVIEWER_ID'],
                'UPDT' => util::CurrentDateTime()
            );

            if (array_key_exists('LABELS_ARRAY', $row)) {
                $labelsArray = $row['LABELS_ARRAY'];
                foreach ($labelsArray as $label) {
                    $labelsData = array(
                        'ID' => $label['ID'],
                        'IDFILE' => $label['IDFILE'],
                        'IMAGE' => $label['IMAGE'],
                        'LABEL' => $label['LABEL'],
                        'COORDINATE' => $label['COORDINATE'],
                        'REMOVED' => intval($label['REMOVED']),
                        'UPDT' => util::CurrentDateTime(),
                        'HASH' => $hash
                    );

                    if ((int)$labelsData['ID'] > 0)
                    {
                        array_push($saveLabelsArray, $labelsData);
                    }
                    else
                    {
                        $labelsData['ID'] = null;
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
            $this->db->insert_batch('RESULTS_LABELS', $insertLabelsArray);
        }

        if ($rowsAffected > 0)
        {
            return true;
        }

        return false;
    }

    public function get_review_count_for_user($fileId, $userid)
    {
        $sql = "SELECT COUNT(*) as COUNT FROM RESULTS_CLIENT c " .
            "INNER JOIN FILES f " .
            "ON f.IDFILE = c.IDFILE " .
            "INNER JOIN SPARK_JOBS j " .
            "ON f.IDFILE = j.IDFILE " .
            "WHERE f.IDFILE = '" . $fileId . "' " .
            "AND REVIEWER_ID = '" . $userid . "' ";


        $sql = $this->AddWhereClause($sql, 'true');
        $countQuery = $this->db->query($sql);
        $countResults = $countQuery->result_array();

        return (int)$countResults[0]['COUNT'];
    }

    public function get_review_count_for_all_users($fileId)
    {
        $sql = "SELECT c.IDFILE, REVIEWER_ID, EMAIL, COUNT(*) as COUNT FROM RESULTS_CLIENT c " .
            "INNER JOIN FILES f " .
            "ON f.IDFILE = c.IDFILE " .
            "INNER JOIN SPARK_JOBS j " .
            "ON f.IDFILE = j.IDFILE " .
            "LEFT JOIN USERS u " .
            "ON c.REVIEWER_ID = u.IDUSERS " .
            "WHERE f.IDFILE = '" . $fileId . "' " .
            "AND (c.CLEANUP = 'Cleanup' || c.CLEANUP = 'Partial') " .
            "GROUP BY IDFILE, REVIEWER_ID, EMAIL " .
            "ORDER BY c.CLEANUP, c.IMAGE ASC ";

        $sqlActual = "SELECT c.IDFILE, REVIEWER_ID, EMAIL, COUNT(*) as COUNT FROM RESULTS_CLIENT c " .
            "INNER JOIN FILES f " .
            "ON f.IDFILE = c.IDFILE " .
            "INNER JOIN SPARK_JOBS j " .
            "ON f.IDFILE = j.IDFILE " .
            "LEFT JOIN USERS u " .
            "ON c.REVIEWER_ID = u.IDUSERS " .
            "WHERE f.IDFILE = '" . $fileId . "' " .
            "AND (c.CLEANUP = 'Cleanup' || c.CLEANUP = 'Partial') " .
            "AND CLEANUP_STATUS = 'REVIEWED' " .
            "GROUP BY IDFILE, REVIEWER_ID, EMAIL " .
            "ORDER BY c.CLEANUP, c.IMAGE ASC ";


        $countQuery = $this->db->query($sql);
        $countResults = $countQuery->result_array();

        $countActualQuery = $this->db->query($sqlActual);
        $countActualResults = $countActualQuery->result_array();

        $totalImageCount = $this->get_count_by_fileId($fileId, $cleanUp = 'true');

        foreach ($countResults as &$row) {
            if (empty($row['REVIEWER_ID']))
            {
                $row['EMAIL'] = 'Unassigned';
            }

            $count = $row['COUNT'];
            $percent = ROUND(($count / $totalImageCount) * 100, 2);
            $row['PERCENT'] = $percent;
            $row['COMPLETED_PERCENT'] = 0;
            $row['COMPLETED_COUNT'] = 0;

            foreach ($countActualResults as $actualRow) {
                if ($actualRow['REVIEWER_ID'] == $row['REVIEWER_ID'])
                {
                    $actualCount = $actualRow['COUNT'];
                    $row['COMPLETED_PERCENT'] = ROUND(($actualCount / $count) * 100, 2);
                    $row['COMPLETED_COUNT'] = $actualCount;
                    break;
                }
            }

        }
        return $countResults;
    }

}