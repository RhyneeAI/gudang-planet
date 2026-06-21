<?php

namespace App\Jobs;

use App\Helpers\FileHelper;
use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateReportExport implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public Company $company,
        public string $reportType,
        public array $filters,
        public string $format,
        public ?int $requestedByUserId = null,
    ) {
        //
    }

    public function handle(): void
    {
        $filename = 'laporan-' . $this->reportType . '-' . now()->format('YmdHis') . '.' . $this->format;

        $storagePath = 'reports/' . $this->reportType . '/' . $filename;

        // TODO: Implement report generation logic per $this->reportType
        // This job will be fully implemented when async exports are enabled
        // For now, the synchronous export in controllers still handles generation

        Log::info('GenerateReportExport dispatched', [
            'report_type' => $this->reportType,
            'company_id' => $this->company->id,
            'format' => $this->format,
            'filename' => $filename,
        ]);
    }
}
