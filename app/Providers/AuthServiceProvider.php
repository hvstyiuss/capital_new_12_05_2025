<?php

namespace App\Providers;

use App\Models\Annonce;
use App\Models\Avis;
use App\Models\AvisDepart;
use App\Models\AvisRetour;
use App\Models\Conge;
use App\Models\Demande;
use App\Models\Entite;
use App\Models\JoursFerie;
use App\Models\Mutation;
use App\Models\NoteAnnuelle;
use App\Models\Parcours;
use App\Models\Suggestion;
use App\Models\User;
use App\Policies\AnnoncePolicy;
use App\Policies\AvisDepartPolicy;
use App\Policies\AvisPolicy;
use App\Policies\AvisRetourPolicy;
use App\Policies\CongePolicy;
use App\Policies\DemandePolicy;
use App\Policies\EntitePolicy;
use App\Policies\JoursFeriePolicy;
use App\Policies\MutationPolicy;
use App\Policies\NoteAnnuellePolicy;
use App\Policies\ParcoursPolicy;
use App\Policies\SuggestionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Annonce::class      => AnnoncePolicy::class,
        Avis::class          => AvisPolicy::class,
        AvisDepart::class    => AvisDepartPolicy::class,
        AvisRetour::class    => AvisRetourPolicy::class,
        Conge::class         => CongePolicy::class,
        Demande::class       => DemandePolicy::class,
        Entite::class        => EntitePolicy::class,
        JoursFerie::class    => JoursFeriePolicy::class,
        Mutation::class      => MutationPolicy::class,
        NoteAnnuelle::class  => NoteAnnuellePolicy::class,
        Parcours::class      => ParcoursPolicy::class,
        Suggestion::class     => SuggestionPolicy::class,
        User::class          => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}


