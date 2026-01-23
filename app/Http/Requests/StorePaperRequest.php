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
            'abstract' => 'required|string|min:100|max:250',
            'keywords' => 'required|string|max:255',
            'topic_area' => 'required|string|max:255',
            'submission_type' => [
                'required',
                Rule::in(['full_paper', 'short_paper', 'poster', 'demo', 'workshop', 'tutorial'])
            ],
            'paper_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'author_comments' => 'nullable|string|max:1000',
            'authors' => 'required|array|min:1',
            'authors.*.user_id' => 'required|exists:users,id',
            'authors.*.is_corresponding' => 'boolean',
            'conference_year' => 'required|string|max:4',
            'is_anonymous' => 'boolean',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['paper_file'] = 'sometimes|file|mimes:pdf,doc,docx|max:10240';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'paper_file.mimes' => 'Paper must be in PDF, DOC, or DOCX format.',
            'paper_file.max' => 'Paper size must not exceed 10MB.',
            'authors.required' => 'At least one author must be specified.',
            'authors.*.user_id.exists' => 'Selected author does not exist in our system.',
        ];
    }

    public function prepareForValidation()
    {
        $authors = $this->input('authors', []);
        $hasCorresponding = collect($authors)->contains('is_corresponding', true);
        
        if (!$hasCorresponding && count($authors) > 0) {
            $authors[0]['is_corresponding'] = true;
            $this->merge(['authors' => $authors]);
        }
    }
}