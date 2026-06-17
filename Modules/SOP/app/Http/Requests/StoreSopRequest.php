<?php

namespace Modules\SOP\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'file'        => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'is_public'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please attach a PDF file.',
            'file.mimes'    => 'The file must be a PDF.',
            'file.max'      => 'The PDF may not be larger than 20 MB.',
        ];
    }
}
