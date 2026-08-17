export type ModuleLevel = 'beginner' | 'intermediate' | 'advanced' | 'quant';

export interface LearningModuleSummary {
    id: string;
    slug: string;
    order: number;
    level: ModuleLevel;
    level_label: string;
    title: string;
    description: string | null;
    total_lessons: number;
    completed_lessons: number;
    percent: number;
    is_locked: boolean;
}

export interface ContinueLesson {
    module_slug: string;
    lesson_slug: string;
    lesson_title: string;
}

export interface LearningDashboard {
    modules: LearningModuleSummary[];
    overall_percent: number;
    total_lessons: number;
    completed_lessons: number;
    quiz_average: number | null;
    continue_lesson: ContinueLesson | null;
}

export interface LearningModuleDetail {
    slug: string;
    order: number;
    level: ModuleLevel;
    level_label: string;
    title: string;
    description: string | null;
}

export interface LearningLessonSummary {
    slug: string;
    order: number;
    title: string;
    estimated_minutes: number;
    is_completed: boolean;
    is_locked: boolean;
}

export interface QuizOption {
    id: string;
    text: string;
}

export interface QuizQuestion {
    id: string;
    type: 'multiple_choice' | 'true_false';
    question: string;
    options: QuizOption[];
}

export interface Quiz {
    id: string;
    title: string;
    questions: QuizQuestion[];
}

export interface QuizResultItem {
    question_id: string;
    selected_option_id: string | null;
    correct_option_id: string;
    is_correct: boolean;
    explanation: string | null;
}

export interface QuizAttemptResult {
    score: number;
    correct_answers: number;
    total_questions: number;
    attempted_at: string;
    results: QuizResultItem[];
}

export interface LearningLessonDetail {
    slug: string;
    order: number;
    title: string;
    estimated_minutes: number;
    learning_objectives: string[];
    key_terms: string[];
    content: string;
    summary: string | null;
}

export interface AdjacentLesson {
    slug: string;
    title: string;
}

export interface GlossaryTerm {
    slug: string;
    term: string;
    full_name: string | null;
    simple_definition: string;
    formal_definition: string | null;
    example: string | null;
    application_usage: string | null;
    related_term_slugs: string[];
}
