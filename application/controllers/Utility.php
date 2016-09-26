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

    public static function buildImageHTML($row, $sourcePath, $ezRefString)
    {
        $src = "";
        $cleanUp = 'false';
        if (strtolower($row['CLEANUP']) == "cleanup")
        {
            $cleanUp = 'true';
        }

        if ($cleanUp == 'true')
        {
            $src = $sourcePath . $ezRefString . "/cleanup/" . self::flatten($row["IMAGE"]);
        }

        elseif
        ($cleanUp == 'false')
        {
            $src = $sourcePath . $ezRefString . "/" . self::flatten($row["IMAGE"]);
        }

        $image_properties = array(
            'src' => $src,
            'alt' => $row["IMAGE"],
            'class' => 'img-responsive',
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

}