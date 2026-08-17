# Learning Center — Architecture Notes

An interactive Stock Learning Center inside the app, teaching stock market
concepts from absolute zero toward eventually understanding how the app's
own (not-yet-built) recommendation engine works. Same stack as the Stock
module: Laravel + Inertia + React + TypeScript, thin controllers, Actions,
explicit Resources/props.

## What exists today

Full learning infrastructure (modules → lessons → quizzes → questions →
options, per-user progress tracking, quiz attempts, a searchable glossary),
seeded with **one real module**: "Dasar-Dasar Saham" (Module 01 — Stock
Market Fundamentals), 8 lessons, a 3-question quiz per lesson, and a
19-term glossary covering everything referenced by those lessons. The other
12 modules described in the original curriculum brief are deliberately not
built yet — see "Scope" below.

- **Read path**: `Http/Controllers/Learning/*` (thin) → `Actions/Learning/*`
  (`GetLearningDashboardAction`, `GetModuleDetailAction`, `GetLessonDetailAction`,
  `SearchGlossaryTermsAction` — framework-agnostic Eloquent queries) → plain
  arrays shaped directly in the controller → Inertia → React pages under
  `resources/js/Pages/Learning`. No `Http/Resources/Learning/*` for the
  composite dashboard/module/lesson views (their shape mixes several models'
  data plus computed fields — a `LearningGlossaryTermResource` exists for the
  one place a resource maps 1:1 onto a single model).
- **Content lives in the database, not in React.** `learning_lessons.content`
  is Markdown, rendered client-side via `react-markdown` +
  `remark-gfm` (`Components/Learning/LessonContent.tsx`). This was chosen
  over server-side rendering (e.g. `league/commonmark`) to keep the
  Resource/props layer a plain string and avoid adding a PHP Markdown
  dependency for one feature.
- **Progress & locking**: implicit and linear (see the migration note on
  `learning_lessons` and `App\Support\Learning\ModuleLock`) — lesson *N*
  unlocks once lesson *N-1* in the same module is completed; module *N*
  unlocks once every lesson in module *N-1* is completed. Viewing a lesson
  (`GET`) records `learning_progress` as `in_progress`
  (`RecordLessonViewAction`); completing it — either via the "Tandai
  Selesai" button for lessons with no quiz, or automatically on quiz
  submission regardless of score — marks it `completed`
  (`MarkLessonCompleteAction`). A wrong quiz answer still completes the
  lesson; the point is exposure to the material and the ability to retry,
  not gatekeeping.
- **Quiz grading is a pure function**: `App\Support\Learning\QuizGrader::grade()`
  takes plain `{id, correct_option_id}` question shapes and an
  `{question_id: option_id}` answer map, returns score + per-question
  correctness — no Eloquent, no DB, unit-tested the same way as
  `PriceChangeCalculator` in the Stock module. `SubmitQuizAttemptAction` is
  the thin framework layer around it (loads the quiz, maps models to plain
  arrays, grades, persists the attempt, marks the lesson complete).
- **Correct answers are never sent to the client before grading.** The quiz
  shape returned by `LearningLessonController::quizForDisplay()` only
  includes option `id`/`text` — no `is_correct`, no `explanation`. Those are
  only included in `latest_attempt`, built *after* an attempt exists, by
  recomputing `QuizGrader::grade()` against the stored `answers` and joining
  in each question's `explanation`. Covered by
  `LessonShowTest::test_it_shows_lesson_content_and_quiz_without_leaking_correct_answers`.

## Scope: what's deliberately not built

Per the original brief's own "Initial Implementation Scope" — build the
infrastructure once, prove it end-to-end with one real module, and let
content grow later without re-architecting:

- Modules 02–13 (market mechanics, financial statements, valuation,
  technical/risk/portfolio analysis, backtesting, ML, and finally "how our
  recommendation engine works") — the schema and locking logic already
  support adding them as more `LearningModule`/`LearningLesson`/`LearningQuiz`
  rows via the same seeder pattern (`database/seeders/Learning*Seeder.php`).
  No code changes needed to add Module 02 — just new seeder content.
- A `learning_courses` table wrapping modules — the brief's schema lists
  one, but with a single linear curriculum it added no real behavior yet
  (nothing to distinguish one "course" from another). `ModuleLevel` (an enum
  on `learning_modules`: beginner/intermediate/advanced/quant) covers the
  categorization the brief wanted from a course/level split. Add a real
  `learning_courses` table if/when there's ever more than one distinct
  curriculum track.
- A general prerequisite graph (lesson X requires lessons A *and* B from
  different modules) — the curriculum is linear for now; `ModuleLock`
  encodes "previous module/lesson must be complete," not an arbitrary DAG.
- Deep-linking a lesson's "why this matters" section back to the actual
  Stock Recommendation Engine's live score breakdown (the brief's §26 "Why
  did the system rank BBCA #1?" vision) — the recommendation/scoring engine
  itself doesn't exist yet (see `ai/stock-module.md`'s "not built yet" list).
  Each lesson does link forward to real data on the [Stocks](/stocks) pages
  where relevant, and glossary terms carry an `application_usage` field
  pointing at the current (Module 1-relevant) parts of the system.
- Gamification beyond the dashboard's progress bar / quiz average — no
  streaks, badges, or points, per the brief's own "keep it lightweight"
  instruction (§19).

## Content authoring convention

New lessons/quizzes are added via seeders (`database/seeders/Learning*Seeder.php`),
each lesson's Markdown as a PHP heredoc string, not hand-written migrations
or hardcoded in React — see §15/§16 of the original brief. Language is
Bahasa Indonesia (matching the rest of the app's user-facing text), formal
English financial terms introduced in parentheses on first use, e.g.
"Kepemilikan (Ownership)". Every lesson ends with a **"Kenapa Ini Penting
untuk Sistem Kita?"** section connecting the concept back to a real page in
the app, and every lesson has at least one worked numerical example instead
of a bare definition, per the brief's explicit content-quality rules (§17,
§23).
