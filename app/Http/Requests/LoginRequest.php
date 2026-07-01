<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * LoginRequest
 *
 * Handles validation for user login requests.
 * Ensures proper email and password validation.
 *
 * @package App\Http\Requests
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always returns true as login is open to all users
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Validates:
     * - email: Required valid email format
     * - password: Required string
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
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
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'password.required' => 'Password is required',
        ];
    }
}
