<?php

namespace App\Utilities;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ReadExcel implements ToArray, WithStartRow
{
    protected int $startRow;

    public function __construct(int $startRow = 2)
    {
        $this->startRow = $startRow;
    }

    public function startRow(): int
    {
        return $this->startRow;
    }

    public function array(array $rows): array
    {
        return $rows;
    }
}