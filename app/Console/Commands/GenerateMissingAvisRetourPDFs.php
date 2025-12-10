<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvisRetour;
use App\Models\Demande;
use App\Http\Controllers\LeaveController;
use Illuminate\Support\Facades\Schema;

class GenerateMissingAvisRetourPDFs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avis-retour:generate-missing-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PDFs for approved avis de retour that don\'t have PDFs yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recherche des avis de retour approuvés sans PDF...');

        // Check if column exists
        if (!Schema::hasColumn('avis_retours', 'pdf_path')) {
            $this->error('La colonne pdf_path n\'existe pas dans la table avis_retours.');
            $this->info('Veuillez exécuter la migration: php artisan migrate');
            return 1;
        }

        // Get all approved avis de retour without PDF
        $avisRetours = AvisRetour::where('statut', 'approved')
            ->where(function($query) {
                $query->whereNull('pdf_path')
                      ->orWhere('pdf_path', '');
            })
            ->with(['avis.demande.user', 'avis.avisDepart'])
            ->get();

        if ($avisRetours->isEmpty()) {
            $this->info('Aucun avis de retour approuvé sans PDF trouvé.');
            return 0;
        }

        $this->info("Trouvé {$avisRetours->count()} avis de retour approuvés sans PDF.");

        $controller = new LeaveController();
        $generated = 0;
        $failed = 0;

        foreach ($avisRetours as $avisRetour) {
            try {
                $demande = $avisRetour->avis->demande ?? null;
                if (!$demande || !$demande->user) {
                    $this->warn("Avis de retour #{$avisRetour->id}: Demande ou utilisateur introuvable.");
                    $failed++;
                    continue;
                }

                $avisDepart = $avisRetour->avis->avisDepart ?? null;

                // Generate PDF using reflection to access private method
                $reflection = new \ReflectionClass($controller);
                $method = $reflection->getMethod('generateAvisRetourPDF');
                $method->setAccessible(true);
                $pdfPath = $method->invoke($controller, $avisRetour, $demande->user, $avisDepart);

                // Update avis retour with PDF path
                $avisRetour->update(['pdf_path' => $pdfPath]);
                $generated++;

                $this->info("PDF généré pour l'avis de retour #{$avisRetour->id} (PPR: {$demande->user->ppr})");
            } catch (\Exception $e) {
                $this->error("Erreur lors de la génération du PDF pour l'avis de retour #{$avisRetour->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\nRésumé:");
        $this->info("PDFs générés: {$generated}");
        $this->info("Échecs: {$failed}");

        return 0;
    }
}






