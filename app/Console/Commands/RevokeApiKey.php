<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RevokeApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:revoke-key
                            {id : The ID of the API key to revoke}
                            {--permanent : Permanently delete the key instead of deactivating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke or delete an API key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $permanent = $this->option('permanent');

        $exitCode = 0;
        $action = null;

        $apiKey = ApiKey::find($id);

        if (!$apiKey) {
            $this->error("API key with ID {$id} not found.");
            $exitCode = 1;
        } else {
            // Show API key details
            $this->info('API Key Details:');
            $this->table(
                ['Property', 'Value'],
                [
                    ['ID', $apiKey->id],
                    ['Name', $apiKey->name],
                    ['Application', $apiKey->application ?? 'N/A'],
                    ['Masked Key', $apiKey->masked_key],
                    ['Status', $apiKey->is_active ? 'Active' : 'Inactive'],
                    ['Created', $apiKey->created_at->format('Y-m-d H:i:s')],
                    ['Last Used', $apiKey->last_used_at ? $apiKey->last_used_at->format('Y-m-d H:i:s') : 'Never'],
                ]
            );

            // Confirm action and perform it if confirmed
            if ($permanent) {
                if ($this->confirm('Are you sure you want to PERMANENTLY DELETE this API key?')) {
                    $apiKey->delete();
                    $action = 'deleted';
                } else {
                    $this->info('Operation cancelled.');
                }
            } else {
                if ($this->confirm('Are you sure you want to DEACTIVATE this API key?')) {
                    $apiKey->update([
                        'is_active' => false,
                        'updated_by' => auth()->id(),
                    ]);
                    $action = 'deactivated';
                } else {
                    $this->info('Operation cancelled.');
                }
            }

            // If an action was taken, clear caches and show message
            if (!is_null($action)) {
                Cache::forget('api_key_' . md5($apiKey->key));
                Cache::forget('api_rate_limit_' . $apiKey->id);
                $this->info("✅ API key successfully {$action}!");
            }
        }

        return $exitCode;
    }
}
