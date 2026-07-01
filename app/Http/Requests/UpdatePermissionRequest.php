<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdatePermissionRequest
 *
 * Handles validation for updating existing permission records.
 * Ensures unique permission names excluding current record.
 *
 * @package App\Http\Requests
 */
class UpdatePermissionRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        $permissionId = $this->route('permission') ?? $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permissionId),
            ],
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
            'name.required' => 'Permission name is required',
            'name.unique' => 'This permission already exists',
        ];
    }
}
