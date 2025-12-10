<?php

namespace App\Actions\Annonce;

use App\Models\Entite;
use App\Models\User;
use App\Models\TypeAnnonce;

class PrepareCreateFormAction
{
    /**
     * Prepare data needed for the annonce creation form.
     *
     * @return array{entites: \Illuminate\Database\Eloquent\Collection, users: \Illuminate\Database\Eloquent\Collection, typesAnnonces: \Illuminate\Database\Eloquent\Collection}
     */
    public function execute(): array
    {
        return [
            'entites'       => Entite::all(),
            'users'         => User::where('is_active', true)->where('is_deleted', false)->get(),
            'typesAnnonces' => TypeAnnonce::where('is_active', true)->orderBy('nom')->get(),
        ];
    }
}




