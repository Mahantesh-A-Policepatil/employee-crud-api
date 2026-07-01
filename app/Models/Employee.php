<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Employee Model
 *
 * Represents an employee record in the system. This model handles
 * the data structure and mass assignment for employee attributes.
 * Each employee belongs to a department.
 *
 * @package App\Models
 *
 * @property int $id The employee's unique identifier (primary key)
 * @property int $department_id The foreign key referencing the employee's department
 * @property string $name The employee's full name (required)
 * @property string $email The employee's email address (unique, required)
 * @property string $phone The employee's phone number (unique, 10 digits, required)
 * @property string $designation The employee's job designation/title (required)
 * @property \Carbon\Carbon $created_at Timestamp when the record was created
 * @property \Carbon\Carbon $updated_at Timestamp when the record was last updated
 * @property Department $department The department this employee belongs to (lazy loaded)
 * @property string|null $department_name The name of the department (available when joined in queries)
 *
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static Employee|null find(int|string $id)
 * @method static Employee findOrFail(int|string $id)
 * @method static Employee create(array $attributes)
 * @method static \Illuminate\Database\Eloquent\Builder where(string $column, mixed $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder orderBy(string $column, string $direction = 'asc')
 */
class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'name',
        'email',
        'phone',
        'designation',
    ];

    /**
     * Get the department that this employee belongs to.
     *
     * Establishes a many-to-one relationship with the Department model.
     * Each employee belongs to exactly one department.
     *
     * @return BelongsTo The department relationship
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
