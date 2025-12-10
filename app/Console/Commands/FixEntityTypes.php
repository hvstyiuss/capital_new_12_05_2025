<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entite;
use App\Models\EntiteInfo;

class FixEntityTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entities:fix-types {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix entity types: mark entities with regional keywords as regional instead of central';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Mode DRY-RUN: Aucune modification ne sera effectuée.');
            $this->newLine();
        }

        // Keywords that indicate a regional entity
        $regionalKeywords = [
            'DIRECTION REGIONALE',
            'DIRECTIONS REGIONALES',
            'DIRECTION PROVINCIALE',
            'DIRECTIONS PROVINCIALES',
            'PROVINCIAL',
            'REGIONAL',
            'REGION',
            'PROVINCE',
            'PARC NATIONAL', // Parcs Nationaux are typically regional
        ];

        $this->info('Recherche des entités à corriger...');
        $this->newLine();

        // Get all entities with their info
        $entities = Entite::with('entiteInfo')->get();
        
        $this->line("Total d'entités trouvées: " . $entities->count());
        $this->newLine();
        
        $fixedCount = 0;
        $checkedCount = 0;

        foreach ($entities as $entite) {
            $nomUpper = strtoupper($entite->name);
            $isRegional = false;
            
            // Check if entity name contains regional keywords
            foreach ($regionalKeywords as $keyword) {
                if (strpos($nomUpper, $keyword) !== false) {
                    $isRegional = true;
                    break;
                }
            }
            
            // Also check if code contains regional indicators (like -B for services)
            if ($entite->code && strpos($entite->code, '-B') !== false) {
                $isRegional = true;
            }
            
            if ($isRegional) {
                $checkedCount++;
                $currentType = $entite->entiteInfo ? $entite->entiteInfo->type : null;
                
                if ($currentType !== 'regional') {
                    $this->line("→ {$entite->name}");
                    $this->line("  Type actuel: " . ($currentType ?: 'Non défini'));
                    $this->line("  Type attendu: regional");
                    
                    if (!$dryRun) {
                        // Create or update entite_info
                        EntiteInfo::updateOrCreate(
                            ['entite_id' => $entite->id],
                            [
                                'type' => 'regional',
                                'description' => $entite->entiteInfo ? $entite->entiteInfo->description : null,
                            ]
                        );
                        $this->info("  ✓ Corrigée");
                        $fixedCount++;
                    } else {
                        $this->info("  [DRY-RUN] Serait corrigée");
                        $fixedCount++;
                    }
                    $this->newLine();
                }
            }
        }

        $this->info("✅ Opération terminée !");
        $this->info("   Entités vérifiées: {$checkedCount}");
        $this->info("   Entités " . ($dryRun ? "qui seraient corrigées" : "corrigées") . ": {$fixedCount}");
        
        return Command::SUCCESS;
    }
}

