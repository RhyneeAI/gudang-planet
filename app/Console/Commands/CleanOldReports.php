<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanOldReports extends Command
{
    protected $signature = 'reports:clean {--days=7 : Hapus file lebih dari N hari}';
    protected $description = 'Hapus file report lama yang sudah tidak terpakai';

    public function handle()
    {
        $days = (int) $this->option('days');
        $expireDate = now()->subDays($days);
        
        $deletedCount = 0;
        $directories = ['reports/revenue/', 'reports/marketing-commission/'];
        
        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                continue;
            }
            
            $files = Storage::disk('public')->files($directory);
            
            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp(
                    Storage::disk('public')->lastModified($file)
                );
                
                if ($lastModified->lt($expireDate)) {
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                    $this->line("Deleted: {$file}");
                }
            }
        }
        
        $this->info("Deleted {$deletedCount} old report files.");
        return Command::SUCCESS;
    }
}