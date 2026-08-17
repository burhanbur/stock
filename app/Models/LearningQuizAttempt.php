<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningQuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'learning_quiz_attempts';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'total_questions',
        'correct_answers',
        'score',
        'answers',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'total_questions' => 'integer',
            'correct_answers' => 'integer',
            'score' => 'decimal:2',
            'answers' => 'array',
            'attempted_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $attempt) {
            if (empty($attempt->id)) {
                $attempt->id = (string) uuidv7();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LearningQuiz::class, 'quiz_id');
    }
}
