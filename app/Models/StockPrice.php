<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPrice extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'stock_prices';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'stock_id',
        'trading_date',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'trading_date' => 'date',
            'open' => 'decimal:2',
            'high' => 'decimal:2',
            'low' => 'decimal:2',
            'close' => 'decimal:2',
            'volume' => 'integer',
        ];
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
