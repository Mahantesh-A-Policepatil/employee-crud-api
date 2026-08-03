<?php

namespace App\Repositories;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * DepartmentRepository
 *
 * Repository for managing Department model database operations.
 * Provides department-specific query methods and data access layer.
 *
 * @package App\Repositories
 */
class DepartmentRepository extends BaseRepository
{
    /**
     * Get the Department model instance.
     *
     * @return Model The Department model
     */
    protected function getModel(): Model
    {
        return app(Department::class);
    }

    /**
     * Search departments by name and/or description.
     *
     * @param string $search The search term
     *
     * @return \Illuminate\Database\Eloquent\Collection Departments matching search criteria
     */
    public function search(string $search)
    {
        return $this->query()
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            })
            ->get();
    }

    /**
     * Get departments paginated with search and sorting.
     *
     * @param int $length Number of records per page
     * @param int $start Starting offset
     * @param string|null $search Search term
     * @param string $orderColumn Column to order by
     * @param string $orderDir Order direction (asc/desc)
     *
     * @return array Array containing departments and count information
     */
    public function paginate(
        int $length,
        int $start,
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDir = 'asc'
    ): array {
        $query = $this->query()
            ->with(['employees.project'])
            ->withCount('employees');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereHas('employees', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $total = $this->model->count();
        $filtered = $query->count();

        $departments = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return [
            'total' => $total,
            'filtered' => $filtered,
            'data' => $departments,
        ];
    }

    /**
     * Get all departments as options (id as value, name as label).
     *
     * @return \Illuminate\Database\Eloquent\Collection Options formatted collection
     */
    public function getOptions()
    {
        return $this->query()
            ->orderBy('name')
            ->get(['id as value', 'name as label']);
    }

    public function createWithEmployees(array $attributes): Department
    {
        return DB::transaction(function () use ($attributes) {
            $employeeIds = $attributes['employee_ids'] ?? [];
            unset($attributes['employee_ids']);

            /** @var Department $department */
            $department = $this->create($attributes);
            $this->syncEmployees($department, $employeeIds);

            return $this->findWithEmployees($department->id);
        });
    }

    public function updateWithEmployees($id, array $attributes): Department
    {
        return DB::transaction(function () use ($id, $attributes) {
            $shouldSyncEmployees = array_key_exists('employee_ids', $attributes);
            $employeeIds = $attributes['employee_ids'] ?? [];
            unset($attributes['employee_ids']);

            /** @var Department $department */
            $department = $this->update($id, $attributes);

            if ($shouldSyncEmployees) {
                $this->syncEmployees($department, $employeeIds);
            }

            return $this->findWithEmployees($department->id);
        });
    }

    public function findWithEmployees($id): Department
    {
        /** @var Department $department */
        $department = $this->query()->with(['employees.project'])->findOrFail($id);
        $department->setAttribute('employee_ids', $department->employees->pluck('id')->values());

        return $department;
    }

    /**
     * Delete a department while preserving its employees.
     *
     * Employees previously assigned to the department remain in the system
     * with no department assignment after the deletion.
     */
    public function deleteWithEmployeesDetached($id): bool
    {
        return DB::transaction(function () use ($id) {
            /** @var Department $department */
            $department = $this->findOrFail($id);

            Employee::where('department_id', $department->id)
                ->update(['department_id' => null]);

            return (bool) $department->delete();
        });
    }

    private function syncEmployees(Department $department, array $employeeIds): void
    {
        Employee::where('department_id', $department->id)
            ->when(
                count($employeeIds) > 0,
                fn ($query) => $query->whereNotIn('id', $employeeIds)
            )
            ->update(['department_id' => null]);

        if (count($employeeIds) > 0) {
            Employee::whereIn('id', $employeeIds)
                ->update(['department_id' => $department->id]);
        }
    }
}
