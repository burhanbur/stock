<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningQuestionOption extends Model
{
    use HasFactory;

    protected $table = 'learning_question_options';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'question_id',
        'order',
        'text',
        'is_correct',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_correct' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $option) {
            if (empty($option->id)) {
                $option->id = (string) uuidv7();
            }
        });
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(LearningQuestion::class, 'question_id');
    }
}
