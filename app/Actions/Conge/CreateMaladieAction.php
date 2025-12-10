<?php

namespace App\Actions\Conge;

use App\Models\Demande;
use App\Models\DemandeConge;
use App\Models\CongeMaladie;
use App\Models\Avis;
use App\Models\AvisDepart;
use App\Models\TypeConge;
use App\Models\TypeMaladie;
use App\Models\Parcours;
use App\DTOs\Conge\CreateMaladieDTO;
use App\Services\NotificationService;
use Carbon\Carbon;
use DomainException;

class CreateMaladieAction
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a sick leave request.
     */
    public function execute(CreateMaladieDTO $dto): array
    {
        // Get type maladie to validate max duration
        $typeMaladie = TypeMaladie::findOrFail($dto->typeMaladieId);
        $maxDuration = $typeMaladie->max_duration_days;
        
        if ($maxDuration && $dto->nbrJoursDemandes > $maxDuration) {
            throw new DomainException("La durée maximale pour {$typeMaladie->display_name} est de {$maxDuration} jours.");
        }

        // Get user's current entity
        $parcours = Parcours::where('ppr', $dto->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->first();
        
        $entiteId = $parcours ? $parcours->entite_id : null;

        // Get type_conge for 'maladie'
        $typeConge = TypeConge::where('name', 'maladie')->first();
        if (!$typeConge) {
            throw new DomainException('Type de congé maladie non trouvé. Veuillez contacter l\'administrateur.');
        }

        // Create Demande (auto-approved for sick leave)
        $demande = Demande::create([
            'ppr' => $dto->ppr,
            'type' => 'conge',
            'entite_id' => $entiteId,
            'created_by' => $dto->ppr,
            'date_debut' => $dto->dateDepart,
            'statut' => 'approved', // Auto-approved for sick leave
        ]);

        // Create DemandeConge
        $demandeConge = DemandeConge::create([
            'demande_id' => $demande->id,
            'type_conge_id' => $typeConge->id,
            'date_debut' => $dto->dateDepart,
            'date_fin' => $dto->dateRetour,
            'nbr_jours_demandes' => $dto->nbrJoursDemandes,
            'motif' => $dto->observation,
        ]);

        // Create CongeMaladie
        $congeMaladie = CongeMaladie::create([
            'demande_conge_id' => $demandeConge->id,
            'type_maladie_id' => $dto->typeMaladieId,
            'date_declaration' => $dto->dateDeclaration,
            'date_constatation' => $dto->dateConstatation,
            'date_reprise_travail' => $dto->dateRetour,
            'nbr_jours_arret' => $dto->nbrJoursDemandes,
            'nbr_jours_total' => $dto->nbrJoursDemandes,
            'reference_arret' => $dto->referenceArret,
            'observation' => $dto->observation,
        ]);

        // Create Avis (automatically approved for sick leave)
        $avis = Avis::create([
            'demande_id' => $demande->id,
            'date_depot' => Carbon::now(),
            'is_validated' => true, // Auto-validated
        ]);

        // Create AvisDepart (automatically approved)
        $avisDepart = AvisDepart::create([
            'avis_id' => $avis->id,
            'nb_jours_demandes' => $dto->nbrJoursDemandes,
            'date_depart' => $dto->dateDepart,
            'date_retour' => $dto->dateRetour,
            'statut' => 'approved', // Auto-approved, no chef approval needed
        ]);

        return [
            'demande' => $demande,
            'demandeConge' => $demandeConge,
            'congeMaladie' => $congeMaladie,
            'typeMaladie' => $typeMaladie,
        ];
    }
}



