@extends('layouts.app')

@section('title', 'Unité de Gestion Territoriale')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center">
                <i class="fas fa-map-marked-alt text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                    Unité de Gestion Territoriale
                </h1>
                <p class="text-gray-600 text-lg mt-2">Document officiel</p>
            </div>
        </div>
    </div>

    <!-- PDF Viewer -->
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-6 border border-white/20">
        @if($pdfExists)
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Document PDF</h2>
                <a href="{{ $pdfUrl }}" 
                   download 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl hover:from-orange-700 hover:to-red-700 transition-all duration-300">
                    <i class="fas fa-download"></i>
                    <span>Télécharger</span>
                </a>
            </div>
            <div class="border rounded-xl overflow-hidden bg-gray-100" style="height: 80vh;">
                <iframe src="{{ $pdfUrl }}#toolbar=1" 
                        class="w-full h-full border-0"
                        type="application/pdf">
                    <p>Votre navigateur ne supporte pas l'affichage des PDFs. 
                       <a href="{{ $pdfUrl }}" download>Téléchargez le document</a>
                    </p>
                </iframe>
            </div>
        @else
            <div class="text-center py-12">
                <div class="mb-4">
                    <i class="fas fa-file-pdf text-gray-400 text-6xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">Document non disponible</h3>
                <p class="text-gray-600 mb-6">Le fichier PDF "Unité de Gestion Territoriale" n'est pas encore disponible.</p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-yellow-600"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">
                                Veuillez contacter l'administrateur pour obtenir ce document ou vérifier que le fichier <strong>unites_civil.pdf</strong> a été ajouté dans le dossier <strong>public/edocuments/</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

