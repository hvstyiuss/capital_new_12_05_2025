<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Catalogue des permissions disponibles
    |--------------------------------------------------------------------------
    |
    | Cette liste définit les permissions "officielles" qui peuvent être
    | créées via l'interface. L'utilisateur choisit dans cette liste au lieu
    | de saisir un texte libre.
    |
    | Chaque entrée contient :
    | - name  : le nom technique de la permission (clé Spatie)
    | - label : une description lisible par l'humain
    | - group : une catégorie fonctionnelle (affichage côté UI)
    |
    */

    'permissions' => [
        // Utilisateurs
        [
            'name'  => 'users.view',
            'label' => 'Voir les utilisateurs',
            'group' => 'Utilisateurs',
        ],
        [
            'name'  => 'users.create',
            'label' => 'Créer des utilisateurs',
            'group' => 'Utilisateurs',
        ],
        [
            'name'  => 'users.edit',
            'label' => 'Modifier des utilisateurs',
            'group' => 'Utilisateurs',
        ],
        [
            'name'  => 'users.delete',
            'label' => 'Supprimer des utilisateurs',
            'group' => 'Utilisateurs',
        ],

        // Mutations
        [
            'name'  => 'mutations.view',
            'label' => 'Consulter les demandes de mutation',
            'group' => 'Mutations',
        ],
        [
            'name'  => 'mutations.create',
            'label' => 'Créer une demande de mutation',
            'group' => 'Mutations',
        ],
        [
            'name'  => 'mutations.approve',
            'label' => 'Approuver les demandes de mutation',
            'group' => 'Mutations',
        ],
        [
            'name'  => 'mutations.reject',
            'label' => 'Rejeter les demandes de mutation',
            'group' => 'Mutations',
        ],

        // Congés
        [
            'name'  => 'conges.view',
            'label' => 'Consulter les demandes de congé',
            'group' => 'Congés',
        ],
        [
            'name'  => 'conges.create',
            'label' => 'Créer des demandes de congé',
            'group' => 'Congés',
        ],
        [
            'name'  => 'conges.approve',
            'label' => 'Approuver les demandes de congé',
            'group' => 'Congés',
        ],

        // Annonces
        [
            'name'  => 'annonces.view',
            'label' => 'Voir les annonces',
            'group' => 'Annonces',
        ],
        [
            'name'  => 'annonces.create',
            'label' => 'Créer des annonces',
            'group' => 'Annonces',
        ],
        [
            'name'  => 'annonces.edit',
            'label' => 'Modifier des annonces',
            'group' => 'Annonces',
        ],

        // Paramètres
        [
            'name'  => 'settings.access',
            'label' => 'Accéder aux paramètres',
            'group' => 'Paramètres',
        ],
        [
            'name'  => 'settings.security',
            'label' => 'Gérer la sécurité et les rôles',
            'group' => 'Paramètres',
        ],
    ],
];












