<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanErrorLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old error logs based on retention policy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $logsPath = storage_path('logs');
        
        $this->info("Cleaning error logs older than {$days} days...");

        if (!File::isDirectory($logsPath)) {
            $this->error('Logs directory not found!');
            return 1;
        }

        $files = File::files($logsPath);
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            // file->getMTime() returns an int timestamp — convert to Carbon
            $fileAge = now()->diffInDays(Carbon::createFromTimestamp($file->getMTime()));
            
            if ($fileAge > $days) {
                $size = $file->getSize();
                $totalSize += $size;
                
                File::delete($file->getPathname());
                $deletedCount++;
                
                $this->line("Deleted: {$file->getFilename()} ({$this->formatBytes($size)})");
            }
        }

        if ($deletedCount > 0) {
            $this->info("\nSuccessfully deleted {$deletedCount} log file(s)");
            $this->info("Total space freed: {$this->formatBytes($totalSize)}");
        } else {
            $this->info("No old log files found to delete.");
        }

        return 0;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
