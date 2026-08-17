<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stocks';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'company_id',
        'ticker',
        'exchange',
        'board',
        'currency',
        'listed_at',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'listed_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $stock) {
            if (empty($stock->id)) {
                $stock->id = (string) uuidv7();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(StockPrice::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Most recent daily price, resolved via a single correlated
     * subquery (Eloquent's "latest of many") instead of loading
     * the full price history.
     */
    public function latestPrice(): HasOne
    {
        return $this->hasOne(StockPrice::class)->latestOfMany('trading_date');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('ticker', 'ilike', "%{$term}%")
                ->orWhereHas('company', function (Builder $query) use ($term) {
                    $query->where('name', 'ilike', "%{$term}%")
                        ->orWhere('short_name', 'ilike', "%{$term}%");
                });
        });
    }

    public function scopeInSector(Builder $query, ?string $sectorId): Builder
    {
        if (blank($sectorId)) {
            return $query;
        }

        return $query->whereHas('company', function (Builder $query) use ($sectorId) {
            $query->where('sector_id', $sectorId);
        });
    }
}
