@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعلان بالذهاب - Avis de Départ</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            font-size: 12pt;
            direction: rtl;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        .logo {
            width: 120px;
            height: auto;
            margin: 0 auto 10px;
            display: block;
        }
        .agency-name-ar {
            font-size: 16pt;
            font-weight: bold;
            margin: 5px 0;
            color: #2c5530;
        }
        .agency-name-fr {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
            color: #2c5530;
        }
        .directorate {
            font-size: 13pt;
            font-weight: bold;
            margin: 10px 0 5px;
            color: #000;
        }
        .document-title {
            font-size: 18pt;
            font-weight: bold;
            margin: 15px 0;
            color: #000;
            text-decoration: underline;
        }
        .content-wrapper {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .left-section, .right-section {
            display: table-cell;
            vertical-align: top;
            width: 48%;
            padding: 15px;
        }
        .left-section {
            border-left: 2px solid #000;
            padding-right: 20px;
        }
        .right-section {
            padding-left: 20px;
        }
        .info-row {
            margin: 12px 0;
            line-height: 1.8;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            min-width: 180px;
        }
        .info-value {
            display: inline-block;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
            border-top: 2px solid #000;
            padding-top: 20px;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 60px;
            font-size: 11pt;
        }
        .signature-checkbox {
            margin: 10px 0;
            font-size: 10pt;
        }
        .qr-code-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ccc;
            margin: 10px auto;
            display: block;
            background: #f5f5f5;
        }
        .qr-code {
            width: 80px;
            height: 80px;
            margin: 10px auto;
            display: block;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
        }
        .date-section {
            text-align: left;
            margin-top: 30px;
            font-size: 10pt;
            direction: ltr;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $logoPath = public_path('images/anef.png');
            $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
        @endphp
        @if($logoData)
        <img src="{{ $logoData }}" alt="ANEF Logo" class="logo">
        @endif
        <div class="agency-name-ar">الوكالة الوطنية للمياه والغابات</div>
        <div class="agency-name-fr">AGENCE NATIONALE DES EAUX ET FORETS</div>
        <div class="directorate">مديرية الرأسمال البشري واللوجستيك</div>
        <div class="document-title">إعلان بالذهاب</div>
    </div>

    <div class="content-wrapper">
        <div class="left-section">
            <div class="info-row">
                <span class="info-label">{{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}</span>
            </div>
            @php
                $currentParcours = \App\Models\Parcours::where('ppr', $user->ppr)
                    ->where(function($query) {
                        $query->whereNull('date_fin')
                              ->orWhere('date_fin', '>=', now());
                    })
                    ->with(['entite', 'grade'])
                    ->orderBy('date_debut', 'desc')
                    ->first();
                
                $gradeName = $user->userInfo && $user->userInfo->grade 
                    ? strtoupper($user->userInfo->grade->name) 
                    : ($currentParcours && $currentParcours->grade 
                        ? strtoupper($currentParcours->grade->name) 
                        : '');
                
                $serviceName = $currentParcours && $currentParcours->entite 
                    ? $currentParcours->entite->name 
                    : '';
            @endphp
            @if($gradeName)
            <div class="info-row">
                <span class="info-label">{{ $gradeName }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">رقم التأجير : {{ $user->ppr }}</span>
            </div>
            @if($serviceName)
            <div class="info-row">
                <span class="info-label">{{ $serviceName }}</span>
            </div>
            @endif
        </div>

        <div class="right-section">
            <div class="info-row">
                <span class="info-label">السيد (ة) :</span>
                <span class="info-value"></span>
            </div>
            <div class="info-row">
                <span class="info-label">الرتبة :</span>
                <span class="info-value">{{ $gradeName ?: '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">الوظيفة :</span>
                <span class="info-value">{{ $currentParcours && $currentParcours->poste ? $currentParcours->poste : 'Chef de service' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">مصلحة التعيين :</span>
                <span class="info-value">{{ $serviceName ?: '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">مدة الإجازة المطلوبة :</span>
                <span class="info-value">{{ $avisDepart->nb_jours_demandes }} ايام</span>
            </div>
            <div class="info-row">
                <span class="info-label">رقم مقرر :</span>
                <span class="info-value">{{ $demande ? $demande->id : ($avisDepart->id ?? '') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">تاريخ الذهاب :</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($avisDepart->date_depart)->format('d-m-Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">تاريخ الإياب المعلن :</span>
                <span class="info-value">{{ $avisDepart->date_retour ? \Carbon\Carbon::parse($avisDepart->date_retour)->format('d-m-Y') : '' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">اسم النائب خلال مدة الإجازة :</span>
                <span class="info-value"></span>
            </div>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box" style="text-align: right;">
            <div class="signature-label">إمضاء المعني بالأمر</div>
            <div class="signature-checkbox">
                <input type="checkbox" checked disabled> علامة موافقة بمثابة التوقيع إلكتروني
            </div>
            @if(isset($verificationUrl) && !empty($verificationUrl))
            <div class="qr-code">
                @php
                    try {
                        // Generate QR code as SVG (works without GD extension)
                        echo QrCode::size(80)->format('svg')->generate($verificationUrl);
                    } catch (\Exception $e) {
                        // If QR code generation fails, show URL as text
                        echo '<div style="text-align:center;font-size:8pt;word-break:break-all;">' . htmlspecialchars($verificationUrl) . '</div>';
                    }
                @endphp
            </div>
            @else
            <div class="qr-code-placeholder"></div>
            @endif
        </div>
        <div class="signature-box" style="text-align: center;">
            <div class="signature-label">إمضاء الرئيس المباشر</div>
            <div class="signature-checkbox">
                <input type="checkbox" checked disabled> علامة موافقة بمثابة التوقيع إلكتروني
            </div>
        </div>
        <div class="signature-box" style="text-align: left;">
            <div class="signature-label">إمضاء النائب</div>
        </div>
    </div>

    <div class="date-section">
        <div>... {{ \Carbon\Carbon::parse($avisDepart->created_at)->format('d-m-Y') }} ...</div>
    </div>
</body>
</html>
