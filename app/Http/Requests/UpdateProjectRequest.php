<?php

namespace App\Http\Requests;

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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('projects', 'name')->ignore($this->route('id')),
            ],
            'description' => 'nullable|string|max:500',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'integer|distinct|exists:employees,id',
        ];
    }
}
