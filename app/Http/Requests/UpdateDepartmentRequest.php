<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateDepartmentRequest
 *
 * Handles validation for updating existing department records.
 * Ensures unique department names excluding current record.
 *
 * @package App\Http\Requests
 */
class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Authorization check delegated to middleware
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Validates:
     * - name: Required unique string, 2-255 characters (excluding current)
     * - description: Optional string, max 500 characters
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        $departmentId = $this->route('department') ?? $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('departments', 'name')->ignore($departmentId),
            ],
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string> Custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Department name is required',
            'name.min' => 'Department name must be at least 2 characters',
            'name.unique' => 'This department already exists',
            'description.max' => 'Description cannot exceed 500 characters',
        ];
    }
}
