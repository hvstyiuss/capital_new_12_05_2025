<?php

namespace App\Repositories;

use App\Models\Suggestion;

class SuggestionRepository extends BaseRepository
{
    public function __construct(Suggestion $model)
    {
        parent::__construct($model);
    }
}




