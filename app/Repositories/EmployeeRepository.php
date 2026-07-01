<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

/**
 * EmployeeRepository
 *
 * Repository for managing Employee model database operations.
 * Provides employee-specific query methods with department relationships.
 *
 * @package App\Repositories
 */
class EmployeeRepository extends BaseRepository
{
    /**
     * Get the Employee model instance.
     *
     * @return Model The Employee model
     */
    protected function getModel(): Model
    {
        return app(Employee::class);
    }

    /**
     * Get employees paginated with search, sorting, and department information.
     *
     * @param int $length Number of records per page
     * @param int $start Starting offset
     * @param string|null $search Search term
     * @param string $orderColumn Column to order by
     * @param string $orderDir Order direction (asc/desc)
     *
     * @return array Array containing employees and count information
     */
    public function paginate(
        int $length,
        int $start,
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDir = 'asc'
    ): array {
        $query = $this->query()
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->select('employees.*', 'departments.name as department_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('employees.name', 'like', "%$search%")
                    ->orWhere('employees.email', 'like', "%$search%")
                    ->orWhere('employees.phone', 'like', "%$search%")
                    ->orWhere('departments.name', 'like', "%$search%");
            });
        }

        $total = $this->model->count();
        $filtered = $query->count();

        $orderColumn = $orderColumn === 'department_name' ? 'departments.name' : 'employees.' . $orderColumn;

        $employees = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return [
            'total' => $total,
            'filtered' => $filtered,
            'data' => $employees,
        ];
    }

    /**
     * Find an employee by email address.
     *
     * @param string $email The employee's email address
     *
     * @return Employee|null The employee model or null if not found
     */
    public function findByEmail(string $email)
    {
        return $this->query()->where('email', $email)->first();
    }

    /**
     * Find an employee by phone number.
     *
     * @param string $phone The employee's phone number
     *
     * @return Employee|null The employee model or null if not found
     */
    public function findByPhone(string $phone)
    {
        return $this->query()->where('phone', $phone)->first();
    }
}
