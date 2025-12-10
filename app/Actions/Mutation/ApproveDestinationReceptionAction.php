<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use App\Models\User;
use App\Models\Parcours;
use App\Services\MutationService;
use App\Services\NotificationService;
use DomainException;
use Illuminate\Http\Response;

class ApproveDestinationReceptionAction
{
    protected MutationService $mutationService;
    protected NotificationService $notificationService;

    public function __construct(MutationService $mutationService, NotificationService $notificationService)
    {
        $this->mutationService = $mutationService;
        $this->notificationService = $notificationService;
    }

    public function execute(Mutation $mutation, User $approver, \App\DTOs\Mutation\ApproveDestinationReceptionDTO $dto): Mutation
    {
        if ($mutation->approved_by_super_collaborateur_rh) {
            throw new DomainException('Cette mutation a déjà été approuvée.');
        }

        if ($mutation->rejected_by_super_rh) {
            throw new DomainException('Cette mutation a déjà été rejetée.');
        }

        // Approve destination reception
        $mutation = $this->mutationService->approveDestinationReception($mutation, $approver, $dto->dateDebutAffectation);

        // If final validation, update parcours and send notification
        if ($mutation->approved_by_super_collaborateur_rh) {
            $userParcours = Parcours::where('ppr', $mutation->ppr)
                ->where(function($query) {
                    $query->whereNull('date_fin')
                        ->orWhere('date_fin', '>=', now());
                })
                ->orderBy('date_debut', 'desc')
                ->first();

            if ($userParcours) {
                $this->mutationService->updateParcoursForSuperRhValidation($mutation, $userParcours);
            }

            $this->sendFinalApprovalNotification($mutation);
        } else {
            // Initial review: send to destination
            $this->sendDestinationNotification($mutation);
        }

        return $mutation->load(['user', 'toEntite', 'approvedBySuperCollaborateurRh']);
    }

    protected function sendFinalApprovalNotification(Mutation $mutation): void
    {
        $mutationUser = $mutation->user;
        
        if ($mutationUser) {
            $this->notificationService->sendToUser(
                $mutationUser,
                'mutation_final_approved',
                'Mutation validée avec succès',
                'Votre demande de mutation a été validée avec succès. La date de début d\'affectation a été définie.',
                ['mutation_id' => $mutation->id],
                [
                    'action_url' => route('mutations.tracking'),
                    'icon' => 'fa-check-circle',
                    'color' => 'success',
                    'priority' => 'high'
                ]
            );
        }
    }

    protected function sendDestinationNotification(Mutation $mutation): void
    {
        $destinationEntite = $mutation->toEntite;
        if (!$destinationEntite || !$destinationEntite->chef_ppr) {
            return;
        }

        $destinationChef = User::where('ppr', $destinationEntite->chef_ppr)->first();
        if ($destinationChef) {
            $this->notificationService->sendToUser(
                $destinationChef,
                'mutation_pending_destination',
                'Demande de mutation à examiner',
                'Une nouvelle demande de mutation externe nécessite votre examen.',
                ['mutation_id' => $mutation->id],
                [
                    'action_url' => route('mutations.agent-requests'),
                    'icon' => 'fa-exclamation-circle',
                    'color' => 'warning',
                    'priority' => 'high'
                ]
            );
        }
    }
}




