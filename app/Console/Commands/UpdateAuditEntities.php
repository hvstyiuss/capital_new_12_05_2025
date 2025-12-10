<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entite;
use Illuminate\Support\Facades\DB;

class UpdateAuditEntities extends Command
{
    protected $signature = 'update:audit-entities';
    protected $description = 'Update Audit Direction entities with correct entity_type and parent_id';

    public function handle()
    {
        $this->info("=== Updating Audit Direction Entities ===\n");

        // Find Direction
        $direction = Entite::where('name', 'like', "%AUDIT INTERNE ET DES RISQUES%")->first();

        if (!$direction) {
            $this->error("✗ Direction not found!");
            return 1;
        }

        $this->info("✓ Found Direction: {$direction->name} (ID: {$direction->id})");

        // Update Direction
        $direction->entity_type = 'direction';
        $direction->save();
        $this->info("  → Updated entity_type to 'direction'");

        // Define the structure
        $structure = [
            'DEPARTEMENT AUDIT ET EVALUATION' => [
                'entity_type' => 'departement',
                'services' => [
                    "Service de l'audit",
                    "Service d'évaluation",
                ],
            ],
            'DEPARTEMENT DES RISQUES' => [
                'entity_type' => 'departement',
                'services' => [
                    'Service gestion des risques',
                    "Service organisation et méthodes",
                ],
            ],
            'DEPARTEMENT COORDINATION ET REQUETES' => [
                'entity_type' => 'departement',
                'services' => [
                    'Service de coordination avec les structures déconcentrées',
                    "Service des requetes et des relations avec le Médiateur",
                ],
            ],
        ];

        // Update Departments and Services
        foreach ($structure as $deptName => $deptData) {
            $dept = Entite::where('name', 'like', "%{$deptName}%")
                ->where('parent_id', $direction->id)
                ->first();

            if (!$dept) {
                // Try to find without parent_id constraint
                $dept = Entite::where('name', 'like', "%{$deptName}%")->first();
            }

            if ($dept) {
                $this->info("\n✓ Found Department: {$dept->name} (ID: {$dept->id})");
                
                // Update department
                $dept->entity_type = 'departement';
                $dept->parent_id = $direction->id;
                $dept->save();
                $this->info("  → Updated entity_type to 'departement'");
                $this->info("  → Updated parent_id to {$direction->id}");

                // Update Services
                foreach ($deptData['services'] as $serviceName) {
                    $service = Entite::where('name', 'like', "%{$serviceName}%")
                        ->where('parent_id', $dept->id)
                        ->first();

                    if (!$service) {
                        // Try to find without parent_id constraint
                        $service = Entite::where('name', 'like', "%{$serviceName}%")->first();
                    }

                    if ($service) {
                        $this->info("  ✓ Found Service: {$service->name} (ID: {$service->id})");
                        $service->entity_type = 'service';
                        $service->parent_id = $dept->id;
                        $service->save();
                        $this->info("    → Updated entity_type to 'service'");
                        $this->info("    → Updated parent_id to {$dept->id}");
                    } else {
                        $this->warn("  ✗ Service not found: {$serviceName}");
                        $this->info("    Creating new service...");
                        
                        // Create the missing service
                        $newService = Entite::create([
                            'name' => $serviceName,
                            'entity_type' => 'service',
                            'parent_id' => $dept->id,
                            'date_debut' => now(),
                        ]);
                        $this->info("    ✓ Created Service: {$newService->name} (ID: {$newService->id})");
                    }
                }
            } else {
                $this->error("✗ Department not found: {$deptName}");
            }
        }

        // Verify the structure
        $this->info("\n=== Verification ===");
        $direction = Entite::find($direction->id);
        $this->info("Direction: {$direction->name}");
        $this->info("  - entity_type: " . ($direction->entity_type ?? 'NULL'));
        $this->info("  - parent_id: " . ($direction->parent_id ?? 'NULL'));

        $departements = Entite::where('parent_id', $direction->id)->get();
        $this->info("\nDepartements ({$departements->count()}):");
        foreach ($departements as $dept) {
            $this->info("  - {$dept->name} (ID: {$dept->id})");
            $this->info("    entity_type: " . ($dept->entity_type ?? 'NULL'));
            $this->info("    parent_id: {$dept->parent_id}");
            
            $services = Entite::where('parent_id', $dept->id)->get();
            $this->info("    Services ({$services->count()}):");
            foreach ($services as $service) {
                $this->info("      • {$service->name} (ID: {$service->id})");
                $this->info("        entity_type: " . ($service->entity_type ?? 'NULL'));
                $this->info("        parent_id: {$service->parent_id}");
            }
        }

        $this->info("\n✓ Update complete!");
        return 0;
    }
}






