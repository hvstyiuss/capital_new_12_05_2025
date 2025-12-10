<?php

namespace App\Actions\Annonce;

use App\Models\Annonce;
use App\Models\Entite;
use App\Models\User;
use App\Models\TypeAnnonce;

class PrepareEditFormAction
{
    /**
     * Prepare data needed for the annonce edit form.
     *
     * @return array{annonce: Annonce, entites: \Illuminate\Database\Eloquent\Collection, users: \Illuminate\Database\Eloquent\Collection, typesAnnonces: \Illuminate\Database\Eloquent\Collection}
     */
    public function execute(Annonce $annonce): array
    {
        $annonce->load('entites');

        return [
            'annonce'       => $annonce,
            'entites'       => Entite::all(),
            'users'         => User::where('is_active', true)->where('is_deleted', false)->get(),
            'typesAnnonces' => TypeAnnonce::where('is_active', true)->orderBy('nom')->get(),
        ];
    }
}




