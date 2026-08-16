<?php

namespace App\Console\Commands;

use App\Services\ErrorAnalytics;
use Illuminate\Console\Command;

class GenerateErrorReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'errors:report 
                            {--days=30 : Number of days to analyze}
                            {--export : Export report to file}
                            {--json : Output as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate error analytics report from application logs';

    /**
     * Execute the console command.
     */
    public function handle(ErrorAnalytics $analytics)
    {
        $days = $this->option('days');
        $export = $this->option('export');
        $json = $this->option('json');

        $this->info("Generating error report for the last {$days} days...\n");

        if ($export) {
            $path = $analytics->saveReport($days);
            $this->info("✓ Report saved to: {$path}");
            return 0;
        }

        if ($json) {
            $stats = $analytics->getErrorStats($days);
            $this->line(json_encode($stats, JSON_PRETTY_PRINT));
            return 0;
        }

        // Display summary in console
        $this->displaySummary($analytics, $days);

        return 0;
    }

    /**
     * Display error summary in console
     */
    protected function displaySummary(ErrorAnalytics $analytics, int $days): void
    {
        $summary = $analytics->getDashboardSummary();

        // Header
        $this->info("═══════════════════════════════════════════════════");
        $this->info("           ERROR ANALYTICS REPORT");
        $this->info("═══════════════════════════════════════════════════\n");

        // Summary Stats
        $this->line("<fg=cyan>SUMMARY (Last {$days} days)</>");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Errors', $summary['summary']['total_errors_7days']],
                ['Errors/Hour (24h)', $summary['summary']['error_rate_24h']['per_hour']],
                ['Errors/Minute (24h)', $summary['summary']['error_rate_24h']['per_minute']],
                ['Status', $summary['summary']['is_abnormal'] ? '⚠️  ABNORMAL' : '✓ Normal'],
            ]
        );

        // Most Common Errors
        if (!empty($summary['summary']['most_common'])) {
            $this->newLine();
            $this->line("<fg=cyan>MOST COMMON ERRORS</>");
            
            $commonErrors = [];
            foreach ($summary['summary']['most_common'] as $code => $count) {
                $commonErrors[] = ["HTTP {$code}", $count . ' errors'];
            }
            
            $this->table(['Error Code', 'Count'], $commonErrors);
        }

        // Errors by Code
        if (!empty($summary['details']['by_code'])) {
            $this->newLine();
            $this->line("<fg=cyan>ERRORS BY HTTP CODE</>");
            
            $byCode = [];
            foreach ($summary['details']['by_code'] as $code => $count) {
                $emoji = $this->getErrorEmoji($code);
                $byCode[] = [$emoji . " HTTP {$code}", $count];
            }
            
            $this->table(['Error Code', 'Total'], array_slice($byCode, 0, 10));
        }

        // Top Error URLs
        if (!empty($summary['details']['top_urls'])) {
            $this->newLine();
            $this->line("<fg=cyan>TOP ERROR URLs</>");
            
            $topUrls = [];
            foreach (array_slice($summary['details']['top_urls'], 0, 10, true) as $url => $count) {
                $topUrls[] = [
                    \Illuminate\Support\Str::limit($url, 60),
                    $count
                ];
            }
            
            $this->table(['URL', 'Count'], $topUrls);
        }

        // Trends
        if (!empty($summary['trends'])) {
            $this->newLine();
            $this->line("<fg=cyan>DAILY TRENDS (Last 7 days)</>");
            
            $trends = [];
            foreach ($summary['trends'] as $date => $count) {
                $bar = str_repeat('█', min(50, $count / 2));
                $trends[] = [$date, $count, $bar];
            }
            
            $this->table(['Date', 'Errors', 'Graph'], $trends);
        }

        // Recent Errors
        if (!empty($summary['details']['recent'])) {
            $this->newLine();
            $this->line("<fg=cyan>RECENT ERRORS</>");
            
            foreach (array_slice($summary['details']['recent'], 0, 5) as $error) {
                $this->line("  <fg=red>HTTP {$error['code']}</> - " . substr($error['line'], 0, 100));
            }
        }

        $this->newLine();
        $this->info("═══════════════════════════════════════════════════");
        $this->line("Use --export to save report to file");
        $this->line("Use --json for JSON output");
    }

    /**
     * Get emoji for error code
     */
    protected function getErrorEmoji(int $code): string
    {
        return match (true) {
            $code >= 500 => '🔴',
            $code >= 400 => '🟡',
            default => '🟢',
        };
    }
}
