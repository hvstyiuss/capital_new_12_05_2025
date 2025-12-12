<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'Explication - Retard de Retour</title>
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
        .warning-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box h3 {
            color: #856404;
            margin-top: 0;
        }
        .deadline {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .deadline strong {
            color: #dc3545;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>DEMANDE D'EXPLICATION</h1>
        <p>Retard de Retour de Congé</p>
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
    </div>

    <div class="info-section">
        <h2 style="margin-top: 0; color: #0066cc;">Informations du Congé</h2>
        <div class="info-row">
            <span class="info-label">Date de Départ:</span>
            <span class="info-value">{{ $avisDepart && $avisDepart->date_depart ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d/m/Y') : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de Retour Prévue:</span>
            <span class="info-value">{{ $avisDepart && $avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d/m/Y') : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de Retour Déclarée:</span>
            <span class="info-value">{{ $avisRetour && $avisRetour->date_retour_declaree ? \Carbon\Carbon::parse($avisRetour->date_retour_declaree)->format('d/m/Y') : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nombre de Jours Demandés:</span>
            <span class="info-value">{{ $avisDepart->nb_jours_demandes }} jour(s)</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nombre de Jours Consommés:</span>
            <span class="info-value">{{ $avisRetour->nbr_jours_consumes }} jour(s)</span>
        </div>
    </div>

    <div class="warning-box">
        <h3>⚠️ Dérangement Détecté</h3>
        @if(isset($explanationData) && $explanationData['isLateReturn'] && $explanationData['isConsumptionExceeded'])
            <p>
                Vous avez déclaré votre retour le <strong>{{ $explanationData['dateRetourDeclaree']->format('d/m/Y') }}</strong>, 
                alors que la date de retour prévue était le <strong>{{ $explanationData['dateRetourPrevue']->format('d/m/Y') }}</strong>.
            </p>
            <p>
                De plus, vous avez consommé <strong>{{ $avisRetour->nbr_jours_consumes }} jour(s)</strong>, 
                alors que la période entre votre date de départ ({{ $explanationData['dateDepart']->format('d/m/Y') }}) 
                et votre date de retour déclarée ({{ $explanationData['dateRetourDeclaree']->format('d/m/Y') }}) 
                représente <strong>{{ $explanationData['expectedDays'] }} jour(s) ouvrable(s)</strong>.
            </p>
            <p>
                Conformément aux procédures en vigueur, nous vous demandons de bien vouloir nous fournir une explication détaillée concernant ce dépassement.
            </p>
        @elseif(isset($explanationData) && $explanationData['isConsumptionExceeded'])
            <p>
                Vous avez consommé <strong>{{ $avisRetour->nbr_jours_consumes }} jour(s)</strong>, 
                alors que la période entre votre date de départ ({{ $explanationData['dateDepart']->format('d/m/Y') }}) 
                et votre date de retour déclarée ({{ $explanationData['dateRetourDeclaree']->format('d/m/Y') }}) 
                représente <strong>{{ $explanationData['expectedDays'] }} jour(s) ouvrable(s)</strong>.
            </p>
            <p>
                Conformément aux procédures en vigueur, nous vous demandons de bien vouloir nous fournir une explication détaillée concernant ce dépassement de consommation.
            </p>
        @elseif(isset($explanationData) && $explanationData['isLateReturn'])
            <p>
                Vous avez déclaré votre retour le <strong>{{ $explanationData['dateRetourDeclaree']->format('d/m/Y') }}</strong>, 
                alors que la date de retour prévue était le <strong>{{ $explanationData['dateRetourPrevue']->format('d/m/Y') }}</strong>.
            </p>
            <p>
                Conformément aux procédures en vigueur, nous vous demandons de bien vouloir nous fournir une explication détaillée concernant ce retard.
            </p>
        @endif
    </div>

    <div class="deadline">
        <strong>⏰ DÉLAI DE RÉPONSE: 48 HEURES</strong><br>
        <p style="margin: 10px 0 0 0;">
            Vous devez fournir votre explication avant le:<br>
            <strong style="font-size: 20px;">{{ isset($deadline) ? $deadline->format('d/m/Y à H:i') : \Carbon\Carbon::now()->addHours(48)->format('d/m/Y à H:i') }}</strong>
        </p>
    </div>

    <div class="info-section">
        <h2 style="margin-top: 0; color: #0066cc;">Instructions</h2>
        <p>Veuillez fournir une explication détaillée et justifiée concernant ce dérangement. Cette explication doit être soumise dans les 48 heures suivant la réception de ce document.</p>
        <p><strong>Merci de votre compréhension et de votre collaboration.</strong></p>
    </div>

    <div class="footer">
        <p>Document généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</p>
        <p>Direction du Capital Humain et de la Logistique</p>
    </div>
</body>
</html>


