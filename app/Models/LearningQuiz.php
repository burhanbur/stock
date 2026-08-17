<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningQuiz extends Model
{
    use HasFactory;

    protected $table = 'learning_quizzes';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'lesson_id',
        'title',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $quiz) {
            if (empty($quiz->id)) {
                $quiz->id = (string) uuidv7();
            }
        });
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(LearningLesson::class, 'lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LearningQuestion::class, 'quiz_id')->orderBy('order');
    }
}
