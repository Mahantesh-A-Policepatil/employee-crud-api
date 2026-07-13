<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreAttendanceRequest
 *
 * Handles validation for creating employee attendance records.
 *
 * @package App\Http\Requests
 */
class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'employee_id' => 'required|exists:employees,id',
            'attendance_year' => 'required|integer|digits:4',
            'attendance_month' => 'required|integer|between:1,12',
            'working_days' => 'required|integer|min:1|max:31',
            'present_days' => 'required|integer|min:0|max:31',
            'leave_days' => 'nullable|integer|min:0|max:31',
            'lop_days' => 'nullable|integer|min:0|max:31',
            'remarks' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'employee_id.required' => 'Employee is required.',
            'employee_id.exists' => 'Selected employee is invalid.',
            'attendance_year.required' => 'Attendance year is required.',
            'attendance_month.required' => 'Attendance month is required.',
            'attendance_month.between' => 'Attendance month must be between January and December.',
            'working_days.required' => 'Working days is required.',
            'working_days.max' => 'Working days cannot exceed 31.',
            'present_days.required' => 'Present days is required.',
            'leave_days.max' => 'Leave days cannot exceed 31.',
            'lop_days.max' => 'LOP days cannot exceed 31.',
        ];
    }
}
