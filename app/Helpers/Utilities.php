<?php

if (!function_exists('isProduction')) {
    function isProduction(): bool
    {
        return app()->environment('production') || app()->environment('live') || app()->environment('prod');
    }
}

if (!function_exists('isStaging')) {
    function isStaging(): bool
    {
        return app()->environment('staging') || app()->environment('stage') || app()->environment('dev') || app()->environment('development');
    }
}

if (!function_exists('camelToSnakeCase')) {
    function camelToSnakeCase($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}

if (!function_exists('snakeToCamelCase')) {
    function snakeToCamelCase($input)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }
}

if (!function_exists('check')) {
    function check($param) {
        echo "<pre>";
        var_dump($param);
        echo '</pre>';
    }
}

if (!function_exists('objectToArray')) {
    function objectToArray($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map('objectToArray', $data);
        }

        return $data;
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($number = null) {
        return number_format((float) $number, 0, ',', '.');
    }
}

if (!function_exists('toRupiah')) {
    function toRupiah($number = null) {
        return "Rp " . formatCurrency($number);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date = null) {

        if ($date) {
            $a = explode('-', $date);
            $date = $a['2'] . " " . formatMonthIndonesian($a['1']) . " " . $a['0'];
        }

        return $date;
    }
}

if (!function_exists('formatMonthIndonesian')) {
    function formatMonthIndonesian($month = null){
        switch ($month) {
            case 1:
                $month = "Januari";
                break;
            case 2:
                $month = "Februari";
                break;
            case 3:
                $month = "Maret";
                break;
            case 4:
                $month = "April";
                break;
            case 5:
                $month = "Mei";
                break;
            case 6:
                $month = "Juni";
                break;
            case 7:
                $month = "Juli";
                break;
            case 8:
                $month = "Agustus";
                break;
            case 9:
                $month = "September";
                break;
            case 10:
                $month = "Oktober";
                break;
            case 11:
                $month = "November";
                break;
            case 12:
                $month = "Desember";
                break;
            default:
                $month = Date('F');
                break;
        }

        return $month;
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime($dateTime = null, $gmt = null) {
        $returnValue = null;
        
        if (!$gmt) {
            $gmt = 'WIB';
        }

        if (!$dateTime) {
            return $returnValue;
        }
        
        $date = date('Y-m-d', strtotime($dateTime));
        $time = date('H:i', strtotime($dateTime));

        return formatDate($date) . ' | ' . $time . ' ' . $gmt;
    }
}

if (!function_exists('formatDayIndonesian')) {
    function formatDayIndonesian($date = null) {
        $day = date('l', strtotime($date));

        switch ($day) {
            case 'Sunday':
                $day = 'Minggu';
                break;
            case 'Monday':
                $day = 'Senin';
                break;
            case 'Tuesday':
                $day = 'Selasa';
                break;
            case 'Wednesday':
                $day = 'Rabu';
                break;
            case 'Thursday':
                $day = 'Kamis';
                break;
            case 'Friday':
                $day = 'Jumat';
                break;
            case 'Saturday':
                $day = 'Sabtu';
                break;
            
            default:
                $day = null;
                break;
        }

        return $day;
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length = null) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < (int) $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('generateRandomUpperCaseAlphabet')) {
    function generateRandomUpperCaseAlphabet($length = null) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('generateRandomLowerCaseAlphabet')) {
    function generateRandomLowerCaseAlphabet($length = null) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('generateRandomNumber')) {
    function generateRandomNumber($length = null) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('cleanString')) {
    function cleanString($value) {
        // Hilangkan semua karakter non-digit (kecuali angka)
        $value = preg_replace('/[^\d]/u', '', $value);

        // Bersihkan karakter tak terlihat seperti U+202C
        $value = preg_replace('/[\x{200B}-\x{200F}\x{202A}-\x{202E}]/u', '', $value);

        return $value;
    }
}

if (!function_exists('uuidv7')) {
    function uuidv7()
    {
        // Unix epoch timestamp in milliseconds (48 bits)
        $unixTimeMs = (int) floor(microtime(true) * 1000);

        // Convert timestamp to 48-bit hex
        $timeHex = str_pad(dechex($unixTimeMs), 12, '0', STR_PAD_LEFT);

        // Random bits
        $randA = random_int(0, 0x0fff);      // 12 bits
        $randB = random_int(0, 0x3fff);      // 14 bits
        $randC = random_int(0, 0xffff);      // 16 bits
        $randD = random_int(0, 0xffffffff);  // 32 bits

        // Apply version: 0x7xxx
        $version = dechex(0x7000 | $randA);

        // Apply variant: 0b10xxxxxx xxxx....
        $variant = dechex(0x8000 | $randB);

        // Format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
        return sprintf(
            '%s-%s-%s-%s-%s%08x',
            substr($timeHex, 0, 8),        // time high
            substr($timeHex, 8, 4),        // time low
            $version,                      // version + randomness
            $variant,                      // variant + randomness
            str_pad(dechex($randC), 4, '0', STR_PAD_LEFT),
            $randD
        );
    }
}

if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        $number = abs($number);
        $words = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
            'sepuluh', 'sebelas'
        ];

        if ($number < 12) {
            return $words[$number];
        } elseif ($number < 20) {
            return numberToWords($number - 10) . ' belas';
        } elseif ($number < 100) {
            return numberToWords(floor($number / 10)) . ' puluh ' . numberToWords($number % 10);
        } elseif ($number < 200) {
            return 'seratus ' . numberToWords($number - 100);
        } elseif ($number < 1000) {
            return numberToWords(floor($number / 100)) . ' ratus ' . numberToWords($number % 100);
        } elseif ($number < 2000) {
            return 'seribu ' . numberToWords($number - 1000);
        } elseif ($number < 1000000) {
            return numberToWords(floor($number / 1000)) . ' ribu ' . numberToWords($number % 1000);
        } elseif ($number < 1000000000) {
            return numberToWords(floor($number / 1000000)) . ' juta ' . numberToWords($number % 1000000);
        } elseif ($number < 1000000000000) {
            return numberToWords(floor($number / 1000000000)) . ' miliar ' . numberToWords($number % 1000000000);
        } elseif ($number < 1000000000000000) {
            return numberToWords(floor($number / 1000000000000)) . ' triliun ' . numberToWords($number % 1000000000000);
        }

        return 'angka terlalu besar';
    }
}

if (!function_exists('buildHierarchy')) {
    function buildHierarchy($units, $parentId = null, $level = 0)
    {
        $result = [];
        
        foreach ($units as $unit) {
            if ($unit->parent_id == $parentId) {
                $unit->level = $level;
                $result[] = $unit;
                
                // Rekursif untuk child
                $children = buildHierarchy($units, $unit->id, $level + 1);
                $result = array_merge($result, $children);
            }
        }
        
        return $result;
    }
}
