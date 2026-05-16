<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOldReports extends Command
{
    protected $signature   = 'reports:clean {--days=3 : Hapus file lebih dari N hari}';
    protected $description = 'Hapus file laporan PDF yang sudah lama';

    public function handle(): void
    {
        $days  = (int) $this->option('days');
        $files = Storage::disk('public')->files('reports');
        $count = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);

            if (now()->subDays($days)->timestamp > $lastModified) {
                Storage::disk('public')->delete($file);
                $count++;
            }
        }

        $this->info("Deleted {$count} old report files.");
        \Illuminate\Support\Facades\Log::info("reports:clean — deleted {$count} files older than {$days} days.");
    }
}