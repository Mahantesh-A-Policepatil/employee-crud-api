<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateUserRequest
 *
 * Handles validation for user profile update requests.
 * Ensures unique email and optional password change with confirmation.
 *
 * @package App\Http\Requests
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always returns true as users can update their own profile
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Validates:
     * - name: Required string, max 255 characters
     * - email: Required unique email (excluding current user)
     * - password: Optional, minimum 8 characters with confirmation if provided
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
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
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'This email is already in use',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
