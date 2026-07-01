<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 *
 * Represents a user account in the system. Users are authenticated via Sanctum tokens
 * and can be assigned roles and permissions using the Spatie Permission package.
 * This model is the foundation for authentication and authorization.
 *
 * @package App\Models
 *
 * @property int $id The user's unique identifier (primary key)
 * @property string $name The user's full name
 * @property string $email The user's email address (unique, required)
 * @property \Illuminate\Support\Carbon|null $email_verified_at Timestamp when email was verified (nullable)
 * @property string $password The user's hashed password
 * @property string|null $remember_token Remember me token (nullable)
 * @property \Carbon\Carbon $created_at Timestamp when the record was created
 * @property \Carbon\Carbon $updated_at Timestamp when the record was last updated
 * @property Collection<\Spatie\Permission\Models\Role> $roles User's assigned roles (lazy loaded)
 * @property Collection<\Spatie\Permission\Models\Permission> $permissions User's direct permissions (lazy loaded)
 * @property Collection $tokens API tokens for authentication
 * @property Collection<\Illuminate\Notifications\Notification> $notifications User notifications
 *
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static User|null find(int|string $id)
 * @method static User findOrFail(int|string $id)
 * @method static User create(array $attributes)
 * @method static \Illuminate\Database\Eloquent\Builder where(string $column, mixed $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder orderBy(string $column, string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder with(array|string $relations)
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * These attributes can be set using mass assignment methods like create() or update().
     * Sensitive attributes like password should be explicitly set, not mass assigned in production.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * These sensitive attributes will not be included when the model is converted to an array or JSON.
     * This prevents accidental exposure of passwords and tokens in API responses.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * Specifies which attributes should be automatically cast to specific types
     * when retrieved from or stored in the database.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
