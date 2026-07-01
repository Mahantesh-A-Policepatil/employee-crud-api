<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * UserRepository
 *
 * Repository for managing User model database operations.
 * Provides user-specific query methods including role and permission loading.
 *
 * @package App\Repositories
 */
class UserRepository extends BaseRepository
{
    /**
     * Get the User model instance.
     *
     * @return Model The User model
     */
    protected function getModel(): Model
    {
        return app(User::class);
    }

    /**
     * Find a user by email address.
     *
     * @param string $email The user's email address
     *
     * @return User|null The user model or null if not found
     */
    public function findByEmail(string $email)
    {
        return $this->query()->where('email', $email)->first();
    }

    /**
     * Get a user with roles and permissions loaded.
     *
     * @param int $id The user ID
     *
     * @return User|null The user model with relationships loaded
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function findWithRelations($id)
    {
        return $this->findOrFail($id)->loadMissing('roles', 'permissions');
    }

    /**
     * Get all users with roles and permissions paginated.
     *
     * @param int $length Number of records per page
     * @param int $start Starting offset
     * @param string|null $search Search term
     * @param string $orderColumn Column to order by
     * @param string $orderDir Order direction (asc/desc)
     *
     * @return array Array containing users and count information
     */
    public function paginate(
        int $length,
        int $start,
        ?string $search = null,
        string $orderColumn = 'id',
        string $orderDir = 'asc'
    ): array {
        $query = $this->query()->with('roles');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $total = $this->model->count();
        $filtered = $query->count();

        $users = $query
            ->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        return [
            'total' => $total,
            'filtered' => $filtered,
            'data' => $users,
        ];
    }
}
