<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sources' => 'sometimes|array',
            'sources.*' => 'string|exists:sources,slug',
            'categories' => 'sometimes|array',
            'categories.*' => 'string|exists:categories,slug',
            'authors' => 'sometimes|array',
            'authors.*' => 'string|exists:authors,slug',
        ];
    }
}
