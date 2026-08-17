<?php

namespace App\Models;

use App\Enums\Learning\QuestionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningQuestion extends Model
{
    use HasFactory;

    protected $table = 'learning_questions';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'quiz_id',
        'order',
        'type',
        'question',
        'explanation',
        'difficulty',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'type' => QuestionType::class,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $question) {
            if (empty($question->id)) {
                $question->id = (string) uuidv7();
            }
        });
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LearningQuiz::class, 'quiz_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(LearningQuestionOption::class, 'question_id')->orderBy('order');
    }
}
