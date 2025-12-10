<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entite;
use App\Models\EntiteInfo;
use App\Models\Parcours;
use App\Models\User;

class CheckAuditEntities extends Command
{
    protected $signature = 'check:audit-entities';
    protected $description = 'Check if Audit Direction entities exist in database';

    public function handle()
    {
        $this->info("=== Checking Audit Direction Entities ===\n");

        // Find Direction
        $direction = Entite::where('name', 'like', "%AUDIT INTERNE ET DES RISQUES%")->first();

        if (!$direction) {
            $this->error("✗ DIRECTION NOT FOUND");
            $this->info("Searching for: DIRECTION DE L'AUDIT INTERNE ET DES RISQUES");
            return;
        }

        $this->info("✓ Direction Found: {$direction->name} (ID: {$direction->id})");
        $this->line("  - entity_type: " . ($direction->entity_type ?? 'NULL'));
        $this->line("  - parent_id: " . ($direction->parent_id ?? 'NULL'));

        // Check entite_info
        $info = $direction->entiteInfo;
        if ($info) {
            $this->line("  - entite_info exists: Yes");
            $this->line("  - entite_info.type: " . ($info->type ?? 'NULL'));
        } else {
            $this->warn("  - entite_info exists: No");
        }

        // Check Departements
        $this->info("\n=== Checking DÉPARTEMENTS ===");
        $departements = Entite::where('parent_id', $direction->id)->get();

        $this->info("Found {$departements->count()} département(s)");

        foreach ($departements as $dept) {
            $this->line("\n  Département: {$dept->name} (ID: {$dept->id})");
            $this->line("    - entity_type: " . ($dept->entity_type ?? 'NULL'));
            $this->line("    - parent_id: {$dept->parent_id}");

            // Check Services
            $services = Entite::where('parent_id', $dept->id)->get();
            $this->line("    - Services count: {$services->count()}");

            foreach ($services as $service) {
                $this->line("      • {$service->name} (ID: {$service->id})");
                $this->line("        - entity_type: " . ($service->entity_type ?? 'NULL'));
                $this->line("        - parent_id: {$service->parent_id}");
            }
        }

        // Summary
        $this->info("\n=== SUMMARY ===");
        $this->line("Direction ID: {$direction->id}");
        $this->line("Number of Départements: {$departements->count()}");

        $totalServices = 0;
        foreach ($departements as $dept) {
            $totalServices += Entite::where('parent_id', $dept->id)->count();
        }
        $this->line("Total Services: {$totalServices}");

        // Check entity_type distribution
        $this->info("\nEntity Type Distribution:");
        $this->line("  Direction entity_type: " . ($direction->entity_type ?? 'NULL'));

        $deptTypes = Entite::where('parent_id', $direction->id)
            ->selectRaw('entity_type, count(*) as count')
            ->groupBy('entity_type')
            ->get();
        $this->line("  Départements:");
        foreach ($deptTypes as $dt) {
            $this->line("    - " . ($dt->entity_type ?? 'NULL') . ": {$dt->count}");
        }

        $deptIds = $departements->pluck('id');
        if ($deptIds->isNotEmpty()) {
            $serviceTypes = Entite::whereIn('parent_id', $deptIds)
                ->selectRaw('entity_type, count(*) as count')
                ->groupBy('entity_type')
                ->get();
            $this->line("  Services:");
            foreach ($serviceTypes as $st) {
                $this->line("    - " . ($st->entity_type ?? 'NULL') . ": {$st->count}");
            }
        }

        // Check specific entities from image
        $this->info("\n=== Checking Specific Entities from Image ===");
        
        $expectedEntities = [
            'DIRECTION DE L\'AUDIT INTERNE ET DES RISQUES' => 'direction',
            'DEPARTEMENT AUDIT ET EVALUATION' => 'departement',
            'DEPARTEMENT DES RISQUES' => 'departement',
            'DEPARTEMENT COORDINATION ET REQUETES' => 'departement',
            'Service de l\'audit' => 'service',
            'Service d\'évaluation' => 'service',
            'Service gestion des risques' => 'service',
            'Service organisation et méthodes' => 'service',
            'Service de coordination avec les structures déconcentrées' => 'service',
            'Service des requetes et des relations avec le Médiateur' => 'service',
        ];

        foreach ($expectedEntities as $name => $expectedType) {
            $entity = Entite::where('name', 'like', "%{$name}%")->first();
            if ($entity) {
                $actualType = $entity->entity_type ?? 'NULL';
                $match = ($actualType === $expectedType) ? '✓' : '✗';
                $this->line("{$match} {$name}");
                $this->line("    Found: Yes | entity_type: {$actualType} (expected: {$expectedType})");
                $this->line("    parent_id: " . ($entity->parent_id ?? 'NULL'));
            } else {
                $this->error("✗ {$name}");
                $this->line("    Found: No");
            }
        }

        $this->info("\nDone!");
    }
}






