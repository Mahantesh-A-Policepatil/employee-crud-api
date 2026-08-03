<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Repositories\DepartmentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DepartmentController
 *
 * Handles all RESTful API operations for department management.
 * Implements DataTables server-side processing with search and sorting.
 * Uses repository pattern for database operations and form requests for validation.
 *
 * @package App\Http\Controllers\API
 */
class DepartmentController extends Controller
{
    /**
     * Department repository instance for database operations.
     *
     * @var DepartmentRepository
     */
    private DepartmentRepository $departmentRepository;

    /**
     * Create a new DepartmentController instance.
     *
     * @param DepartmentRepository $departmentRepository The department repository
     */
    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Display a paginated list of departments with search and sorting.
     *
     * Implements server-side DataTables processing with support for
     * search functionality and custom column sorting.
     *
     * @param Request $request The HTTP request containing DataTables parameters
     *                          (length, start, search.value, order.0.column, order.0.dir, draw)
     *
     * @return JsonResponse JSON response with DataTables format containing:
     *         - draw: DataTables draw counter
     *         - recordsTotal: Total number of departments
     *         - recordsFiltered: Number of departments matching search
     *         - data: Array of department records
     */
    public function index(Request $request): JsonResponse
    {
        $columns = [null, 'id', 'name', 'description', 'employees_count'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $result = $this->departmentRepository->paginate(
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
     * Get all departments as options for dropdowns.
     *
     * Returns departments formatted as options with id as value and name as label.
     *
     * @return mixed Collection of department options
     */
    public function options()
    {
        return $this->departmentRepository->getOptions();
    }

    /**
     * Create a new department record.
     *
     * Validates and creates a new department with the provided data.
     * Department names must be unique.
     *
     * @param StoreDepartmentRequest $request The validated store request
     *
     * @return mixed The newly created department record
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function store(StoreDepartmentRequest $request)
    {
        return $this->departmentRepository->createWithEmployees($request->validated());
    }

    /**
     * Retrieve a single department by ID.
     *
     * @param int $id The department ID to retrieve
     *
     * @return mixed The department record
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function show($id)
    {
        return $this->departmentRepository->findWithEmployees($id);
    }

    /**
     * Update an existing department record.
     *
     * Validates and updates a department's information.
     * Department name must remain unique.
     *
     * @param UpdateDepartmentRequest $request The validated update request
     * @param int $id The department ID to update
     *
     * @return mixed The updated department record
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        return $this->departmentRepository->updateWithEmployees($id, $request->validated());
    }

    /**
     * Delete a department record.
     *
     * Removes a department from the database by ID.
     *
     * @param int $id The department ID to delete
     *
     * @return JsonResponse Deletion confirmation message
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function destroy($id): JsonResponse
    {
        $this->departmentRepository->deleteWithEmployeesDetached($id);

        return response()->json(['message' => 'Deleted']);
    }
}
