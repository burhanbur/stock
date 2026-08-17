<?php

namespace App\Http\Requests\Learning;

use App\Http\Requests\BaseFormRequest;

class SubmitQuizAttemptRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Jawab dulu setidaknya satu pertanyaan.',
            'answers.min' => 'Jawab dulu setidaknya satu pertanyaan.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function answers(): array
    {
        return $this->input('answers', []);
    }
}
