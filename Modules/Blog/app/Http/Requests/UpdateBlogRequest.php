<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $blogId = $this->route('blog')->id;

        return [
            'title'            => 'required|string|max:255',
            'slug'             => "required|string|max:255|unique:blogs,slug,{$blogId}",
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'banner'           => 'nullable|image|max:5120',
            'category_id'      => 'nullable|exists:categories,id',
            'status'           => 'required|in:draft,published,scheduled',
            'published_at'     => 'nullable|date',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string|max:50',
        ];
    }
}
