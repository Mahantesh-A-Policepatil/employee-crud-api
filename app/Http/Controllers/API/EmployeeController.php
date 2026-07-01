<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * EmployeeController
 *
 * Handles all RESTful API operations for employee management including
 * listing, creating, retrieving, updating, and deleting employees.
 * Implements DataTables server-side processing with search and sorting.
 * Uses repository pattern for database operations and form requests for validation.
 *
 * @package App\Http\Controllers\API
 */
class EmployeeController extends Controller
{
    /**
     * Employee repository instance for database operations.
     *
     * @var EmployeeRepository
     */
    private EmployeeRepository $employeeRepository;

    /**
     * Create a new EmployeeController instance.
     *
     * @param EmployeeRepository $employeeRepository The employee repository
     */
    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Display a paginated list of employees with search and sorting support.
     *
     * Implements server-side DataTables processing with support for
     * search functionality and custom column sorting. Includes department
     * information via left join.
     *
     * @param Request $request The HTTP request containing DataTables parameters
     *                          (length, start, search.value, order.0.column, order.0.dir, draw)
     *
     * @return JsonResponse JSON response with DataTables format containing:
     *         - draw: DataTables draw counter
     *         - recordsTotal: Total number of employees in database
     *         - recordsFiltered: Number of employees matching search criteria
     *         - data: Array of employee records with department information
     */
    public function index(Request $request): JsonResponse
    {
        $columns = ['id', 'department_name', 'name', 'email', 'phone', 'designation'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $result = $this->employeeRepository->paginate(
            $length,
            $start,
            $search,
            $orderColumn,
            $orderDir
        );

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $result['data'],
        ]);
    }

    /**
     * Create a new employee record.
     *
     * Validates and creates a new employee with the provided data.
     * Email and phone number must be unique in the database.
     *
     * @param StoreEmployeeRequest $request The validated store request containing:
     *                                       - name: Employee's full name
     *                                       - email: Unique email address
     *                                       - phone: Unique 10-digit phone number
     *                                       - designation: Job title/designation
     *                                       - department_id: Valid department reference
     *
     * @return mixed The newly created employee record
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function store(StoreEmployeeRequest $request)
    {
        return $this->employeeRepository->create($request->validated());
    }

    /**
     * Retrieve a single employee by ID.
     *
     * Fetches and returns a specific employee record from the database.
     *
     * @param int $id The employee ID to retrieve
     *
     * @return mixed The employee record with the specified ID
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If employee not found
     */
    public function show($id)
    {
        return $this->employeeRepository->findOrFail($id);
    }

    /**
     * Update an existing employee record.
     *
     * Validates and updates an employee's information. Email and phone
     * number must remain unique (excluding current employee's values).
     *
     * @param UpdateEmployeeRequest $request The validated update request containing:
     *                                        - name: Updated employee name
     *                                        - email: Updated email (must be unique or current)
     *                                        - phone: Updated phone (must be unique or current)
     *                                        - designation: Updated job title
     *                                        - department_id: Updated department reference
     * @param int $id The employee ID to update
     *
     * @return mixed The updated employee record
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If employee not found
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        return $this->employeeRepository->update($id, $request->validated());
    }

    /**
     * Delete an employee record.
     *
     * Removes an employee from the database by their ID.
     *
     * @param int $id The employee ID to delete
     *
     * @return JsonResponse Deletion confirmation message
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If employee not found
     */
    public function destroy($id): JsonResponse
    {
        $this->employeeRepository->delete($id);

        return response()->json(['message' => 'Deleted']);
    }
}
