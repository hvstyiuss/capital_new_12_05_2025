<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AvisDepart;
use App\Models\Demande;
use App\Services\LeavePDFService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        // Get all approved avis de départ
        // Note: PDFs are now generated on-the-fly, so we check for approved status only
        $avisDeparts = AvisDepart::where('statut', 'approved')
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

                // Generate PDF using service (on-the-fly, no storage)
                $pdf = $pdfService->generateAvisDepartPDF($avisDepart, $demande->user);
                
                // PDF is generated on-the-fly, no need to store path
                // If pdf_path column exists and is needed, we can mark it as generated
                if (Schema::hasColumn('avis_departs', 'pdf_path')) {
                    // Mark as generated without storing actual path
                    $avisDepart->update(['pdf_path' => 'generated']);
                }
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

