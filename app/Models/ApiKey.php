<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'api_keys';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'key',
        'description',
        'application',
        'ip_whitelist',
        'permissions',
        'is_active',
        'rate_limit',
        'last_used_at',
        'expires_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ip_whitelist' => 'array',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'rate_limit' => 'integer',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'key', // Hide the actual key in responses
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($apiKey) {
            if (empty($apiKey->id)) {
                $apiKey->id = (string) uuidv7();
            }
        });
    }

    /**
     * Generate a new API key
     */
    public static function generate(): string
    {
        return Str::slug(config('app.alias', 'app'), '_') . '_' . Str::random(48);
    }

    /**
     * Check if the API key is valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if IP is whitelisted
     */
    public function isIpAllowed(string $ip): bool
    {
        if (empty($this->ip_whitelist)) {
            return true; // No whitelist means all IPs allowed
        }

        return in_array($ip, $this->ip_whitelist);
    }

    /**
     * Check if permission is allowed
     */
    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return true; // No permissions means all allowed
        }

        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    /**
     * Update last used timestamp
     */
    public function recordUsage(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Creator relationship
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater relationship
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get masked key for display
     */
    public function getMaskedKeyAttribute(): string
    {
        if (empty($this->key)) {
            return '';
        }

        $prefix = substr($this->key, 0, 10);
        $suffix = substr($this->key, -4);
        
        return $prefix . '...' . $suffix;
    }
}
