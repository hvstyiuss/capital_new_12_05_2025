<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvisDepart;
use App\Models\Demande;
use App\Services\LeavePDFService;
use Illuminate\Support\Facades\DB;

class GenerateMissingAvisDepartPDFs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avis-depart:generate-missing-pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PDFs for approved avis de départ that don\'t have PDFs yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recherche des avis de départ approuvés sans PDF...');

        // Get all approved avis de départ without PDF
        $avisDeparts = AvisDepart::where('statut', 'approved')
            ->where(function($query) {
                $query->whereNull('pdf_path')
                      ->orWhere('pdf_path', '');
            })
            ->with(['avis.demande.user'])
            ->get();

        if ($avisDeparts->isEmpty()) {
            $this->info('Aucun avis de départ approuvé sans PDF trouvé.');
            return 0;
        }

        $this->info("Trouvé {$avisDeparts->count()} avis de départ approuvés sans PDF.");

        $pdfService = app(LeavePDFService::class);
        $generated = 0;
        $failed = 0;

        foreach ($avisDeparts as $avisDepart) {
            try {
                $demande = $avisDepart->avis->demande ?? null;
                if (!$demande || !$demande->user) {
                    $this->warn("Avis de départ #{$avisDepart->id}: Demande ou utilisateur introuvable.");
                    $failed++;
                    continue;
                }

                // Generate PDF using service
                $pdfPath = $pdfService->generateAvisDepartPDF($avisDepart, $demande->user);

                // Update avis depart with PDF path
                $avisDepart->update(['pdf_path' => $pdfPath]);
                $generated++;

                $this->info("PDF généré pour l'avis de départ #{$avisDepart->id} (PPR: {$demande->user->ppr})");
            } catch (\Exception $e) {
                $this->error("Erreur lors de la génération du PDF pour l'avis de départ #{$avisDepart->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\nRésumé:");
        $this->info("PDFs générés: {$generated}");
        $this->info("Échecs: {$failed}");

        return 0;
    }
}

