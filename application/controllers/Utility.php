<?php

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

    public static function  getImagesTiledFromDB($data, $sourcePath, $ezRefString)
    {

        $resultHTML = "";
        $count = 0;
        if ($data != false) {

            foreach ($data as $row) {

                $image_properties = self::buildImageHTML($row, $sourcePath, $ezRefString);

                if ($count % 3 == 0 || $count == 0) {
                    $resultHTML .= '<div class="row">';
                }

                $resultHTML .= '<div class="col-md-4">';
                $resultHTML .= img($image_properties);
                $resultHTML .= '</div>';

                if (($count + 1) % 3 == 0) {
                    $resultHTML .= '</div>';
                }

                $count++;
            }
        }

        return $resultHTML;
    }

    public static function  getImagesTiledFromDBForCleanup($data, $sourcePath, $ezRefString)
    {

        $resultHTML = "";
        $count = 0;
        if ($data != false) {

            foreach ($data as $row) {

                $allLabels = array_filter(explode(",", $row['LABEL']), 'strlen');
                $removedLabels = array_filter(explode(",", $row['LABEL_REMOVED']), 'strlen');
                $goodLabels = array_diff($allLabels, $removedLabels);

                if ($count == 0) {
                    $image_properties = self::buildImageHTML($row, $sourcePath, $ezRefString, true);
                }
                else
                {
                    $image_properties = self::buildImageHTML($row, $sourcePath, $ezRefString);
                }

                if ($count % 3 == 0 || $count == 0) {
                    $resultHTML .= '<div class="row">';
                }

                $resultHTML .= '<div class="col-md-4">';
                $resultHTML .= img($image_properties);

                $resultHTML .= '<br>';

                foreach ($removedLabels as $label) {
                    $resultHTML .= '<br><input type="checkbox" name="' . $row['IDFILE'] . '" value="' . $label . '"> ' . $label;
                }
                foreach ($goodLabels as $label) {
                    $resultHTML .= '<br><input type="checkbox" name="' . $row['IDFILE'] . '" value="' . $label . '" checked> ' . $label;
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

    public static function buildImageHTML($row, $sourcePath, $ezRefString, $selected = false)
    {
        $src = "";
        $cleanUp = 'false';
        if (strtolower($row['CLEANUP']) == "cleanup")
        {
            $cleanUp = 'true';
        }

        if ($cleanUp == 'true')
        {
            $src = $sourcePath . $ezRefString . "/cleanup/" . $row["IMAGE"];
        }

        elseif
        ($cleanUp == 'false')
        {
            $src = $sourcePath . $ezRefString . "/" . $row["IMAGE"];
        }

        $glowBorderClass = "";
        if ($selected)
        {
            $glowBorderClass = "glowing-border-selected";
        }

        $image_properties = array(
            'src' => $src,
            'alt' => $row["IMAGE"],
            'class' => 'img-responsive ' . $glowBorderClass,
            //'width' => '200',
            //'height' => '200',
            'title' => $row["IMAGE"],
            'rel' => 'lightbox',
        );

        return $image_properties;
    }

    public static function flatten($string)
    {
        $newString = str_replace("/", "^", $string);
        $newString = str_replace(" ", "+", $newString);

        return $newString;
    }

    public static function bibStringToArray($labels, $labelsRemoved)
    {
        $allLabels = array_filter(explode(",", $labels), 'strlen');
        $removedLabels = array_filter(explode(",", $labelsRemoved), 'strlen');
        $goodLabels = array_diff($allLabels, $removedLabels);
        $finalArrayList = array();

        foreach($removedLabels as $label)
        {
            $newObj = array(
                "label" => $label,
                "cleanup" => false
            );

            array_push($finalArrayList, $newObj);
        }

        foreach($goodLabels as $label)
        {
            $newObj = array(
                "label" => $label,
                "cleanup" => true
            );

            array_push($finalArrayList, $newObj);
        }

        return $finalArrayList;

    }

}