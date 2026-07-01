<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * RoleController
 *
 * Handles all RESTful API operations for role management.
 * Implements DataTables server-side processing with role and permission association.
 * Uses repository pattern for database operations and form requests for validation.
 *
 * @package App\Http\Controllers\API
 */
class RoleController extends Controller
{
    /**
     * Role repository instance for database operations.
     *
     * @var RoleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * Permission repository instance for permission operations.
     *
     * @var PermissionRepository
     */
    private PermissionRepository $permissionRepository;

    /**
     * Create a new RoleController instance.
     *
     * @param RoleRepository $roleRepository The role repository
     * @param PermissionRepository $permissionRepository The permission repository
     */
    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Format role response with permissions.
     *
     * Loads role permissions and returns formatted array suitable for API responses.
     *
     * @param mixed $role The role model instance
     *
     * @return array Role data with associated permissions
     */
    private function formatRole($role): array
    {
        $role->loadMissing('permissions');
        $permissionNames = $role->permissions->pluck('name')->values();

        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $permissionNames,
            'permissions_display' => $permissionNames->implode(', '),
        ];
    }

    /**
     * Display a paginated list of roles with search and sorting.
     *
     * Implements server-side DataTables processing with role permissions loaded.
     *
     * @param Request $request The HTTP request containing DataTables parameters
     *                          (length, start, search.value, order.0.column, order.0.dir, draw)
     *
     * @return JsonResponse JSON response with DataTables format containing:
     *         - draw: DataTables draw counter
     *         - recordsTotal: Total number of roles
     *         - recordsFiltered: Number of roles matching search
     *         - data: Array of formatted role records
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

        $result = $this->roleRepository->paginate(
            $length,
            $start,
            $search,
            $orderColumn,
            $orderDir
        );

        $formattedData = collect($result['data'])->map(fn ($role) => $this->formatRole($role))->all();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $formattedData,
        ]);
    }

    /**
     * Create a new role record.
     *
     * Validates and creates a new role with the provided data and
     * syncs associated permissions.
     *
     * @param StoreRoleRequest $request The validated store request containing:
     *                                   - name: Role name (unique)
     *                                   - permissions: Optional array of permission names
     *
     * @return array Formatted role data with permissions
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function store(StoreRoleRequest $request): array
    {
        $validated = $request->validated();

        $role = $this->roleRepository->create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = $this->permissionRepository->findByNames($validated['permissions']);
            $role->syncPermissions($permissions);
        }

        return $this->formatRole($role);
    }

    /**
     * Retrieve a single role by ID.
     *
     * @param int $id The role ID to retrieve
     *
     * @return array Formatted role data with permissions
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function show($id): array
    {
        $role = $this->roleRepository->findWithPermissions($id);

        return $this->formatRole($role);
    }

    /**
     * Update an existing role record.
     *
     * Validates and updates a role's information and syncs permissions.
     *
     * @param UpdateRoleRequest $request The validated update request containing:
     *                                    - name: Updated role name (must be unique)
     *                                    - permissions: Optional array of permission names
     * @param int $id The role ID to update
     *
     * @return array Formatted role data with permissions
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function update(UpdateRoleRequest $request, $id): array
    {
        $validated = $request->validated();

        $role = $this->roleRepository->update($id, [
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $permissions = $this->permissionRepository->findByNames($validated['permissions']);
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return $this->formatRole($role);
    }

    /**
     * Delete a role record.
     *
     * Removes a role from the database. System roles (admin, non-admin) cannot be deleted.
     *
     * @param int $id The role ID to delete
     *
     * @return JsonResponse Deletion confirmation or error message
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function destroy($id): JsonResponse
    {
        $role = $this->roleRepository->findOrFail($id);

        if ($this->roleRepository->isSystemRole($role->name)) {
            return response()->json([
                'message' => 'Default roles cannot be deleted.',
            ], 422);
        }

        $this->roleRepository->delete($id);

        return response()->json(['message' => 'Deleted']);
    }
}
