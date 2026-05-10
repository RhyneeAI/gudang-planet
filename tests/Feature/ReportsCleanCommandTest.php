<?php

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

beforeEach(function () {
    Storage::fake('public');
});

it('can delete old report files', function () {
    // Create old file
    $oldFile = 'reports/revenue/old-report.pdf';
    Storage::disk('public')->put($oldFile, 'dummy content');
    
    // Manually set last modified time
    $oldFilePath = Storage::disk('public')->path($oldFile);
    touch($oldFilePath, now()->subDays(5)->timestamp);
    
    // Create new file
    $newFile = 'reports/revenue/new-report.pdf';
    Storage::disk('public')->put($newFile, 'dummy content');
    $newFilePath = Storage::disk('public')->path($newFile);
    touch($newFilePath, now()->timestamp);
    
    // Run command
    $this->artisan('reports:clean --days=3')
        ->assertExitCode(0);
    
    // Check files manually
    expect(Storage::disk('public')->exists($oldFile))->toBeFalse();
    expect(Storage::disk('public')->exists($newFile))->toBeTrue();
});

it('does not delete files newer than specified days', function () {
    $recentFile = 'reports/revenue/recent-report.pdf';
    Storage::disk('public')->put($recentFile, 'dummy content');
    
    // Set file to 2 days old
    $filePath = Storage::disk('public')->path($recentFile);
    touch($filePath, now()->subDays(2)->timestamp);
    
    $this->artisan('reports:clean --days=3')
        ->assertExitCode(0);
    
    expect(Storage::disk('public')->exists($recentFile))->toBeTrue();
});

it('handles empty directories gracefully', function () {
    $this->artisan('reports:clean --days=3')
        ->assertExitCode(0);
});

it('deletes files from both revenue and marketing-commission directories', function () {
    // Create old files in both directories
    $revenueFile = 'reports/revenue/old-revenue.pdf';
    Storage::disk('public')->put($revenueFile, 'dummy');
    $revenuePath = Storage::disk('public')->path($revenueFile);
    touch($revenuePath, now()->subDays(10)->timestamp);
    
    $commissionFile = 'reports/marketing-commission/old-commission.pdf';
    Storage::disk('public')->put($commissionFile, 'dummy');
    $commissionPath = Storage::disk('public')->path($commissionFile);
    touch($commissionPath, now()->subDays(10)->timestamp);
    
    $this->artisan('reports:clean --days=7')
        ->assertExitCode(0);
    
    expect(Storage::disk('public')->exists($revenueFile))->toBeFalse();
    expect(Storage::disk('public')->exists($commissionFile))->toBeFalse();
});