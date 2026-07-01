<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateRoleRequest
 *
 * Handles validation for updating existing role records.
 * Ensures unique role names excluding current record.
 *
 * @package App\Http\Requests
 */
class UpdateRoleRequest extends FormRequest
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
     * - permissions: Optional array of existing permission names
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        $roleId = $this->route('role') ?? $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId),
            ],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
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
            'name.required' => 'Role name is required',
            'name.unique' => 'This role already exists',
            'permissions.array' => 'Permissions must be an array',
            'permissions.*.exists' => 'One or more selected permissions do not exist',
        ];
    }
}
