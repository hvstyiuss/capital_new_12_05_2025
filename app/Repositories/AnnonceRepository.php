<?php

namespace App\Repositories;

use App\Models\Annonce;

class AnnonceRepository extends BaseRepository
{
    public function __construct(Annonce $model)
    {
        parent::__construct($model);
    }
}





