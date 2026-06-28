<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Employee Model
 *
 * Represents an employee record in the system. This model handles
 * the data structure and mass assignment for employee attributes.
 *
 * @package App\Models
 * @property int $id The employee's unique identifier
 * @property string $name The employee's full name
 * @property string $email The employee's email address (unique)
 * @property string $phone The employee's phone number (unique, 10 digits)
 * @property string $designation The employee's job designation
 * @property \Carbon\Carbon $created_at Timestamp when the record was created
 * @property \Carbon\Carbon $updated_at Timestamp when the record was last updated
 */
class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
    	'name', 'email', 'phone', 'designation'
	];
}
