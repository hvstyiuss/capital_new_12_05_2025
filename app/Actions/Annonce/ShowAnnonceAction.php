<?php

namespace App\Actions\Annonce;

use App\Models\Annonce;

class ShowAnnonceAction
{
    public function execute(Annonce $annonce): Annonce
    {
        return $annonce->load(['user', 'typeAnnonce', 'entites']);
    }
}




