<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ListApiKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:list-keys
                            {--active : Show only active keys}
                            {--expired : Show only expired keys}
                            {--app= : Filter by application name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all API keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = ApiKey::query()->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->option('active')) {
            $query->where('is_active', true)
                  ->where(function($q) {
                      $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                  });
        }

        if ($this->option('expired')) {
            $query->where(function($q) {
                $q->where('is_active', false)
                  ->orWhere('expires_at', '<=', now());
            });
        }

        if ($app = $this->option('app')) {
            $lower = strtolower($app);
            $query->where('application', 'like', "%{$lower}%");
        }

        $apiKeys = $query->get();

        if ($apiKeys->isEmpty()) {
            $this->info('No API keys found.');
            return 0;
        }

        $this->table(
            ['ID', 'Name', 'Application', 'Masked Key', 'Status', 'Rate Limit', 'Last Used', 'Expires At'],
            $apiKeys->map(function ($key) {
                $status = $key->isValid() ? '<fg=green>Active</>' : '<fg=red>Inactive</>';
                
                return [
                    $key->id,
                    $key->name,
                    $key->application ?? 'N/A',
                    $key->masked_key,
                    $status,
                    $key->rate_limit . '/min',
                    $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Never',
                    $key->expires_at ? $key->expires_at->format('Y-m-d') : 'Never',
                ];
            })
        );

        $this->newLine();
        $this->info('Total: ' . $apiKeys->count() . ' API key(s)');

        return 0;
    }
}
