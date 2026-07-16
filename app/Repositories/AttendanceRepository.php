<?php

namespace App\Repositories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

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

    public function uploadCsv(UploadedFile $file, int $month, int $year): array
    {
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'file' => 'The selected CSV file could not be read.',
            ]);
        }

        $header = fgetcsv($handle);
        $expectedHeaders = [
            'Employee Email',
            'Working Days',
            'Present Days',
            'Leave Days',
            'LOP Days',
            'Remarks',
        ];

        if ($header === false) {
            fclose($handle);

            throw ValidationException::withMessages([
                'file' => 'The CSV file is empty.',
            ]);
        }

        $header = array_map(static fn ($value) => trim((string) $value), $header);
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        if ($header !== $expectedHeaders) {
            fclose($handle);

            throw ValidationException::withMessages([
                'file' => 'Invalid CSV columns. Download the attendance template and do not change its header row.',
            ]);
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($row === [null] || $row === []) {
                continue;
            }

            if (count($row) !== count($header)) {
                $errors[] = "Row {$rowNumber}: column count does not match the template.";
                $skipped++;
                continue;
            }

            $data = array_combine($header, $row);
            $email = trim($data['Employee Email']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: enter a valid employee email address.";
                $skipped++;
                continue;
            }

            $employee = Employee::where('email', $email)->first();

            if (!$employee) {
                $errors[] = "Row {$rowNumber}: employee with email {$email} was not found.";
                $skipped++;
                continue;
            }

            $days = [
                'working_days' => $data['Working Days'],
                'present_days' => $data['Present Days'],
                'leave_days' => $data['Leave Days'],
                'lop_days' => $data['LOP Days'],
            ];

            if (!$this->hasValidDayValues($days)) {
                $errors[] = "Row {$rowNumber}: attendance day values must be whole numbers between 0 and 31.";
                $skipped++;
                continue;
            }

            if ((int) $days['working_days'] < 1 || (int) $days['present_days'] > (int) $days['working_days']) {
                $errors[] = "Row {$rowNumber}: working days must be at least 1 and present days cannot exceed working days.";
                $skipped++;
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'attendance_month' => $month,
                    'attendance_year' => $year,
                ],
                [
                    ...array_map('intval', $days),
                    'remarks' => trim($data['Remarks']) ?: null,
                ]
            );

            $imported++;
        }

        fclose($handle);

        return [
            'message' => "CSV import completed. {$imported} record(s) imported and {$skipped} skipped.",
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Determine whether all attendance counts are valid whole-day values.
     *
     * @param array<string, string> $days
     */
    private function hasValidDayValues(array $days): bool
    {
        foreach ($days as $value) {
            if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 0 || (int) $value > 31) {
                return false;
            }
        }

        return true;
    }
}
