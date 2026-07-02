<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateEmployeeRequest
 *
 * Handles validation for updating existing employee records.
 * Ensures unique emails/phones excluding current record.
 *
 * @package App\Http\Requests
 */
class UpdateEmployeeRequest extends FormRequest
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
     * - name: Required string, 2-255 characters
     * - email: Required unique email (excluding current employee)
     * - phone: Required unique 10-digit phone (excluding current employee)
     * - designation: Required string, 2-255 characters
     * - department_id: Required existing department reference
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> The validation rules
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee') ?? $this->route('id');

        return [
            'project_id' => 'nullable|integer|exists:projects,id',
            'name' => 'required|string|min:2|max:255',
            'department_id' => 'required|exists:departments,id',
            'email' => [
                'required',
                'email',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
            'phone' => [
                'required',
                'digits:10',
                Rule::unique('employees', 'phone')->ignore($employeeId),
            ],
            'designation' => 'required|string|min:2|max:255',
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
            'name.min' => 'Name must be at least 2 characters',
            'department_id.required' => 'Department is required',
            'department_id.exists' => 'Selected department is invalid',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'This email already exists',
            'phone.required' => 'Phone number is required',
            'phone.digits' => 'Phone number must be exactly 10 digits',
            'phone.unique' => 'This phone number already exists',
            'designation.required' => 'Designation is required',
            'designation.min' => 'Designation must be at least 2 characters',
        ];
    }
}
