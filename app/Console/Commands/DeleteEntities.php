<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entite;

class DeleteEntities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entities:delete {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete specific entities: Direction de Parc National, Direction des Ressources Humaines, and Direction Provincial';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $entitiesToDelete = [
            'Direction de Parc National',
            'Direction des Ressources Humaines'
        ];

        $this->info('Recherche des entités à supprimer...');
        $this->newLine();

        $deletedCount = 0;
        $notFoundCount = 0;

        foreach ($entitiesToDelete as $entityName) {
            $entite = Entite::where('name', $entityName)->first();
            
            if ($entite) {
                // Check if entity has children
                $childrenCount = $entite->children()->count();
                
                if ($childrenCount > 0) {
                    $this->warn("⚠️  L'entité '{$entityName}' a {$childrenCount} entité(s) enfant(s).");
                    
                    if (!$this->option('force') && !$this->confirm("Voulez-vous quand même supprimer '{$entityName}' et ses enfants ?", false)) {
                        $this->info("  → Ignorée");
                        continue;
                    }
                    
                    // Delete children first
                    foreach ($entite->children as $child) {
                        $this->line("  → Suppression de l'enfant: {$child->name}");
                        $child->delete();
                    }
                }
                
                // Check if entity has users
                $usersCount = $entite->users()->count();
                if ($usersCount > 0) {
                    $this->warn("⚠️  L'entité '{$entityName}' a {$usersCount} utilisateur(s) associé(s).");
                    $this->line("  → Les relations utilisateur-entité seront supprimées automatiquement.");
                }
                
                // Delete the entity
                $entite->delete();
                $this->info("✓ Supprimée: {$entityName}");
                $deletedCount++;
            } else {
                $this->line("  → Non trouvée: {$entityName}");
                $notFoundCount++;
            }
            $this->newLine();
        }

        $this->info("✅ Opération terminée !");
        $this->info("   Supprimées: {$deletedCount}");
        $this->info("   Non trouvées: {$notFoundCount}");
        
        return Command::SUCCESS;
    }
}

