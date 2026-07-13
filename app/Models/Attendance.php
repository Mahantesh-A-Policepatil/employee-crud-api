<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Attendance Model
 *
 * Represents an employee attendance summary for a specific month and year.
 * Each attendance record belongs to one employee.
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $employee_id
 * @property int $attendance_year
 * @property int $attendance_month
 * @property int $working_days
 * @property int $present_days
 * @property int $leave_days
 * @property int $lop_days
 * @property string|null $remarks
 *
 * @property Employee $employee
 *
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static Attendance|null find(int|string $id)
 * @method static Attendance findOrFail(int|string $id)
 * @method static Attendance create(array $attributes)
 */
class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'employee_id',
        'attendance_year',
        'attendance_month',
        'working_days',
        'present_days',
        'leave_days',
        'lop_days',
        'remarks',
    ];

    /**
     * Get employee for this attendance.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
