<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255|unique:projects,name',
            'description' => 'nullable|string|max:500',
            'employee_ids' => 'array',
            'employee_ids.*' => 'integer|distinct|exists:employees,id',
        ];
    }
}
