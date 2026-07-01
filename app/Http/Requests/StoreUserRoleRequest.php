<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreUserRoleRequest
 *
 * Handles validation for assigning roles to users.
 * Ensures valid user references and existing role names.
 *
 * @package App\Http\Requests
 */
class StoreUserRoleRequest extends FormRequest
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
     * - user_id: Required existing user reference
     * - roles: Optional array of existing role names
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
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
            'user_id.required' => 'User is required',
            'user_id.exists' => 'Selected user is invalid',
            'roles.array' => 'Roles must be an array',
            'roles.*.exists' => 'One or more selected roles do not exist',
        ];
    }
}
