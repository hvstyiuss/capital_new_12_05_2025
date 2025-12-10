<?php

namespace App\Repositories;

use App\Models\Conge;

class CongeRepository extends BaseRepository
{
    public function __construct(Conge $model)
    {
        parent::__construct($model);
    }
}




