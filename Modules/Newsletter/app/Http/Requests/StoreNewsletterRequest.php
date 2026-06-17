<?php

namespace Modules\Newsletter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'status'            => ['required', 'in:draft,published,archived'],
            'published_at'      => ['nullable', 'date'],
            'image'             => ['nullable', 'image', 'max:5120'],
        ];
    }
}
