<?php

namespace App\Repositories;

use App\Models\NoteAnnuelle;

class NoteAnnuelleRepository extends BaseRepository
{
    public function __construct(NoteAnnuelle $model)
    {
        parent::__construct($model);
    }
}




