<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace de vérification du Congé Annuel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f5f7fa;
            min-height: 100vh;
            padding: 30px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .header-section {
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        .header-content {
            position: relative;
            z-index: 1;
        }
        .header-section h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .header-section .year {
            font-size: 1.1rem;
            opacity: 0.95;
            font-weight: 500;
            letter-spacing: 2px;
        }
        .content-section {
            padding: 40px;
        }
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            border: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .info-card h3 {
            color: #2c5530;
            margin-bottom: 25px;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-card h3 i {
            font-size: 1.1rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.95rem;
        }
        .info-value {
            color: #212529;
            font-weight: 600;
            font-size: 1rem;
        }
        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            border: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .summary-card h4 {
            color: #2c5530;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .summary-item {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        .summary-item-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-item-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c5530;
        }
        .confirmation-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .confirmation-box h4 {
            color: #155724;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .confirmation-box p {
            color: #155724;
            margin-bottom: 12px;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        .confirmation-box p:last-child {
            margin-bottom: 0;
        }
        .table-section {
            margin-bottom: 30px;
        }
        .table-section h3 {
            color: #2c5530;
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table-wrapper {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .table-section table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            min-width: 600px;
        }
        .table-section thead {
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
        }
        .table-section th {
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        .table-section td {
            padding: 18px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
            font-size: 0.95rem;
        }
        .table-section tbody tr:hover {
            background: #f8f9fa;
        }
        .table-section tbody tr:last-child td {
            border-bottom: none;
        }
        .table-section tbody tr td:last-child {
            font-weight: 600;
            color: #2c5530;
        }
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .error-box h3 {
            color: #721c24;
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .error-box p {
            color: #721c24;
            font-size: 1.1rem;
        }
        .signature-section {
            margin-top: 40px;
            text-align: center;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }
        .signature-section p {
            color: #495057;
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
        }
        .footer-section {
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }
        .footer-section p {
            margin: 5px 0;
        }
        .footer-section a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
        }
        .footer-section a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .header-section {
                padding: 30px 20px;
            }
            .header-section h1 {
                font-size: 1.5rem;
            }
            .content-section {
                padding: 25px 20px;
            }
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="header-section">
            <div class="header-content">
                <h1><i class="fas fa-shield-check"></i> Espace de vérification du Congé Annuel</h1>
                <div class="year">- {{ Carbon\Carbon::now()->year }} -</div>
            </div>
        </div>

        <div class="content-section">
            @if(isset($verified) && $verified)
                <!-- Employee Information -->
                <div class="info-card">
                    <h3><i class="fas fa-user"></i> Informations du Bénéficiaire</h3>
                    <div class="info-row">
                        <span class="info-label">Nom & Prénom du bénéficiaire</span>
                        <span class="info-value">{{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}</span>
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

                <!-- Leave Summary Card -->
                <div class="summary-card">
                    <h4><i class="fas fa-calendar-alt"></i> Résumé du Congé</h4>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-item-label">Nombre de Jours</div>
                            <div class="summary-item-value">{{ $avisDepart ? $avisDepart->nb_jours_demandes : ($avisRetour ? $avisRetour->nbr_jours_consumes : 0) }}j</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-item-label">Du</div>
                            <div class="summary-item-value">{{ $avisDepart ? \Carbon\Carbon::parse($avisDepart->date_depart)->format('d-m-Y') : 'N/A' }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-item-label">Au</div>
                            <div class="summary-item-value">{{ $avisDepart ? ($avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d-m-Y') : 'N/A') : ($avisRetour && $avisRetour->date_retour_effectif ? \Carbon\Carbon::parse($avisRetour->date_retour_effectif)->format('d-m-Y') : 'N/A') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Box -->
                <div class="confirmation-box">
                    <h4><i class="fas fa-check-circle"></i> Confirmation & Validation</h4>
                    <p>Nous avons le plaisir de vous informer que cette fiche de congé de <strong>M. {{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}</strong> a été correctement générée à partir de notre plateforme RH Capital Humain. Les données fournies sont vérifiées et validées par notre système.</p>
                    <p>En tant qu'Agence Nationale des Eaux et Forêts [A.N.E.F], nous veillons à ce que toutes les informations soient exactes et à jour. Pour toute question ou information complémentaire, n'hésitez pas à nous contacter via les canaux appropriés.</p>
                </div>

                <!-- Leave Balance Information -->
                @if(isset($leaveData))
                <div class="table-section">
                    <h3><i class="fas fa-chart-line"></i> Solde des Congés</h3>
                    <div class="table-wrapper">
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
                                    <td>{{ $leaveData['jours_restants'] ?? 0 }}j</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Avis de Départ Details -->
                @if($avisDepart)
                <div class="table-section">
                    <h3><i class="fas fa-plane-departure"></i> Avis de Départ</h3>
                    <div class="table-wrapper">
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
                </div>
                @endif

                <!-- Avis de Retour Details -->
                @if($avisRetour)
                <div class="table-section">
                    <h3><i class="fas fa-plane-arrival"></i> Avis de Retour</h3>
                    <div class="table-wrapper">
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
                                    <td>{{ $avisRetour->nbr_jours_consumes ?? 0 }}j</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Signature Section -->
                <div class="signature-section">
                    <p><i class="fas fa-stamp me-2"></i>Signature ANEF</p>
                </div>
            @else
                <!-- Error Message -->
                <div class="error-box">
                    <h3><i class="fas fa-exclamation-triangle"></i> Erreur de Vérification</h3>
                    <p>{{ $error ?? 'Code de vérification invalide ou introuvable.' }}</p>
                </div>
            @endif
        </div>

        <div class="footer-section">
            <p class="mb-2"><i class="fas fa-envelope me-2"></i>Email : <a href="mailto:capitalhumain@eauxetforets.gov.ma">capitalhumain@eauxetforets.gov.ma</a></p>
            <p class="mb-0">Copyright © ANEF {{ Carbon\Carbon::now()->year }}</p>
        </div>
    </div>
</body>
</html>
