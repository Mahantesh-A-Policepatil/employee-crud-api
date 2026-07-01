<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Repositories\PermissionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * PermissionController
 *
 * Handles all RESTful API operations for permission management.
 * Implements DataTables server-side processing with search and sorting.
 * Uses repository pattern for database operations and form requests for validation.
 *
 * @package App\Http\Controllers\API
 */
class PermissionController extends Controller
{
    /**
     * Permission repository instance for database operations.
     *
     * @var PermissionRepository
     */
    private PermissionRepository $permissionRepository;

    /**
     * Create a new PermissionController instance.
     *
     * @param PermissionRepository $permissionRepository The permission repository
     */
    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Display a paginated list of permissions with search and sorting.
     *
     * Implements server-side DataTables processing with support for
     * search functionality and custom column sorting.
     *
     * @param Request $request The HTTP request containing DataTables parameters
     *                          (length, start, search.value, order.0.column, order.0.dir, draw)
     *
     * @return JsonResponse JSON response with DataTables format containing:
     *         - draw: DataTables draw counter
     *         - recordsTotal: Total number of permissions
     *         - recordsFiltered: Number of permissions matching search
     *         - data: Array of permission records
     */
    public function index(Request $request): JsonResponse
    {
        $columns = ['id', 'name', 'guard_name'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $result = $this->permissionRepository->paginate(
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
     * Get all permissions as options for dropdowns.
     *
     * Returns permissions formatted as options with id as value and name as label.
     *
     * @return mixed Collection of permission options
     */
    public function options()
    {
        return $this->permissionRepository->getOptions();
    }

    /**
     * Create a new permission record.
     *
     * Validates and creates a new permission with the provided data.
     *
     * @param StorePermissionRequest $request The validated store request containing:
     *                                         - name: Permission name (unique)
     *
     * @return mixed The newly created permission record
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function store(StorePermissionRequest $request)
    {
        $validated = $request->validated();

        return $this->permissionRepository->create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
    }

    /**
     * Retrieve a single permission by ID.
     *
     * @param int $id The permission ID to retrieve
     *
     * @return mixed The permission record
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function show($id)
    {
        return $this->permissionRepository->findOrFail($id);
    }

    /**
     * Update an existing permission record.
     *
     * Validates and updates a permission's information.
     *
     * @param UpdatePermissionRequest $request The validated update request containing:
     *                                          - name: Updated permission name (must be unique)
     * @param int $id The permission ID to update
     *
     * @return mixed The updated permission record
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function update(UpdatePermissionRequest $request, $id)
    {
        $validated = $request->validated();

        return $this->permissionRepository->update($id, [
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);
    }

    /**
     * Delete a permission record.
     *
     * Removes a permission from the database by ID.
     *
     * @param int $id The permission ID to delete
     *
     * @return JsonResponse Deletion confirmation message
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function destroy($id): JsonResponse
    {
        $this->permissionRepository->delete($id);

        return response()->json(['message' => 'Deleted']);
    }
}
