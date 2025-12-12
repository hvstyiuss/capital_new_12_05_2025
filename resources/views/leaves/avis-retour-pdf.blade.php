<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis de Retour</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            font-size: 10pt;
            direction: ltr;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
        }
        .logo {
            width: 100px;
            height: auto;
            margin: 0 auto 5px;
            display: block;
        }
        .agency-name-ar {
            font-size: 14pt;
            font-weight: bold;
            margin: 3px 0;
            color: #2c5530;
        }

        .directorate {
            font-size: 10pt;
            font-weight: bold;
            margin: 5px 0 3px;
            color: #000;
        }
        .document-title {
            font-size: 16pt;
            font-weight: bold;
            margin: 8px 0;
            color: #000;
            text-decoration: underline;
        }
        .content-wrapper {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        .left-section, .right-section {
            display: table-cell;
            vertical-align: top;
            width: 48%;
            padding: 10px;
        }
        .left-section {
            border-right: 2px solid #000;
            padding-right: 15px;
            direction: ltr;
            text-align: left;
        }
        .right-section {
            padding-left: 15px;
            direction: ltr;
            text-align: left;
        }
        .info-row {
            margin: 6px 0;
            line-height: 1.6;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 200px;
            font-size: 9pt;
        }
        .info-value {
            display: inline-block;
            font-size: 9pt;
        }
        .info-value-fill {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 150px;
            padding: 0 5px;
        }
        .signature-section {
            margin-top: 15px;
            display: table;
            width: 100%;
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 8px;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        .signature-checkbox {
            margin: 8px 0;
            font-size: 8pt;
            text-align: center;
        }
        .signature-checkbox input {
            margin-left: 5px;
        }
        .qr-code-placeholder {
            width: 70px;
            height: 70px;
            border: 1px solid #ccc;
            margin: 10px auto;
            display: block;
            background: #f5f5f5;
        }
        .qr-code {
            width: 70px;
            height: 70px;
            margin: 10px auto;
            display: block;
            text-align: center;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
            display: block;
            margin: 0 auto;
        }
        .date-section {
            text-align: right;
            margin-top: 10px;
            font-size: 9pt;
            direction: ltr;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($logoData)
        <img src="{{ $logoData }}" alt="ANEF Logo" class="logo">
        @endif
        <div class="directorate">{{ $parentEntityName ?? 'Direction du Capital Humain et de la Logistique' }}</div>
        <div class="document-title">Avis de Retour</div>
    </div>

    <div class="content-wrapper">
        <div class="left-section">
            <div class="info-row">
                <span class="info-label">{{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}</span>
            </div>
            @if($gradeName)
            <div class="info-row">
                <span class="info-label">{{ $gradeName }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Numéro PPR : {{ $user->ppr }}</span>
            </div>
            @if($serviceName)
            <div class="info-row">
                <span class="info-label">{{ $serviceName }}</span>
            </div>
            @endif
        </div>

        <div class="right-section">
            <div class="info-row">
                <span class="info-label">Monsieur/Madame :</span>
                <span class="info-value-fill"></span>
            </div>
            <div class="info-row">
                <span class="info-label">Grade :</span>
                <span class="info-value">{{ $gradeName ?: '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fonction :</span>
                <span class="info-value">{{ $currentParcours && $currentParcours->poste ? $currentParcours->poste : 'Chef de service' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Service d'affectation :</span>
                <span class="info-value">{{ $serviceName ?: '' }}</span>
            </div>
            @if($avisDepart && $dateDepartFormatted)
            <div class="info-row">
                <span class="info-label">Date de départ :</span>
                <span class="info-value-fill">{{ $dateDepartFormatted }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Date de retour prévue :</span>
                <span class="info-value-fill">{{ $dateRetourDeclareeFormatted }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de retour effective :</span>
                <span class="info-value-fill">{{ $dateRetourEffectifFormatted }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nombre de jours consommés :</span>
                <span class="info-value">{{ $avisRetour->nbr_jours_consumes ?? 0 }} {{ ($avisRetour->nbr_jours_consumes ?? 0) > 1 ? 'jours' : 'jour' }}</span>
            </div>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box" style="text-align: center;">
            <div class="signature-label">Signature de l'intéressé</div>
            @if($qrCodeData)
            <div class="qr-code">
                <img src="{{ $qrCodeData }}" alt="QR Code" style="width:70px;height:70px;display:block;margin:10px auto;" />
            </div>
            @elseif($verificationUrl)
            <div class="qr-code">
                <div style="text-align:center;font-size:8pt;word-break:break-all;padding:10px;">{{ $verificationUrl }}</div>
            </div>
            @else
            <div class="qr-code-placeholder"></div>
            @endif
        </div>
        <div class="signature-box" style="text-align: center;">
            <div class="signature-label">Signature du supérieur hiérarchique</div>
            <div class="signature-checkbox">
                <input type="checkbox" checked disabled> Marque d'approbation valant signature électronique
            </div>
        </div>
    </div>


</body>
</html>
