<?php
$mysql_host = "localhost";
$mysql_user = "share7_root";
$mysql_password = "makingitrain2015";
$mysql_db = "share7_sortvision_labels";


$rootImagePath = "image_sets/";
$resultsDetailedCSV = "/resultsDetailed.csv";

function sanitize($data, $conn)
{
	// remove whitespaces (not a must though)
	$data = trim($data);
	 
	// apply stripslashes if magic_quotes_gpc is enabled
	if(get_magic_quotes_gpc())
	{
		$data = stripslashes($data);
	}
	 
	// a mySQL connection is required before using this function
	$data = mysqli_real_escape_string($conn, $data);
	 
	return $data;
}

$action = $_POST["action"];

if ($action == "load")
{
	$imageSet = $_POST["imageSet"];
	$resultsDetailedPath = $rootImagePath . $imageSet . $resultsDetailedCSV;
	
	$csv = file_get_contents($resultsDetailedPath);

	echo $csv;
}
else if ($action == "getImageInfo")
{

	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	    exit;
	}

	$imageSet = sanitize($_POST["imageSet"], $mysqli);
	$imageKey = sanitize($_POST["imageKey"], $mysqli);
	$cleanUpOnly = sanitize($_POST["cleanUpOnly"], $mysqli);

	$sql    = "SELECT LABELS, S.DONE FROM LABELS L " .
			  "INNER JOIN RACES R " .
			  "	ON L.RACE_ID = R.ID " .
			  "LEFT JOIN IMAGES_STATUS S " .
			  "	ON S.IMAGE = '" . strtolower($imageKey) . "' " .
			  "WHERE LCASE(R.RACE_NAME) = '" . strtolower($imageSet) . "' " .
			  "AND LCASE(L.IMAGE) = '" . strtolower($imageKey) . "'";

	$sql2    = "SELECT FOUND, TOTAL, PERCENTAGE, FALSE_POS, SOURCE, RESULT FROM RESULTS_COMPARE C " .
			  "INNER JOIN RACES R " .
			  "	ON C.RACE_ID = R.ID " .
			  "WHERE LCASE(R.RACE_NAME) = '" . strtolower($imageSet) . "' " .
			  "AND LCASE(C.IMAGE) = '" . strtolower($imageKey) . "'";

	$sql3    = "SELECT MAX(P.UPDT) PATCH_UPDT FROM `PATCHES_EXPORT` P " .
			   "INNER JOIN RACES R " .
			   "	    ON P.RACE_ID = R.ID " .
			   "WHERE LCASE(R.RACE_NAME) = '" . strtolower($imageSet) . "' " .
			   "AND P.PATCH LIKE '" . $imageKey . "%' ";

	$sql4    = "SELECT PATCH, LABEL FROM `PATCHES_EXPORT` P " .
			   "INNER JOIN RACES R " .
			   "	    ON P.RACE_ID = R.ID " .
			   "WHERE LCASE(R.RACE_NAME) = '" . strtolower($imageSet) . "' " .
			   "AND P.PATCH LIKE '" . $imageKey . "%' ";

	$sql5 = "";
	if ($cleanUpOnly == "true")
	{
		$sql5    =  "select DISTINCT IMAGE_NAME AS IMAGE, MAX_ENSEMBLE, MAX(E.UPDT) PATCH_UPDT, S.DONE FROM " .
					"( " .
					"select IMAGE_NAME, MAX(ENSEMBLE) as MAX_ENSEMBLE from RESULTS_DETAILED D " .
					"INNER JOIN RACES R " .
					"	ON D.RACE_ID = R.ID " .
					"WHERE LCASE(R.RACE_NAME) = '" . strtolower($imageSet) . "' " .
					"GROUP BY IMAGE_NAME " .
					"HAVING MAX_ENSEMBLE <= 2 " .
					" " .
					"UNION " .
					" " .
					"SELECT IMAGE_NAME, ENSEMBLE as MAX_ENSEMBLE from RESULTS_DETAILED D " .
					"INNER JOIN RACES R " .
					"	ON D.RACE_ID = R.ID " .
					"WHERE LCASE(R.RACE_NAME) = '" . strtolower($imageSet) . "' " .
					"AND ENSEMBLE = 2 " .
					"GROUP BY IMAGE_NAME, ENSEMBLE " .
					") R " .
					"LEFT JOIN PATCHES_EXPORT E " .
					"	ON E.IMAGE = R.IMAGE_NAME " .
					"LEFT JOIN IMAGES_STATUS S " .
			  	    "	ON S.IMAGE = R.IMAGE_NAME " .
					"GROUP BY IMAGE_NAME, MAX_ENSEMBLE " .
					"ORDER BY S.DONE DESC, PATCH_UPDT DESC, MAX_ENSEMBLE DESC, IMAGE_NAME ";
	}
	else
	{
		$sql5    = "SELECT C.IMAGE, C.PERCENTAGE, C.FALSE_POS, MAX(E.UPDT) PATCH_UPDT, S.DONE FROM RESULTS_COMPARE C " .
				   "INNER JOIN RACES R " .
				   "	ON C.RACE_ID = R.ID " .
				   "LEFT JOIN PATCHES_EXPORT E " .
				   "	ON E.IMAGE = C.IMAGE " .
				   "LEFT JOIN IMAGES_STATUS S " .
			  	   "	ON S.IMAGE = C.IMAGE " .
				   "WHERE LCASE(R.RACE_NAME) = '" . strtolower($imageSet) . "' " .
				   "AND (C.PERCENTAGE < 1 OR C.FALSE_POS > 0) " .
				   "GROUP BY C.IMAGE " .
				   "ORDER BY S.DONE DESC, PATCH_UPDT DESC, C.PERCENTAGE, C.FALSE_POS, C.IMAGE";
	}

	$result = $mysqli->query($sql);
	$result->data_seek(0);
	$row = $result->fetch_assoc();

	$result2 = $mysqli->query($sql2);
	$result2->data_seek(0);
	$row2 = $result2->fetch_assoc();

	$result3 = $mysqli->query($sql3);
	$result3->data_seek(0);
	$row3 = $result3->fetch_assoc();

	$result4 = $mysqli->query($sql4);
	
	$patchArray = array();
	while ($rowResult = $result4->fetch_assoc())
	{
		$patchArray[$rowResult['PATCH']] = $rowResult['LABEL'];
	}

	$result5 = $mysqli->query($sql5);
	
	$compareArray = array();
	while ($rowResult = $result5->fetch_assoc())
	{
		$compareArray[$rowResult['IMAGE']] = (object)array('PERCENTAGE' => $rowResult['PERCENTAGE'], 'FALSE_POS' => $rowResult['FALSE_POS'], 'PATCH_UPDT' => $rowResult['PATCH_UPDT'], 'DONE' => $rowResult['DONE']);
	}

	$data[] = array('LABELS' => $row['LABELS'], 'DONE' => $row['DONE'], 'FOUND' => $row2['FOUND'], 'TOTAL' => $row2['TOTAL'], 'PERCENTAGE' => $row2['PERCENTAGE'], 'FALSE_POS' => $row2['FALSE_POS'], 'SOURCE' => $row2['SOURCE'], 'RESULT' => $row2['RESULT'], 'PATCH_UPDT' => $row3['PATCH_UPDT'], 'PATCHES' => $patchArray, 'COMPARE_LIST' => $compareArray);

	echo json_encode($data);

	$result->close();
	$mysqli->close();

}
else if ($action == "saveLabel")
{
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	    exit;
	}

	$imageSet = sanitize($_POST["imageSet"], $mysqli);
	$imageKey = sanitize($_POST["imageKey"], $mysqli);
	$labels = sanitize($_POST["labels"], $mysqli);

	$sql    = "INSERT INTO LABELS (RACE_ID, IMAGE, LABELS, UPDT) " .
			  "		SELECT ID, '" . $imageKey . "', '" . $labels . "', NULL " .
			  "		FROM RACES WHERE LCASE(RACE_NAME) = '" . strtolower($imageSet) . "' " .
			  "ON DUPLICATE KEY UPDATE " .
			  " 	LABELS = VALUES(LABELS), " .
			  "		UPDT = VALUES(UPDT); ";

	$result = $mysqli->query($sql);
	$rowsAffected = $mysqli->affected_rows;

	echo $rowsAffected;

	$mysqli->close();
}
else if ($action == "updateImageStatus")
{
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	    exit;
	}

	$imageSet = sanitize($_POST["imageSet"], $mysqli);
	$imageKey = sanitize($_POST["imageKey"], $mysqli);
	$isDone = sanitize($_POST["isDone"], $mysqli);

	$sql    = "INSERT INTO IMAGES_STATUS " .
			  "		SELECT ID, '" . $imageKey . "', " . $isDone . ", NULL " .
			  "	    FROM RACES WHERE LCASE(RACE_NAME) = '" . strtolower($imageSet) . "' " .
			  "ON DUPLICATE KEY UPDATE " .
			  "		DONE = VALUES(DONE), " .
			  "	    UPDT = VALUES(UPDT) ";

	$result = $mysqli->query($sql);
	$rowsAffected = $mysqli->affected_rows;

	echo $rowsAffected;

	$mysqli->close();
}
else if ($action == "exportPatches")
{
	$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db);
	if ($mysqli->connect_errno) {
	    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	    exit;
	}

	$imageSet = sanitize($_POST["imageSet"], $mysqli);
	$data = $_POST["data"];

	$patchesArray = json_decode($data, true);
	$sql = "";

	if (is_array($patchesArray) || is_object($patchesArray))
	{
		foreach ($patchesArray as $patch)
		{
			$sql = $sql . 
				"INSERT INTO PATCHES_EXPORT (RACE_ID, PATCH, IMAGE, LABEL, UPDT) " .
				"    SELECT ID, '" . $patch["PATCH"] . "', '" . $patch["IMAGE"] . "', '" . $patch["LABEL"] . "', NULL " .
				"    FROM RACES WHERE LCASE(RACE_NAME) = '" . strtolower($imageSet) . "' " .
				"ON DUPLICATE KEY UPDATE " .
				"    LABEL = VALUES(LABEL), " .
				"	 UPDT = VALUES(UPDT); ";
		}

		$result = $mysqli->multi_query($sql);
		$rowsAffected = $mysqli->affected_rows;

		echo $rowsAffected;

		$mysqli->close();
	}
	else
	{
		echo "Not array!";
	}
}
else
{
	echo "No Valid Commands";
}

?>