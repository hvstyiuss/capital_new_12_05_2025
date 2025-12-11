<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace de vérification du Congé Annuel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header-section {
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header-section h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header-section .year {
            font-size: 1.5rem;
            opacity: 0.9;
        }
        .content-section {
            padding: 40px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid #2c5530;
        }
        .info-card h3 {
            color: #2c5530;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .info-value {
            color: #333;
            font-weight: 500;
        }
        .table-section {
            margin-top: 30px;
        }
        .table-section table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table-section th {
            background: #2c5530;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        .table-section td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .table-section tr:last-child td {
            border-bottom: none;
        }
        .confirmation-box {
            background: #d4edda;
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
        }
        .confirmation-box h4 {
            color: #155724;
            margin-bottom: 15px;
        }
        .confirmation-box p {
            color: #155724;
            margin-bottom: 10px;
        }
        .error-box {
            background: #f8d7da;
            border: 2px solid #dc3545;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
        }
        .error-box h3 {
            color: #721c24;
            margin-bottom: 15px;
        }
        .error-box p {
            color: #721c24;
        }
        .signature-section {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .signature-section p {
            color: #666;
            font-style: italic;
        }
        .footer-section {
            background: #2c5530;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .footer-section a {
            color: #fff;
            text-decoration: none;
        }
        .footer-section a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="header-section">
            <h1><i class="fas fa-shield-check me-3"></i>Espace de vérification du Congé Annuel</h1>
            <div class="year">- {{ Carbon\Carbon::now()->year }} -</div>
        </div>

        <div class="content-section">
            @if(isset($verified) && $verified)
                <!-- Employee Information -->
                <div class="info-card">
                    <h3><i class="fas fa-user me-2"></i>Informations du Bénéficiaire</h3>
                    <div class="info-row">
                        <span class="info-label">Nom & Prénom du bénéficiaire</span>
                        <span class="info-value"><strong>{{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}</strong></span>
                    </div>
                    @if($user->userInfo && $user->userInfo->cin)
                    <div class="info-row">
                        <span class="info-label">CINE</span>
                        <span class="info-value">{{ $user->userInfo->cin }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">PPR</span>
                        <span class="info-value">{{ $user->ppr }}</span>
                    </div>
                </div>

                <!-- Leave Summary Table -->
                <div class="table-section">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre de Jours</th>
                                <th>Du</th>
                                <th>Au</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>{{ $avisDepart ? $avisDepart->nb_jours_demandes : ($avisRetour ? $avisRetour->nbr_jours_consumes : 0) }}j</strong></td>
                                <td>{{ $avisDepart ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d-m-Y') : 'N/A' }}</td>
                                <td>{{ $avisDepart ? ($avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d-m-Y') : 'N/A') : ($avisRetour && $avisRetour->date_retour_effectif ? \Carbon\Carbon::parse($avisRetour->date_retour_effectif)->format('d-m-Y') : 'N/A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Confirmation Box -->
                <div class="confirmation-box">
                    <h4><i class="fas fa-check-circle me-2"></i>Confirmation & Validation</h4>
                    <p>Nous avons le plaisir de vous informer que cette fiche de congé de <strong>M. {{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}</strong> a été correctement générée à partir de notre plateforme RH Capital Humain. Les données fournies sont vérifiées et validées par notre système.</p>
                    <p>En tant qu'Agence Nationale des Eaux et Forêts [A.N.E.F], nous veillons à ce que toutes les informations soient exactes et à jour. Pour toute question ou information complémentaire, n'hésitez pas à nous contacter via les canaux appropriés.</p>
                </div>

                <!-- Leave Balance Information -->
                @if(isset($leaveData))
                <div class="table-section">
                    <table>
                        <thead>
                            <tr>
                                <th>Référence Décision</th>
                                <th>Reliquat Année Antérieure</th>
                                <th>Reliquat Année Courante</th>
                                <th>Cumul Jours Consommés</th>
                                <th>Reste</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $leaveData['reference_decision'] ?? 'N/A' }}</td>
                                <td>{{ $leaveData['reliquat_annee_anterieure'] ?? 0 }}j</td>
                                <td>{{ $leaveData['reliquat_annee_courante'] ?? 0 }}j</td>
                                <td>{{ $leaveData['cumul_jours_consommes'] ?? 0 }}j</td>
                                <td><strong>{{ $leaveData['jours_restants'] ?? 0 }}j</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- Avis de Départ Details -->
                @if($avisDepart)
                <div class="table-section">
                    <h3 class="mb-3"><i class="fas fa-plane-departure me-2"></i>Avis de Départ</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Demande</th>
                                <th>Nombre Jours Demandés</th>
                                <th>Date Départ</th>
                                <th>Date Retour Prévue</th>
                                <th>Intérimaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $demande->id ?? 'N/A' }}</td>
                                <td>{{ $avisDepart->nb_jours_demandes }}j</td>
                                <td>{{ \Carbon\Carbon::parse($avisDepart->date_depart)->format('d-m-Y') }}</td>
                                <td>{{ $avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d-m-Y') : 'N/A' }}</td>
                                <td>Aucun intérimaire</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- Avis de Retour Details -->
                @if($avisRetour)
                <div class="table-section">
                    <h3 class="mb-3"><i class="fas fa-plane-arrival me-2"></i>Avis de Retour</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Demande</th>
                                <th>Nombre Jours Demandés</th>
                                <th>Date Départ</th>
                                <th>Date Retour Prévue</th>
                                <th>Date Retour Effectif</th>
                                <th>Jours Consommés</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $demande->id ?? 'N/A' }}</td>
                                <td>{{ $avisDepart ? $avisDepart->nb_jours_demandes : 'N/A' }}j</td>
                                <td>{{ $avisDepart ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d-m-Y') : 'N/A' }}</td>
                                <td>{{ $avisDepart && $avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d-m-Y') : 'N/A' }}</td>
                                <td>{{ $avisRetour->date_retour_effectif ? \Carbon\Carbon::parse($avisRetour->date_retour_effectif)->format('d-m-Y') : 'N/A' }}</td>
                                <td><strong>{{ $avisRetour->nbr_jours_consumes ?? 0 }}j</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- Signature Section -->
                <div class="signature-section">
                    <p><strong>Signature ANEF</strong></p>
                </div>
            @else
                <!-- Error Message -->
                <div class="error-box">
                    <h3><i class="fas fa-exclamation-triangle me-2"></i>Erreur de Vérification</h3>
                    <p>{{ $error ?? 'Code de vérification invalide ou introuvable.' }}</p>
                </div>
            @endif
        </div>

        <div class="footer-section">
            <p class="mb-2">Email : <a href="mailto:capitalhumain@eauxetforets.gov.ma">capitalhumain@eauxetforets.gov.ma</a></p>
            <p class="mb-0">Copyright © ANEF {{ Carbon\Carbon::now()->year }}</p>
        </div>
    </div>
</body>
</html>

