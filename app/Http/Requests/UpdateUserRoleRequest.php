<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateUserRoleRequest
 *
 * Handles validation for updating user role assignments.
 * Ensures valid role names for role synchronization.
 *
 * @package App\Http\Requests
 */
class UpdateUserRoleRequest extends FormRequest
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
     * - roles: Optional array of existing role names
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        return [
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
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
            'roles.array' => 'Roles must be an array',
            'roles.*.exists' => 'One or more selected roles do not exist',
        ];
    }
}
