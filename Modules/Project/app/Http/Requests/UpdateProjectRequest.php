<?php

namespace Modules\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($this->route('project'))],
            'short_description' => ['nullable', 'string', 'max:500'],
            'banner'            => ['nullable', 'image', 'max:5120'],
            'description'       => ['nullable', 'string'],
            'specifications'    => ['nullable', 'string'],
            'statistics'        => ['nullable', 'string'],
            'before_points'     => ['nullable', 'string'],
            'after_points'      => ['nullable', 'string'],
            'onboard_accounts'  => ['nullable', 'string'],
            'gallery'           => ['nullable', 'string'],
            'charts'            => ['nullable', 'string'],
        ];
    }
}
