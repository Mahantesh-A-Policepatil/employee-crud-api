<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRoleRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * UserRoleController
 *
 * Handles all RESTful API operations for managing user roles and permissions.
 * Implements DataTables server-side processing with role association management.
 * Uses repository pattern for database operations and form requests for validation.
 *
 * @package App\Http\Controllers\API
 */
class UserRoleController extends Controller
{
    /**
     * User repository instance for database operations.
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * Role repository instance for role operations.
     *
     * @var RoleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * Create a new UserRoleController instance.
     *
     * @param UserRepository $userRepository The user repository
     * @param RoleRepository $roleRepository The role repository
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Format user response with roles.
     *
     * Loads user roles and returns formatted array suitable for API responses.
     *
     * @param mixed $user The user model instance
     *
     * @return array User data with assigned roles
     */
    private function formatUser($user): array
    {
        $user->loadMissing('roles');
        $roleNames = $user->roles->pluck('name')->values();

        return [
            'id' => $user->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $roleNames,
            'roles_display' => $roleNames->implode(', '),
        ];
    }

    /**
     * Display a paginated list of users with search and sorting.
     *
     * Implements server-side DataTables processing with role information loaded.
     *
     * @param Request $request The HTTP request containing DataTables parameters
     *                          (length, start, search.value, order.0.column, order.0.dir, draw)
     *
     * @return JsonResponse JSON response with DataTables format containing:
     *         - draw: DataTables draw counter
     *         - recordsTotal: Total number of users
     *         - recordsFiltered: Number of users matching search
     *         - data: Array of formatted user records with roles
     */
    public function index(Request $request): JsonResponse
    {
        $columns = ['id', 'name', 'email'];

        $length = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');

        $result = $this->userRepository->paginate(
            $length,
            $start,
            $search,
            $orderColumn,
            $orderDir
        );

        $formattedData = collect($result['data'])->map(fn ($user) => $this->formatUser($user))->all();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $formattedData,
        ]);
    }

    /**
     * Get all users as options for dropdowns.
     *
     * Returns users formatted as options with id as value and name/email as label.
     *
     * @return mixed Collection of user options
     */
    public function userOptions()
    {
        return $this->userRepository->all()
            ->map(fn ($user) => [
                'value' => $user->id,
                'label' => $user->name . ' (' . $user->email . ')',
            ]);
    }

    /**
     * Get all roles as options for dropdowns.
     *
     * Returns roles formatted as options with name as value and label.
     *
     * @return mixed Collection of role options
     */
    public function roleOptions()
    {
        return $this->roleRepository->getOptions();
    }

    /**
     * Assign roles to a user.
     *
     * Validates and synchronizes roles for a specified user.
     *
     * @param StoreUserRoleRequest $request The validated store request containing:
     *                                       - user_id: Target user ID
     *                                       - roles: Optional array of role names
     *
     * @return array Formatted user data with assigned roles
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function store(StoreUserRoleRequest $request): array
    {
        $validated = $request->validated();

        $user = $this->userRepository->findOrFail($validated['user_id']);
        $user->syncRoles($validated['roles'] ?? []);

        return $this->formatUser($user);
    }

    /**
     * Get a user's assigned roles.
     *
     * @param int $id The user ID
     *
     * @return array Formatted user data with assigned roles
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function show($id): array
    {
        $user = $this->userRepository->findOrFail($id);

        return $this->formatUser($user);
    }

    /**
     * Update a user's role assignments.
     *
     * Validates and synchronizes new roles for a specified user.
     *
     * @param UpdateUserRoleRequest $request The validated update request containing:
     *                                        - roles: Optional array of role names
     * @param int $id The user ID to update
     *
     * @return array Formatted user data with updated roles
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function update(UpdateUserRoleRequest $request, $id): array
    {
        $validated = $request->validated();

        $user = $this->userRepository->findOrFail($id);
        $user->syncRoles($validated['roles'] ?? []);

        return $this->formatUser($user);
    }

    /**
     * Remove all roles from a user.
     *
     * Clears all role assignments for a specified user.
     *
     * @param int $id The user ID
     *
     * @return JsonResponse Confirmation message
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function destroy($id): JsonResponse
    {
        $user = $this->userRepository->findOrFail($id);
        $user->syncRoles([]);

        return response()->json(['message' => 'Roles removed']);
    }
}
