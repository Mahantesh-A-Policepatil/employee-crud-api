<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * BaseRepository
 *
 * Abstract base repository providing common CRUD operations for all models.
 * Implements repository pattern to abstract eloquent queries from controllers.
 *
 * @package App\Repositories
 * @template T of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseRepository
{
    /**
     * The eloquent model instance.
     *
     * @var Model|null
     */
    protected ?Model $model = null;

    /**
     * Create a new repository instance.
     * Subclasses must set $model in their constructor.
     */
    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get the model class name for this repository.
     * Must be implemented by subclasses.
     *
     * @return Model The eloquent model instance
     */
    abstract protected function getModel(): Model;

    /**
     * Get all records from the table.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of model instances
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find a record by primary key.
     *
     * @param int|string $id The primary key value
     *
     * @return Model|null The model instance or null if not found
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find a record by primary key or throw exception.
     *
     * @param int|string $id The primary key value
     *
     * @return Model The model instance
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a new record in the database.
     *
     * @param array $attributes The attributes to assign to the model
     *
     * @return Model The created model instance
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a record in the database.
     *
     * @param int|string $id The primary key value
     * @param array $attributes The attributes to update
     *
     * @return Model The updated model instance
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function update($id, array $attributes)
    {
        $model = $this->findOrFail($id);
        $model->update($attributes);

        return $model;
    }

    /**
     * Delete a record from the database.
     *
     * @param int|string $id The primary key value
     *
     * @return bool True if deletion was successful
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If not found
     */
    public function delete($id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }

    /**
     * Get a query builder instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Builder Query builder instance
     */
    public function query()
    {
        return $this->model->query();
    }
}
