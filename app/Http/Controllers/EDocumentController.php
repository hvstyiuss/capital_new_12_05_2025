<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Actions\EDocument\ShowLoi5220Action;
use App\Actions\EDocument\ShowStatutPersonnelAction;
use App\Actions\EDocument\ShowDemandeIntegrationAction;
use App\Actions\EDocument\ShowUniteGestionTerritorialeAction;

class EDocumentController extends Controller
{
    /**
     * Display Loi N°52.20
     */
    public function loi5220()
    {
        $data = app(ShowLoi5220Action::class)->execute();

        return view('edocuments.loi5220', $data);
    }

    /**
     * Display Statut du Personnel
     */
    public function statutPersonnel()
    {
        $data = app(ShowStatutPersonnelAction::class)->execute();

        return view('edocuments.statut-personnel', $data);
    }

    /**
     * Display Modèle de demande d'intégration à l'ANEF
     */
    public function demandeIntegration()
    {
        $data = app(ShowDemandeIntegrationAction::class)->execute();

        return view('edocuments.demande-integration', $data);
    }

    /**
     * Display Unité de Gestion Territoriale
     */
    public function uniteGestionTerritoriale()
    {
        $data = app(ShowUniteGestionTerritorialeAction::class)->execute();

        return view('edocuments.unite-gestion-territoriale', $data);
    }
}

