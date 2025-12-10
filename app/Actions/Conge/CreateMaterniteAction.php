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
use App\DTOs\Conge\CreateMaterniteDTO;
use App\Services\NotificationService;
use Carbon\Carbon;
use DomainException;

class CreateMaterniteAction
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a maternity leave request.
     */
    public function execute(CreateMaterniteDTO $dto): array
    {
        // Validate duration (maternity leave is fixed at 98 days)
        if ($dto->nbrJoursDemandes > 98) {
            throw new DomainException("Le congé maternité est limité à 98 jours (14 semaines).");
        }

        // Get user's current entity
        $parcours = Parcours::where('ppr', $dto->ppr)
            ->where(function($query) {
                $query->whereNull('date_fin')
                      ->orWhere('date_fin', '>=', now());
            })
            ->first();
        
        $entiteId = $parcours ? $parcours->entite_id : null;

        // Get type_conge for 'maladie' (maternite uses the same type)
        $typeConge = TypeConge::where('name', 'maladie')->first();
        if (!$typeConge) {
            throw new DomainException('Type de congé maladie non trouvé. Veuillez contacter l\'administrateur.');
        }

        // Get type_maladie for 'm' (maternite)
        $typeMaladie = TypeMaladie::where('name', 'm')->first();
        if (!$typeMaladie) {
            throw new DomainException('Type de congé maternité non trouvé. Veuillez contacter l\'administrateur.');
        }

        // Calculate return date if not provided (98 days after departure)
        $dateRetour = $dto->dateRetour;
        if (!$dateRetour) {
            $dateRetour = Carbon::parse($dto->dateDepart)->addDays(98)->format('Y-m-d');
        }

        // Create Demande (auto-approved for maternity leave)
        $demande = Demande::create([
            'ppr' => $dto->ppr,
            'type' => 'conge',
            'entite_id' => $entiteId,
            'created_by' => $dto->ppr,
            'date_debut' => $dto->dateDepart,
            'statut' => 'approved', // Auto-approved for maternity leave
        ]);

        // Create DemandeConge
        $demandeConge = DemandeConge::create([
            'demande_id' => $demande->id,
            'type_conge_id' => $typeConge->id,
            'date_debut' => $dto->dateDepart,
            'date_fin' => $dateRetour,
            'nbr_jours_demandes' => $dto->nbrJoursDemandes,
            'motif' => $dto->observation,
        ]);

        // Create CongeMaladie (maternite uses the same table)
        $congeMaladie = CongeMaladie::create([
            'demande_conge_id' => $demandeConge->id,
            'type_maladie_id' => $typeMaladie->id,
            'date_declaration' => $dto->dateDeclaration,
            'date_constatation' => null,
            'date_reprise_travail' => $dateRetour,
            'nbr_jours_arret' => $dto->nbrJoursDemandes,
            'nbr_jours_total' => $dto->nbrJoursDemandes,
            'reference_arret' => null,
            'observation' => $dto->observation,
        ]);

        // Create Avis (automatically approved for maternity leave)
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
            'date_retour' => $dateRetour,
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



