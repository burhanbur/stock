<?php

namespace Tests\Feature\Learning;

use App\Models\LearningGlossaryTerm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GlossaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_glossary_terms(): void
    {
        LearningGlossaryTerm::factory()->create(['term' => 'ROE', 'full_name' => 'Return on Equity']);
        LearningGlossaryTerm::factory()->create(['term' => 'EPS', 'full_name' => 'Earnings per Share']);

        $this->actingAs(User::factory()->create())
            ->get(route('learning.glossary'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Learning/Glossary')
                ->has('terms', 2)
            );
    }

    public function test_it_filters_by_search_term(): void
    {
        LearningGlossaryTerm::factory()->create(['term' => 'ROE', 'full_name' => 'Return on Equity']);
        LearningGlossaryTerm::factory()->create(['term' => 'EPS', 'full_name' => 'Earnings per Share']);

        $this->actingAs(User::factory()->create())
            ->get(route('learning.glossary', ['search' => 'Equity']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('terms', 1)
                ->where('terms.0.term', 'ROE')
            );
    }
}
