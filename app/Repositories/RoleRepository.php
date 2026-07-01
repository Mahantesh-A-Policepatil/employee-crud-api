<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

/**
 * RoleRepository
 *
 * Repository for managing Role model database operations.
 * Provides role-specific query methods using Spatie Permission package.
 *
 * @package App\Repositories
 */
class RoleRepository extends BaseRepository
{
    /**
     * Get the Role model instance.
     *
     * @return Model The Role model
     */
    protected function getModel(): Model
    {
        return app(Role::class);
    }

    /**
     * Get roles paginated with search, sorting, and permissions loaded.
     *
     * @param int $length Number of records per page
     * @param int $start Starting offset
     * @param string|null $search Search term
     * @param string $orderColumn Column to order by
     * @param string $orderDir Order direction (asc/desc)
     *
     * @return array Array containing roles and count information
     */
    public function paginate(
        int $length,
        int $start,
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDir = 'asc'
    ): array {
        $query = $this->query()->with('permissions');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('guard_name', 'like', "%$search%");
            });
        }

        $total = $this->model->count();
        $filtered = $query->count();

        $roles = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return [
            'total' => $total,
            'filtered' => $filtered,
            'data' => $roles,
        ];
    }

    /**
     * Get a role with permissions loaded.
     *
     * @param int $id The role ID
     *
     * @return Role The role model with permissions loaded
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function findWithPermissions($id)
    {
        return $this->query()->with('permissions')->findOrFail($id);
    }

    /**
     * Get all roles as options (name as value and label).
     *
     * @return \Illuminate\Database\Eloquent\Collection Options formatted collection
     */
    public function getOptions()
    {
        return $this->query()
            ->orderBy('name')
            ->get(['name as value', 'name as label']);
    }

    /**
     * Find a role by name.
     *
     * @param string $name The role name
     * @param string $guardName The guard name (default: 'web')
     *
     * @return Role|null The role model or null if not found
     */
    public function findByName(string $name, string $guardName = 'web')
    {
        return $this->query()
            ->where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
    }

    /**
     * Create a new role or retrieve existing one.
     *
     * @param string $name The role name
     * @param string $guardName The guard name (default: 'web')
     *
     * @return Role The role model
     */
    public function firstOrCreate(string $name, string $guardName = 'web')
    {
        return $this->model->firstOrCreate(
            ['name' => $name, 'guard_name' => $guardName],
            ['name' => $name, 'guard_name' => $guardName]
        );
    }

    /**
     * Check if a role is a default/system role.
     *
     * @param string $roleName The role name to check
     *
     * @return bool True if role is system/default role
     */
    public function isSystemRole(string $roleName): bool
    {
        return in_array($roleName, ['admin', 'non-admin']);
    }
}
