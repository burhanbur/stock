<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-key
                            {name : The friendly name for this API key}
                            {--app= : Application name using this key}
                            {--desc= : Description of the API key}
                            {--ips=* : Allowed IP addresses (comma-separated or multiple --ips)}
                            {--permissions=* : Allowed permissions (comma-separated or multiple --permissions)}
                            {--rate-limit=60 : Rate limit per minute}
                            {--expires= : Expiration date (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new API key for external application access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $application = $this->option('app');
        $description = $this->option('desc');
        $ips = $this->option('ips');
        $permissions = $this->option('permissions');
        $rateLimit = $this->option('rate-limit');
        $expires = $this->option('expires');

        // Parse IPs (handle both comma-separated and multiple options)
        $ipWhitelist = null;
        if (!empty($ips)) {
            $ipWhitelist = [];
            foreach ($ips as $ipString) {
                $ipArray = array_map('trim', explode(',', $ipString));
                $ipWhitelist = array_merge($ipWhitelist, $ipArray);
            }
            $ipWhitelist = array_filter($ipWhitelist);
            $ipWhitelist = empty($ipWhitelist) ? null : $ipWhitelist;
        }

        // Parse permissions (handle both comma-separated and multiple options)
        $permissionsList = null;
        if (!empty($permissions)) {
            $permissionsList = [];
            foreach ($permissions as $permString) {
                $permArray = array_map('trim', explode(',', $permString));
                $permissionsList = array_merge($permissionsList, $permArray);
            }
            $permissionsList = array_filter($permissionsList);
            $permissionsList = empty($permissionsList) ? null : $permissionsList;
        }

        // Parse expiration date
        $expiresAt = null;
        if ($expires) {
            try {
                $expiresAt = \Carbon\Carbon::createFromFormat('Y-m-d', $expires)->endOfDay();
            } catch (\Exception $e) {
                $this->error('Invalid expiration date format. Use Y-m-d (e.g., 2026-12-31)');
                return 1;
            }
        }

        // Generate API key
        $key = ApiKey::generate();

        // Create API key record
        $apiKey = ApiKey::create([
            'name' => $name,
            'key' => $key,
            'description' => $description,
            'application' => $application,
            'ip_whitelist' => $ipWhitelist,
            'permissions' => $permissionsList,
            'is_active' => true,
            'rate_limit' => $rateLimit,
            'expires_at' => $expiresAt,
            'created_by' => auth()->id(),
        ]);

        // Display success message with details
        $this->info('✅ API Key generated successfully!');
        $this->newLine();
        
        $this->table(
            ['Property', 'Value'],
            [
                ['ID', $apiKey->id],
                ['Name', $apiKey->name],
                ['Application', $apiKey->application ?? 'N/A'],
                ['API Key', $key],
                ['Rate Limit', $apiKey->rate_limit . ' requests/minute'],
                ['IP Whitelist', $ipWhitelist ? implode(', ', $ipWhitelist) : 'All IPs allowed'],
                ['Permissions', $permissionsList ? implode(', ', $permissionsList) : 'All permissions'],
                ['Expires At', $expiresAt ? $expiresAt->format('Y-m-d H:i:s') : 'Never'],
                ['Created At', $apiKey->created_at->format('Y-m-d H:i:s')],
            ]
        );

        $this->newLine();
        $this->warn('⚠️  IMPORTANT: Save this API key securely. It will not be shown again!');
        $this->info('Usage: Include this header in your requests:');
        $this->line('X-API-KEY: ' . $key);

        return 0;
    }
}
