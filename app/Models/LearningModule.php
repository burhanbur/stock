<?php

namespace App\Models;

use App\Enums\Learning\ModuleLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'learning_modules';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'order',
        'slug',
        'level',
        'title',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'level' => ModuleLevel::class,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $module) {
            if (empty($module->id)) {
                $module->id = (string) uuidv7();
            }
        });
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(LearningLesson::class, 'module_id')->orderBy('order');
    }
}
