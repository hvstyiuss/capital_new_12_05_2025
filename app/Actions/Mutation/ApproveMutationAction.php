<?php

namespace App\Actions\Mutation;

use App\Models\Mutation;
use App\Models\User;
use App\Services\MutationService;
use App\Services\NotificationService;
use DomainException;
use Illuminate\Support\Facades\Route;

class ApproveMutationAction
{
    protected MutationService $mutationService;
    protected NotificationService $notificationService;

    public function __construct(MutationService $mutationService, NotificationService $notificationService)
    {
        $this->mutationService = $mutationService;
        $this->notificationService = $notificationService;
    }

    public function execute(Mutation $mutation, User $approver, \App\DTOs\Mutation\ApproveMutationDTO $dto): Mutation
    {
        // Check authorization
        $authorization = $this->mutationService->canApproveMutation($mutation, $approver, $dto->approvalType);
        if (!$authorization['can_approve']) {
            throw new DomainException($authorization['error']);
        }

        // Approve the mutation
        $mutation = $this->mutationService->approveMutation($mutation, $approver, $dto->approvalType);

        // Send notifications
        $this->sendApprovalNotifications($mutation, $dto->approvalType);

        return $mutation->load(['user', 'toEntite', 'approvedByCurrentDirection', 'approvedByDestinationDirection']);
    }

    protected function sendApprovalNotifications(Mutation $mutation, string $approvalType): void
    {
        $mutationUser = $mutation->user;
        
        if (!$mutationUser) {
            return;
        }

        if ($approvalType === 'current') {
            $this->notificationService->sendToUser(
                $mutationUser,
                'mutation_approved',
                'Mutation approuvée par la direction actuelle',
                'Votre demande de mutation a été approuvée par la direction actuelle.',
                ['mutation_id' => $mutation->id],
                [
                    'action_url' => route('mutations.tracking'),
                    'icon' => 'fa-check-circle',
                    'color' => 'success',
                    'priority' => 'high'
                ]
            );
        } elseif ($approvalType === 'destination') {
            $this->notificationService->sendToUser(
                $mutationUser,
                'mutation_approved',
                'Mutation approuvée par la direction de destination',
                'Votre demande de mutation a été approuvée par la direction de destination.',
                ['mutation_id' => $mutation->id],
                [
                    'action_url' => route('mutations.tracking'),
                    'icon' => 'fa-check-circle',
                    'color' => 'success',
                    'priority' => 'high'
                ]
            );
        }

        // Notify super Collaborateur Rh if ready
        if ($mutation->mutation_type === 'interne' && $mutation->approved_by_current_direction) {
            $this->notifySuperRh($mutation, 'Une nouvelle demande de mutation interne nécessite votre validation finale.');
        } elseif ($mutation->mutation_type === 'externe' && 
                  $approvalType === 'current' && 
                  $mutation->approved_by_current_direction && 
                  !$mutation->sent_to_destination_by_super_rh) {
            $this->notifySuperRh($mutation, 'Une nouvelle demande de mutation externe nécessite votre examen initial.');
        } elseif ($mutation->mutation_type === 'externe' && 
                  $approvalType === 'destination' && 
                  $mutation->approved_by_current_direction && 
                  $mutation->approved_by_destination_direction &&
                  $mutation->sent_to_destination_by_super_rh) {
            $this->notifySuperRh($mutation, 'Une demande de mutation externe est prête pour validation finale.');
        }
    }

    protected function notifySuperRh(Mutation $mutation, string $message): void
    {
        $superRhUsers = User::role('super Collaborateur Rh')->get();
        
        foreach ($superRhUsers as $superRh) {
            $this->notificationService->sendToUser(
                $superRh,
                'mutation_pending_super_rh',
                'Demande de mutation en attente',
                $message,
                ['mutation_id' => $mutation->id],
                [
                    'action_url' => route('mutations.super-rh.requests'),
                    'icon' => 'fa-exclamation-circle',
                    'color' => 'warning',
                    'priority' => 'high'
                ]
            );
        }
    }
}

