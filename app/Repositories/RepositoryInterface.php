<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all records.
     */
    public function all();

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15);

    /**
     * Find a record by ID.
     */
    public function find($id);

    /**
     * Find a record by ID or fail.
     */
    public function findOrFail($id);

    /**
     * Create a new record.
     */
    public function create(array $data);

    /**
     * Update a record.
     */
    public function update($id, array $data);

    /**
     * Delete a record.
     */
    public function delete($id);

    /**
     * Get records matching criteria.
     */
    public function where(string $column, $value);

    /**
     * Get records with relationships.
     */
    public function with(array $relations);
}




