<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanOldReports extends Command
{
    protected $signature = 'reports:clean {--days=7 : Hapus file lebih dari N hari}';
    protected $description = 'Hapus file report lama yang sudah tidak terpakai';

    public function handle(): void
    {
        $days        = (int) $this->option('days');
        $directories = ['reports/marketing-commission', 'reports/revenue'];
        $count       = 0;

        foreach ($directories as $dir) {
            $files = Storage::disk('public')->files($dir);

            foreach ($files as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);

                if (now()->subDays($days)->timestamp > $lastModified) {
                    Storage::disk('public')->delete($file);
                    $count++;
                }
            }
        }

        $this->info("Deleted {$count} old report files.");
        Log::info("reports:clean — deleted {$count} files older than {$days} days.");
    }
}