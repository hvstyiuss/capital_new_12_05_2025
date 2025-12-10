<?php

namespace App\Helpers;

class EntityHelper
{
    /**
     * Get the list of special entity names that can manage mutations.
     * These entities have special privileges for mutation management.
     */
    public static function getSpecialEntityNames(): array
    {
        return [
            'Département de la Coopération et de la Communication',
            'Département des Affaires Juridiques',
            'Centre Innovation, Recherche et Formation',
            'Centre Innovation',
            'Recherche et Innovation',
        ];
    }
}




