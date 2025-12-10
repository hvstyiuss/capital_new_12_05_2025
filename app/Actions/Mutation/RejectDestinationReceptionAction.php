<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use App\Models\User;
use App\Services\MutationService;
use App\Services\NotificationService;
use DomainException;

class RejectDestinationReceptionAction
{
    protected MutationService $mutationService;
    protected NotificationService $notificationService;

    public function __construct(MutationService $mutationService, NotificationService $notificationService)
    {
        $this->mutationService = $mutationService;
        $this->notificationService = $notificationService;
    }

    public function execute(Mutation $mutation, User $rejector, \App\DTOs\Mutation\RejectDestinationReceptionDTO $dto): Mutation
    {
        if ($mutation->approved_by_super_collaborateur_rh) {
            throw new DomainException('Cette mutation a déjà été approuvée.');
        }

        if ($mutation->rejected_by_super_rh) {
            throw new DomainException('Cette mutation a déjà été rejetée.');
        }

        // Reject destination reception
        $mutation = $this->mutationService->rejectDestinationReception($mutation, $rejector, $dto->rejectionReasonSuperRh);

        // Send rejection notification
        $this->sendRejectionNotification($mutation);

        return $mutation->load(['user', 'toEntite']);
    }

    protected function sendRejectionNotification(Mutation $mutation): void
    {
        $mutationUser = $mutation->user;
        
        if ($mutationUser) {
            $this->notificationService->sendToUser(
                $mutationUser,
                'mutation_rejected',
                'Mutation rejetée',
                'Votre demande de mutation a été rejetée par le super Collaborateur RH. Raison: ' . $mutation->rejection_reason_super_rh,
                ['mutation_id' => $mutation->id],
                [
                    'action_url' => route('mutations.tracking'),
                    'icon' => 'fa-times-circle',
                    'color' => 'danger',
                    'priority' => 'high'
                ]
            );
        }
    }
}


