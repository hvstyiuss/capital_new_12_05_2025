<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis de Départ - Congé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f5f5f5;
            border-left: 4px solid #0066cc;
        }
        .info-row {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .approval-box {
            background-color: #d4edda;
            border: 2px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .approval-box strong {
            color: #155724;
            font-size: 18px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AVIS DE DÉPART</h1>
        <p>Demande de Congé Administratif</p>
    </div>

    <div class="info-section">
        <h2 style="margin-top: 0; color: #0066cc;">Informations du Collaborateur</h2>
        <div class="info-row">
            <span class="info-label">Nom Complet:</span>
            <span class="info-value">{{ $user->fname }} {{ $user->lname }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">PPR:</span>
            <span class="info-value">{{ $user->ppr }}</span>
        </div>
        @if($user->userInfo && $user->userInfo->grade)
        <div class="info-row">
            <span class="info-label">Grade:</span>
            <span class="info-value">{{ $user->userInfo->grade->name }}</span>
        </div>
        @endif
    </div>

    <div class="info-section">
        <h2 style="margin-top: 0; color: #0066cc;">Informations du Congé</h2>
        <div class="info-row">
            <span class="info-label">Date de Départ:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($avisDepart->date_depart)->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de Retour Prévue:</span>
            <span class="info-value">{{ $avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d/m/Y') : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nombre de Jours Demandés:</span>
            <span class="info-value">{{ $avisDepart->nb_jours_demandes }} jour(s)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de Dépôt:</span>
            <span class="info-value">{{ $avisDepart->created_at->format('d/m/Y à H:i') }}</span>
        </div>
    </div>

    <div class="approval-box">
        <strong>✓ AVIS DE DÉPART VALIDÉ</strong>
        <p style="margin: 10px 0 0 0; color: #155724;">Date de validation: {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="footer">
        <p>Document généré automatiquement le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
        <p>Ce document certifie que l'avis de départ a été validé par le chef hiérarchique.</p>
    </div>
</body>
</html>






