<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

/**
 * AttendanceRepository
 *
 * Repository for managing Attendance model database operations.
 *
 * @package App\Repositories
 */
class AttendanceRepository extends BaseRepository
{
    /**
     * Get Attendance model instance.
     */
    protected function getModel(): Model
    {
        return app(Attendance::class);
    }

    /**
     * Get attendance records with employee information.
     *
     * @return array
     */
    public function paginate(
        int $length,
        int $start,
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDir = 'asc'
    ): array {

        $query = $this->query()
            ->leftJoin('employees', 'attendances.employee_id', '=', 'employees.id')
            ->select(
                'attendances.*',
                'employees.name as employee_name',
                'employees.email',
                'employees.designation'
            );

        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('employees.name', 'like', "%{$search}%")
                    ->orWhere('employees.email', 'like', "%{$search}%")
                    ->orWhere('employees.designation', 'like', "%{$search}%");
            });
        }

        $total = $this->model->count();

        $filtered = $query->count();

        switch ($orderColumn) {

            case 'employee_name':
                $orderColumn = 'employees.name';
                break;

            default:
                $orderColumn = 'attendances.' . $orderColumn;
        }

        $attendance = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return [

            'total' => $total,

            'filtered' => $filtered,

            'data' => $attendance
        ];
    }

    /**
     * Check duplicate attendance.
     */
    public function exists(
        int $employeeId,
        int $month,
        int $year,
        ?int $ignoreId = null
    ): bool {

        $query = $this->query()

            ->where('employee_id', $employeeId)

            ->where('attendance_month', $month)

            ->where('attendance_year', $year);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function uploadCsv($file): array
{
    $handle = fopen($file->getRealPath(), 'r');

    $header = fgetcsv($handle);

    $imported = 0;
    $skipped = 0;
    $errors = [];

    while (($row = fgetcsv($handle)) !== false) {

        $data = array_combine($header, $row);

        $employee = Employee::where(
            'email',
            trim($data['Employee Email'])
        )->first();

        if (!$employee) {

            $errors[] = "Employee not found : ".$data['Employee Email'];

            $skipped++;

            continue;
        }

        Attendance::updateOrCreate(

            [
                'employee_id' => $employee->id,
                'attendance_month' => $data['Month'],
                'attendance_year' => $data['Year'],
            ],

            [
                'working_days' => $data['Working Days'],
                'present_days' => $data['Present Days'],
                'leave_days' => $data['Leave Days'],
                'lop_days' => $data['LOP Days'],
                'remarks' => $data['Remarks'],
            ]

        );

        $imported++;

    }

    fclose($handle);

    return [

        'message' => "CSV imported successfully.",

        'imported' => $imported,

        'skipped' => $skipped,

        'errors' => $errors,

    ];
}
}
