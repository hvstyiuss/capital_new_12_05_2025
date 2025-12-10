<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByPpr(string $ppr): ?User
    {
        return $this->model->where('ppr', $ppr)->first();
    }
}





