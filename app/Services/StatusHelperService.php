<?php

namespace App\Services;

class StatusHelperService
{
    /**
     * Get border class based on status
     */
    public static function getBorderClass(?string $statut): string
    {
        $statut = strtolower($statut ?? '');
        return match(true) {
            in_array($statut, ['rejeté', 'rejected']) => 'border-left-rejected',
            in_array($statut, ['approuvé', 'approved', 'validé']) => 'border-left-approved',
            default => 'border-left-pending',
        };
    }

    /**
     * Get badge class based on status
     */
    public static function getBadgeClass(?string $statut): string
    {
        return match($statut) {
            'approved' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'rejected' => 'bg-danger',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    /**
     * Get status map (French labels)
     */
    public static function getStatutMap(): array
    {
        return [
            'pending' => 'En attente',
            'approved' => 'Validé',
            'rejected' => 'Rejeté',
            'cancelled' => 'Annulé',
        ];
    }

    /**
     * Get status colors for badges
     */
    public static function getStatutColors(): array
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
        ];
    }

    /**
     * Get status icons
     */
    public static function getStatutIcons(): array
    {
        return [
            'pending' => 'fa-clock',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'cancelled' => 'fa-ban',
        ];
    }
}

