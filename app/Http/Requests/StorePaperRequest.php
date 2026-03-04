<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaperRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:500',
            'abstract' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $wordCount = str_word_count($value);
                    if ($wordCount > 250) {
                        $fail('The abstract must not exceed 250 words. Current count: ' . $wordCount . ' words.');
                    }
                    if ($wordCount < 50) {
                        $fail('The abstract must be at least 50 words. Current count: ' . $wordCount . ' words.');
                    }
                }
            ],
            'keywords' => 'required|string|max:255',
            'topic_area' => 'required|string|max:255',
            'submission_type' => [
                'required',
                Rule::in(['abstract_only', 'full_paper'])
            ],
            'conference_year' => 'required|string|max:4',
            'author_comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'boolean',
            'authors' => 'required|array|min:1',
            'authors.*.user_id' => 'required|exists:users,id',
            'authors.*.is_corresponding' => 'boolean',
            'registration_ids' => 'nullable|array',
            'registration_ids.*' => 'exists:conference_registrations,id',
        ];

        // Conditional file validation based on submission type
        if ($this->input('submission_type') === 'full_paper') {
            if ($this->isMethod('POST')) {
                $rules['paper_file'] = 'required|file|mimes:pdf|max:10240';
            } else {
                $rules['paper_file'] = 'nullable|file|mimes:pdf|max:10240';
            }
        } else {
            $rules['paper_file'] = 'nullable|file|mimes:pdf|max:10240';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'paper_file.required' => 'A PDF file is required for full paper submissions.',
            'paper_file.mimes' => 'Paper must be in PDF format.',
            'paper_file.max' => 'Paper size must not exceed 10MB.',
            'authors.required' => 'At least one author must be specified.',
            'authors.*.user_id.exists' => 'Selected author does not exist in our system.',
            'submission_type.in' => 'Submission type must be either abstract_only or full_paper.',
        ];
    }

    public function prepareForValidation()
    {
        // Handle corresponding author logic
        $authors = $this->input('authors', []);
        $hasCorresponding = collect($authors)->contains('is_corresponding', true);
        
        if (!$hasCorresponding && count($authors) > 0) {
            $authors[0]['is_corresponding'] = true;
            $this->merge(['authors' => $authors]);
        }
        
        // Ensure is_anonymous has a default value
        if (!$this->has('is_anonymous')) {
            $this->merge(['is_anonymous' => true]);
        }
    }
}