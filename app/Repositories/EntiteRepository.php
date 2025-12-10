<?php

namespace App\Repositories;

use App\Models\Entite;

class EntiteRepository extends BaseRepository
{
    public function __construct(Entite $model)
    {
        parent::__construct($model);
    }
}





