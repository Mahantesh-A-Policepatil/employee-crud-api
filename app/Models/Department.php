<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Department Model
 *
 * Represents a department record in the system. Departments group employees
 * and serve as organizational units within the company.
 *
 * @package App\Models
 *
 * @property int $id The department's unique identifier (primary key)
 * @property string $name The department's name (unique, required)
 * @property string|null $description The department's description (optional)
 * @property \Carbon\Carbon $created_at Timestamp when the record was created
 * @property \Carbon\Carbon $updated_at Timestamp when the record was last updated
 * @property Collection<Employee> $employees The employees belonging to this department (lazy loaded)
 *
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static Department|null find(int|string $id)
 * @method static Department findOrFail(int|string $id)
 * @method static Department create(array $attributes)
 * @method static \Illuminate\Database\Eloquent\Builder where(string $column, mixed $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder orderBy(string $column, string $direction = 'asc')
 */
class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the employees that belong to this department.
     *
     * Establishes a one-to-many relationship with the Employee model.
     * One department can have many employees.
     *
     * @return HasMany The employees relationship
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
