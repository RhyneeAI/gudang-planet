<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class OpsIncomeExpenseExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        protected array $headings,
        protected Collection $rows,
        protected string $sheetTitle = 'Laporan',
    ) {}

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->rows->values()->all();
    }
}
