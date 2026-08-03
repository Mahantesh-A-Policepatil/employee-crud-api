<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

/**
 * PermissionRepository
 *
 * Repository for managing Permission model database operations.
 * Provides permission-specific query methods using Spatie Permission package.
 *
 * @package App\Repositories
 */
class PermissionRepository extends BaseRepository
{
    /**
     * Get the Permission model instance.
     *
     * @return Model The Permission model
     */
    protected function getModel(): Model
    {
        return app(Permission::class);
    }

    /**
     * Get permissions paginated with search and sorting.
     *
     * @param int $length Number of records per page
     * @param int $start Starting offset
     * @param string|null $search Search term
     * @param string $orderColumn Column to order by
     * @param string $orderDir Order direction (asc/desc)
     *
     * @return array Array containing permissions and count information
     */
    public function paginate(
        int $length,
        int $start,
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDir = 'asc'
    ): array {
        $query = $this->query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('guard_name', 'like', "%$search%");
            });
        }

        $total = $this->model->count();
        $filtered = $query->count();

        $permissions = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return [
            'total' => $total,
            'filtered' => $filtered,
            'data' => $permissions,
        ];
    }

    /**
     * Get all permissions as options (id as value, name as label).
     *
     * @return \Illuminate\Database\Eloquent\Collection Options formatted collection
     */
    public function getOptions()
    {
        return $this->query()
            ->orderBy('name')
            ->get(['id as value', 'name as label']);
    }

    /**
     * Get permissions grouped by resource for role assignment checkboxes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getGroupedOptions()
    {
        $actionLabels = [
            'view' => 'Read',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'manage' => 'Create / Update / Delete',
        ];
        $actionOrder = array_flip(array_keys($actionLabels));

        return $this->query()
            ->orderBy('name')
            ->pluck('name')
            // `*.view` was the legacy name for the canonical `*.read`
            // permission. Do not expose both as separate "Read" choices.
            ->reject(fn ($name) => Str::endsWith($name, '.view'))
            ->groupBy(fn ($name) => Str::before($name, '.'))
            ->map(function ($permissions, $resource) use ($actionLabels, $actionOrder) {
                return [
                    'label' => Str::headline($resource),
                    'options' => $permissions
                        ->sortBy(fn ($permission) => $actionOrder[Str::after($permission, '.')] ?? PHP_INT_MAX)
                        ->map(function ($permission) use ($actionLabels) {
                        $action = Str::after($permission, '.');

                        return [
                            'value' => $permission,
                            'label' => $actionLabels[$action] ?? Str::headline($action),
                        ];
                    })->values(),
                ];
            })
            ->values();
    }

    /**
     * Find a permission by name.
     *
     * @param string $name The permission name
     *
     * @return Permission|null The permission model or null if not found
     */
    public function findByName(string $name)
    {
        return $this->query()->where('name', $name)->first();
    }

    /**
     * Get multiple permissions by name array.
     *
     * @param array $names Array of permission names
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of permission models
     */
    public function findByNames(array $names)
    {
        return $this->query()->whereIn('name', $names)->get();
    }
}
