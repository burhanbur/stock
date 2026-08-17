<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningGlossaryTerm extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'learning_glossary_terms';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'slug',
        'term',
        'full_name',
        'simple_definition',
        'formal_definition',
        'example',
        'application_usage',
        'related_term_slugs',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'related_term_slugs' => 'array',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $term) {
            if (empty($term->id)) {
                $term->id = (string) uuidv7();
            }
        });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term) {
            $query->where('term', 'ilike', "%{$term}%")
                ->orWhere('full_name', 'ilike', "%{$term}%");
        });
    }
}
