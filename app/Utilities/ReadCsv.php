<?php 

namespace App\Utilities;

class ReadCsv {

    public static function parse($file, $delimiter = ',') {
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }

        $header = null;
        $data = array();

        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }
}