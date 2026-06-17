<?php

namespace Modules\CaseStudy\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCaseStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'slug'              => ['required', 'string', 'max:255', Rule::unique('case_studies', 'slug')->ignore($this->route('casestudy'))],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'content'           => ['nullable', 'string'],
            'image'             => ['nullable', 'image', 'max:5120'],
            'client'            => ['nullable', 'string', 'max:255'],
            'results'           => ['nullable', 'string', 'max:2000'],
            'status'            => ['required', 'in:draft,published,archived'],
            'published_at'      => ['nullable', 'date'],
        ];
    }
}
