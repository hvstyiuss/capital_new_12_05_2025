<?php

namespace App\Repositories;

use App\Models\JoursFerie;

class JoursFerieRepository extends BaseRepository
{
    public function __construct(JoursFerie $model)
    {
        parent::__construct($model);
    }
}




