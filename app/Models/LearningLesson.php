<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningLesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'learning_lessons';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'module_id',
        'order',
        'slug',
        'title',
        'estimated_minutes',
        'learning_objectives',
        'key_terms',
        'content',
        'summary',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'estimated_minutes' => 'integer',
            'learning_objectives' => 'array',
            'key_terms' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $lesson) {
            if (empty($lesson->id)) {
                $lesson->id = (string) uuidv7();
            }
        });
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(LearningModule::class, 'module_id');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(LearningQuiz::class, 'lesson_id');
    }
}
