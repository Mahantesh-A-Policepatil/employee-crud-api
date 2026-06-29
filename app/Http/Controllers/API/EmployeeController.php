<?php

namespace App\Http\Controllers\API;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Employee API Controller
 *
 * Handles all RESTful API operations for employee management including
 * listing, creating, retrieving, updating, and deleting employees.
 * Implements server-side processing for DataTables and comprehensive validation.
 *
 * @package App\Http\Controllers\API
 */
class EmployeeController extends Controller
{
    /**
     * Display a paginated list of employees with search and sorting support.
     *
     * This method implements server-side DataTables processing, supporting
     * search functionality and custom column sorting.
     *
     * @param Request $request The HTTP request containing DataTables parameters
     *                          (length, start, search.value, order.0.column, order.0.dir, draw)
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing:
     *         - draw: DataTables draw counter
     *         - recordsTotal: Total number of employees in database
     *         - recordsFiltered: Number of employees matching search criteria
     *         - data: Array of employee records
     */
    public function index(Request $request)
    {
        $columns = ['id', 'department_name', 'name', 'email', 'phone', 'designation'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $query = Employee::query()
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->select('employees.*', 'departments.name as department_name');

        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('employees.name', 'like', "%$search%")
                  ->orWhere('employees.email', 'like', "%$search%")
                  ->orWhere('employees.phone', 'like', "%$search%")
                  ->orWhere('departments.name', 'like', "%$search%");
            });
        }

        $total = Employee::count();
        $filtered = $query->count();

        $orderColumn = $orderColumn === 'department_name' ? 'departments.name' : 'employees.' . $orderColumn;

        $employees = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered,
            "data" => $employees
        ]);
    }

    /**
     * Create a new employee record.
     *
     * Validates and creates a new employee with the provided data.
     * Email and phone number must be unique in the database.
     *
     * @param Request $request The HTTP request containing employee data
     *                          - name (required): String between 2-255 characters
     *                          - email (required): Valid unique email address
     *                          - phone (required): Unique 10-digit phone number
     *                          - designation (required): String between 2-255 characters
     *
     * @return Employee The newly created employee record
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails (422 response)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'department_id' => 'required|exists:departments,id',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|digits:10|unique:employees,phone',
            'designation' => 'required|string|min:2|max:255',
        ], [
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
        ]);

        return Employee::create($validated);
    }

    /**
     * Retrieve a single employee by ID.
     *
     * Fetches and returns a specific employee record from the database.
     *
     * @param int $id The employee ID to retrieve
     *
     * @return Employee The employee record with the specified ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If employee not found (404 response)
     */
    public function show($id)
    {
        return Employee::findOrFail($id);
    }

    /**
     * Update an existing employee record.
     *
     * Validates and updates an employee's information. Email and phone number
     * must remain unique (excluding the current employee's existing values).
     *
     * @param Request $request The HTTP request containing updated employee data
     *                          - name (required): String between 2-255 characters
     *                          - email (required): Valid unique email address (excluding current record)
     *                          - phone (required): Unique 10-digit phone number (excluding current record)
     *                          - designation (required): String between 2-255 characters
     * @param int $id The employee ID to update
     *
     * @return Employee The updated employee record
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails (422 response)
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If employee not found (404 response)
     */
    public function update(Request $request, $id)
    {
        $emp = Employee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'department_id' => 'required|exists:departments,id',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|digits:10|unique:employees,phone,' . $id,
            'designation' => 'required|string|min:2|max:255',
        ], [
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
        ]);

        $emp->update($validated);
        return $emp;
    }

    /**
     * Delete an employee record.
     *
     * Removes an employee from the database by their ID.
     *
     * @param int $id The employee ID to delete
     *
     * @return \Illuminate\Http\JsonResponse JSON response with deletion confirmation message
     */
    public function destroy($id)
    {
        Employee::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
