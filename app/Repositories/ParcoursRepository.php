<?php

namespace App\Repositories;

use App\Models\Parcours;

class ParcoursRepository extends BaseRepository
{
    public function __construct(Parcours $model)
    {
        parent::__construct($model);
    }
}




