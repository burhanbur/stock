<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ErrorAnalytics
{
    /**
     * Get error statistics from logs
     */
    public function getErrorStats(int $days = 7): array
    {
        $logsPath = storage_path('logs');
        $stats = [
            'total_errors' => 0,
            'by_code' => [],
            'by_day' => [],
            'top_urls' => [],
            'recent_errors' => [],
        ];

        if (!File::isDirectory($logsPath)) {
            return $stats;
        }

        $files = $this->getRecentLogFiles($logsPath, $days);
        
        foreach ($files as $file) {
            $this->parseLogFile($file, $stats);
        }

        // Sort by frequency
        arsort($stats['by_code']);
        arsort($stats['top_urls']);

        return $stats;
    }

    /**
     * Get recent log files
     */
    protected function getRecentLogFiles(string $path, int $days): array
    {
        $files = File::files($path);
        $cutoff = now()->subDays($days);
        
        return array_filter($files, function ($file) use ($cutoff) {
            return Carbon::createFromTimestamp($file->getMTime())->isAfter($cutoff);
        });
    }

    /**
     * Parse log file and extract error information
     */
    protected function parseLogFile($file, array &$stats): void
    {
        $content = File::get($file->getPathname());
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            // Parse error dari log
            if (preg_match('/HTTP (\d{3}) Error/', $line, $matches)) {
                $code = $matches[1];
                $stats['total_errors']++;
                
                if (!isset($stats['by_code'][$code])) {
                    $stats['by_code'][$code] = 0;
                }
                $stats['by_code'][$code]++;

                // Extract URL jika ada
                if (preg_match('/"url":"([^"]+)"/', $line, $urlMatches)) {
                    $url = $urlMatches[1];
                    if (!isset($stats['top_urls'][$url])) {
                        $stats['top_urls'][$url] = 0;
                    }
                    $stats['top_urls'][$url]++;
                }

                // Store recent errors (last 10)
                if (count($stats['recent_errors']) < 10) {
                    $stats['recent_errors'][] = [
                        'code' => $code,
                        'line' => substr($line, 0, 200),
                        'timestamp' => now()->toDateTimeString(),
                    ];
                }
            }
        }
    }

    /**
     * Get error rate per hour
     */
    public function getErrorRate(int $hours = 24): array
    {
        $stats = $this->getErrorStats(1);
        $totalErrors = $stats['total_errors'];
        
        return [
            'total' => $totalErrors,
            'per_hour' => round($totalErrors / $hours, 2),
            'per_minute' => round($totalErrors / ($hours * 60), 2),
        ];
    }

    /**
     * Get most common errors
     */
    public function getMostCommonErrors(int $limit = 5): array
    {
        $stats = $this->getErrorStats(7);
        
        return array_slice($stats['by_code'], 0, $limit, true);
    }

    /**
     * Get error trends
     */
    public function getErrorTrends(int $days = 7): array
    {
        $trends = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $logFile = storage_path("logs/laravel-{$date}.log");
            
            if (File::exists($logFile)) {
                $content = File::get($logFile);
                $errorCount = substr_count($content, 'HTTP');
                
                $trends[$date] = $errorCount;
            } else {
                $trends[$date] = 0;
            }
        }

        return array_reverse($trends);
    }

    /**
     * Check if error rate is abnormal
     */
    public function isAbnormalErrorRate(float $threshold = 100): bool
    {
        $rate = $this->getErrorRate(1);
        
        return $rate['per_hour'] > $threshold;
    }

    /**
     * Get error summary for dashboard
     */
    public function getDashboardSummary(): array
    {
        $stats = $this->getErrorStats(7);
        $rate = $this->getErrorRate(24);
        $trends = $this->getErrorTrends(7);

        return [
            'summary' => [
                'total_errors_7days' => $stats['total_errors'],
                'error_rate_24h' => $rate,
                'most_common' => $this->getMostCommonErrors(3),
                'is_abnormal' => $this->isAbnormalErrorRate(),
            ],
            'details' => [
                'by_code' => $stats['by_code'],
                'top_urls' => array_slice($stats['top_urls'], 0, 10, true),
                'recent' => $stats['recent_errors'],
            ],
            'trends' => $trends,
        ];
    }

    /**
     * Export error report
     */
    public function exportReport(int $days = 30): string
    {
        $stats = $this->getErrorStats($days);
        $trends = $this->getErrorTrends($days);

        $report = "ERROR REPORT - Generated at " . now()->toDateTimeString() . "\n";
        $report .= str_repeat("=", 80) . "\n\n";

        $report .= "SUMMARY\n";
        $report .= "-------\n";
        $report .= "Period: Last {$days} days\n";
        $report .= "Total Errors: {$stats['total_errors']}\n\n";

        $report .= "ERRORS BY CODE\n";
        $report .= "--------------\n";
        foreach ($stats['by_code'] as $code => $count) {
            $report .= sprintf("HTTP %s: %d errors\n", $code, $count);
        }
        $report .= "\n";

        $report .= "TOP ERROR URLs\n";
        $report .= "--------------\n";
        $topUrls = array_slice($stats['top_urls'], 0, 10, true);
        foreach ($topUrls as $url => $count) {
            $report .= sprintf("%d errors - %s\n", $count, $url);
        }
        $report .= "\n";

        $report .= "DAILY TRENDS\n";
        $report .= "------------\n";
        foreach ($trends as $date => $count) {
            $report .= sprintf("%s: %d errors\n", $date, $count);
        }

        return $report;
    }

    /**
     * Save report to file
     */
    public function saveReport(int $days = 30): string
    {
        $report = $this->exportReport($days);
        $filename = 'error-report-' . now()->format('Y-m-d-His') . '.txt';
        $path = storage_path('reports/' . $filename);

        if (!File::isDirectory(storage_path('reports'))) {
            File::makeDirectory(storage_path('reports'), 0755, true);
        }

        File::put($path, $report);

        return $path;
    }
}
