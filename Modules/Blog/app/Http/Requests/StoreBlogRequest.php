<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255|unique:blogs,slug',
            'category_id'  => 'nullable|exists:categories,id',
            'status'       => 'nullable|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
        ];
    }
}
