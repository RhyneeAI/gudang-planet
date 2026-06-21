<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsReportExport implements FromArray, WithHeadings
{
    public function __construct(
        protected array $headings,
        protected Collection $rows,
    ) {}

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->rows->values()->all();
    }
}
