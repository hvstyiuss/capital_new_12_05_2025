<?php

namespace App\Services;

class DataTableService
{
    /**
     * Prepare table rows for data-table component
     */
    public static function prepareTableRows($items, callable $rowMapper): array
    {
        $rows = [];
        foreach ($items as $item) {
            $rows[] = $rowMapper($item);
        }
        return $rows;
    }

    /**
     * Generate status badge HTML
     */
    public static function getStatusBadge($deletedAt, string $activeClass = 'bg-success', string $deletedClass = 'bg-danger'): string
    {
        return $deletedAt
            ? '<span class="badge ' . $deletedClass . '">Supprimée</span>'
            : '<span class="badge ' . $activeClass . '">Active</span>';
    }

    /**
     * Generate action buttons HTML for edit and delete
     */
    public static function getActionButtons($item, string $editRoute, string $destroyRoute, string $editTitle = 'Modifier', string $deleteTitle = 'Supprimer', string $deleteConfirmMessage = null): string
    {
        $deleteConfirm = $deleteConfirmMessage ?? 'Êtes-vous sûr de vouloir supprimer cet élément ?';
        
        return '<div class="d-flex gap-2">'
            . '<a href="' . e(route($editRoute, $item)) . '" class="btn btn-sm btn-warning" title="' . e($editTitle) . '">'
            . '<i class="fas fa-edit"></i>'
            . '</a>'
            . '<form action="' . e(route($destroyRoute, $item)) . '" method="POST" class="d-inline" onsubmit="return confirm(\'' . e($deleteConfirm) . '\')">'
            . csrf_field() . method_field('DELETE')
            . '<button type="submit" class="btn btn-sm btn-danger" title="' . e($deleteTitle) . '">'
            . '<i class="fas fa-trash"></i>'
            . '</button>'
            . '</form>'
            . '</div>';
    }

    /**
     * Prepare localisations table data
     */
    public static function prepareLocalisationsTable($localisations): array
    {
        $headers = ['ID', 'Code', 'DRANEF', 'Entité', 'Statut', 'Créé le', 'Actions'];
        $rows = [];
        
        foreach ($localisations as $localisation) {
            $statusBadge = self::getStatusBadge($localisation->deleted_at);
            $actionsHtml = self::getActionButtons(
                $localisation,
                'settings.localisations.edit',
                'settings.localisations.destroy',
                'Modifier',
                'Supprimer',
                'Êtes-vous sûr de vouloir supprimer cette localisation ?'
            );
            
            $rows[] = [
                '<span class="badge bg-secondary">' . e($localisation->id) . '</span>',
                e($localisation->CODE),
                e($localisation->DRANEF),
                e($localisation->ENTITE),
                $statusBadge,
                '<small class="text-muted">' . e($localisation->created_at?->format('d/m/Y H:i') ?? 'N/A') . '</small>',
                $actionsHtml,
            ];
        }
        
        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * Prepare forets table data
     */
    public static function prepareForetsTable($forets): array
    {
        $headers = ['ID', 'Nom de la Forêt', 'Statut', 'Créé le', 'Actions'];
        $rows = [];
        
        foreach ($forets as $foret) {
            $statusBadge = self::getStatusBadge($foret->deleted_at);
            $actionsHtml = self::getActionButtons(
                $foret,
                'settings.forets.edit',
                'settings.forets.destroy',
                'Modifier',
                'Supprimer',
                'Êtes-vous sûr de vouloir supprimer cette forêt ?'
            );
            
            $rows[] = [
                '<span class="badge bg-secondary">' . e($foret->id) . '</span>',
                e($foret->foret),
                $statusBadge,
                '<small class="text-muted">' . e($foret->created_at?->format('d/m/Y H:i') ?? 'N/A') . '</small>',
                $actionsHtml,
            ];
        }
        
        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * Prepare essences table data
     */
    public static function prepareEssencesTable($essences): array
    {
        $headers = ['ID', "Nom de l'Essence", 'Statut', 'Date de Création', 'Actions'];
        $rows = [];
        
        foreach ($essences as $essence) {
            $statusBadge = $essence->deleted_at
                ? '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Supprimée</span>'
                : '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Active</span>';
            
            $nameCell = '<div class="flex items-center gap-3">'
                . '<div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">'
                . '<i class="fas fa-leaf text-green-600 text-sm"></i>'
                . '</div>'
                . '<span class="font-medium">' . e($essence->essence) . '</span>'
                . '</div>';
            
            $actionsHtml = '<div class="flex items-center gap-2">'
                . '<a href="' . e(route('settings.essences.edit', $essence)) . '" class="inline-flex items-center gap-1 px-3 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 transition-all duration-300 transform hover:scale-105 shadow-sm" title="Modifier">'
                . '<i class="fas fa-edit text-sm"></i>'
                . '</a>'
                . '<form action="' . e(route('settings.essences.destroy', $essence)) . '" method="POST" class="inline" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer cette essence ?\')">'
                . csrf_field() . method_field('DELETE')
                . '<button type="submit" class="inline-flex items-center gap-1 px-3 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg hover:from-red-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105 shadow-sm" title="Supprimer">'
                . '<i class="fas fa-trash text-sm"></i>'
                . '</button>'
                . '</form>'
                . '</div>';
            
            $rows[] = [
                '<span class="badge bg-secondary">' . e($essence->id) . '</span>',
                $nameCell,
                $statusBadge,
                '<small class="text-muted">' . e($essence->created_at?->format('d/m/Y') ?? 'N/A') . '</small>',
                $actionsHtml,
            ];
        }
        
        return ['headers' => $headers, 'rows' => $rows];
    }
}

