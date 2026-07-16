<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttendanceCsvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt|max:2048',
            'attendance_month' => 'required|integer|between:1,12',
            'attendance_year' => 'required|integer|digits:4',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a CSV file.',
            'file.mimes' => 'Only CSV files are allowed.',
            'attendance_month.required' => 'Please select an attendance month.',
            'attendance_year.required' => 'Please enter an attendance year.',
        ];
    }
}
