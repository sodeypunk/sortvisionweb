<?php

class TestClass2
{

}
class util
{
    public static function  getImagesTiled($sourcePath, $filePaths)
    {
        $resultHTML = "";
        $count = 0;
        foreach ($filePaths as $filePath) {
            $fileNm = pathinfo($filePath)['basename'];

            $image_properties = array(
                'src' => $sourcePath . $filePath,
                'alt' => $fileNm,
                'class' => 'img-responsive',
                //'width' => '200',
                //'height' => '200',
                'title' => $fileNm,
                'rel' => 'lightbox',
            );

            $resultHTML .= '<div class="row">';
            $resultHTML .= '<div class="col-lg-12">';
            $resultHTML .=  img($image_properties);
            $resultHTML .=  '</div>';
            $resultHTML .=  '</div>';

            $count++;
        }

        return $resultHTML;
    }

    public static function  getImagesTiledFromDB($data, $sourcePath, $fileId)
    {

        $resultHTML = "";
        $count = 0;
        if ($data != false) {

            foreach ($data as $row) {

                $image_properties = self::buildImageHTML($row, $sourcePath);

                if ($count % 3 == 0 || $count == 0) {
                    $resultHTML .= '<div class="row">';
                }

                $resultHTML .= '<a href="' . $sourcePath . $row["IMAGE_FLATTENED"] . '" data-toggle="lightbox" data-gallery="image-gallery" class="col-sm-4">';
                $resultHTML .= img($image_properties);
                $resultHTML .= '</a>';

                //$resultHTML .= '<div class="col-md-4">';
                //$resultHTML .= img($image_properties);
                //$resultHTML .= '</div>';

                if (($count + 1) % 3 == 0) {
                    $resultHTML .= '</div>';
                }

                $count++;
            }
        }

        return $resultHTML;
    }

    public static function  getImagesTiledFromDBForCleanup($data, $sourcePath)
    {

        $resultHTML = "";
        $count = 0;
        if ($data != false) {

            foreach ($data as $row) {

                $removedLabels = self::BadLabels($row);
                $goodLabels = self::GoodLabels($row);

                if ($count == 0) {
                    $image_properties = self::buildImageHTML($row, $sourcePath, true);
                }
                else
                {
                    $image_properties = self::buildImageHTML($row, $sourcePath, false);
                }

                if ($count % 3 == 0 || $count == 0) {
                    $resultHTML .= '<div class="row">';
                }

                $resultHTML .= '<div class="col-md-4">';
                $resultHTML .= img($image_properties);

                $resultHTML .= '<br>';

                foreach ($removedLabels as $label) {
                    $resultHTML .= '<br><input type="checkbox" name="' . $row['IDFILE'] . '" value="' . $label['LABEL'] . '"> ' . $label['LABEL'];
                }
                foreach ($goodLabels as $label) {
                    $resultHTML .= '<br><input type="checkbox" name="' . $row['IDFILE'] . '" value="' . $label['LABEL'] . '" checked> ' . $label['LABEL'];
                }

                $resultHTML .= '<br><br></div>';

                if (($count + 1) % 3 == 0) {
                    $resultHTML .= '</div>';
                }

                $count++;
            }
        }

        return $resultHTML;
    }

    public static function buildImageHTML($row, $sourcePath, $selected = false)
    {
        $src = "";
        $cleanUp = 'false';
        if (strtolower($row['CLEANUP']) == "cleanup")
        {
            $cleanUp = 'true';
        }

        if ($cleanUp == 'true')
        {
            $src = $sourcePath . "cleanup/" . $row["IMAGE_FLATTENED"];
        }

        elseif
        ($cleanUp == 'false')
        {
            $src = $sourcePath . $row["IMAGE_FLATTENED"];
        }

        $glowBorderClass = "";
        if ($selected)
        {
            $glowBorderClass = "glowing-border-selected";
        }

        $image_properties = array(
            'src' => $src,
            'alt' => $row["IMAGE_FLATTENED"],
            'class' => 'img-responsive ' . $glowBorderClass,
            //'width' => '200',
            //'height' => '200',
            'title' => $row["IMAGE_FLATTENED"],
            'rel' => 'lightbox',
        );

        return $image_properties;
    }

    public static function flatten($string)
    {
        $newString = str_replace("/", "^", $string);
        $newString = str_replace(" ", "+", $newString);
        $newString = preg_replace('/\\.[^.\\s]{3,4}$/', '', $newString);
        $newString = $newString . ".jpg"; // for showing result images

        return $newString;
    }

//    public static function bibStringToArray($labelsArray)
//    {
//        $finalArrayList = array();
//
//        $index = 0;
//        foreach($labelsArray as $label)
//        {
//            $newObj = array(
//                "index" => $index,
//                "label" => $label['LABEL'],
//                "cleanup" => $label['REMOVED'] == 1
//            );
//
//            $index++;
//
//            array_push($finalArrayList, $newObj);
//        }
//
//        return $finalArrayList;
//    }

    public static function bibArrayToStringSlow($labelsArray, $image, $isRemoved)
    {
        $labelArray = array();

        foreach($labelsArray as $label)
        {
            if ($label['IMAGE'] == $image || $image == null) {
                if ($isRemoved == true) {
                    if ((int)$label['REMOVED'] == 1) {
                        array_push($labelArray, $label['LABEL']);
                    }
                } else {
                    if ((int)$label['REMOVED'] == 0) {
                        array_push($labelArray, $label['LABEL']);
                    }
                }
            }
        }

        $bibString = implode(',', $labelArray);
        return $bibString;

    }

    public static function bibArrayToString($labelHashDict, $hash, $isRemoved)
    {
        $labelArray = array();
        $allLabelsArray = array();

        if (array_key_exists($hash, $labelHashDict))
        {
            $allLabelsArray = $labelHashDict[$hash];
        }

        foreach($allLabelsArray as $label)
        {
            if ($isRemoved == true) {
                if ((int)$label['REMOVED'] == 1) {
                    array_push($labelArray, $label['LABEL']);
                }
            } else {
                if ((int)$label['REMOVED'] == 0) {
                    array_push($labelArray, $label['LABEL']);
                }
            }
        }

        $bibString = implode(',', $labelArray);
        return $bibString;

    }

    public static function labelsArrayFromAllArray($labelHashDict, $hash)
    {
        $newLabelsArray = array();
        $allLabelsArray = array();

        if (array_key_exists($hash, $labelHashDict))
        {
            $allLabelsArray = $labelHashDict[$hash];
        }

        $index = 0;
        foreach($allLabelsArray as $label)
        {
            $label['INDEX'] = $index;
            $label['INCLUDED'] = ($label['REMOVED'] == '0');
            array_push($newLabelsArray, $label);
            $index++;
        }

        return $newLabelsArray;
    }

    public static function GoodLabels($row)
    {
        $labelsArray = $row['LABELS_ARRAY'];
        $goodLabels = array();

        foreach ($labelsArray as $label)
        {
            if ((int)$label['REMOVED'] == 0)
            {
                array_push($goodLabels, $label);
            }
        }

        return $goodLabels;
    }

    public static function BadLabels($row)
    {
        $labelsArray = $row['LABELS_ARRAY'];
        $badLabels = array();

        foreach ($labelsArray as $label)
        {
            if ((int)$label['REMOVED'] == 1)
            {
                array_push($badLabels, $label);
            }
        }

        return $badLabels;
    }

    public static function CurrentDateTime()
    {
        return date("Y-m-d H:i:s");
    }

    public static function GetResultImagePath($fileId)
    {
        $resultImagePath = sprintf("https://s3.amazonaws.com/bibsmart-results/%s/recognition_images/", $fileId);
        return $resultImagePath;
    }

    public static function AddLinks($rows)
    {
        foreach ($rows as &$row)
        {
            $fileid = $row['IDFILE'];
            $resultLink = sprintf('<a href="%s?fileid=%s">Result</a>', base_url("index.php/files/status"), $fileid);
            $jsonLink = sprintf('<a href="%sindex.php/files/clientResultsJSON?fileid=%s">JSON</a>', base_url(), $fileid);
            $analysisLink = sprintf('<a href="%s?fileid=%s">Analysis</a>', base_url("index.php/analysis/index"), $fileid);
            $cleanupLink = sprintf('<a href="%s?fileid=%s">Cleanup</a>', base_url("index.php/cleanup"), $fileid);

            $row['LINKS'] = sprintf('%s | %s | %s | %s', $analysisLink, $cleanupLink, $jsonLink, $resultLink);
        }

        return $rows;
    }

    public static  function CallAPI($method, $url, $header, $data)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

}