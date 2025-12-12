@extends('layouts.app')

@section('title', 'Demandes de mes Agents')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/leaves.css') }}">
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <header class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2" aria-hidden="true"></i>
                Demandes de mes Agents
            </h1>
            <div>
                <a href="{{ route('hr.leaves.annuel') }}" 
                   class="btn btn-outline-secondary"
                   aria-label="Retour à la page précédente">
                    <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>
                    Retour
                </a>
            </div>
        </div>
    </header>

    <!-- Leave Statistics -->
    @if(isset($leaveStats))
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-exchange-alt me-2"></i>Suivi des Demandes de Congé
            </h5>
            <p class="text-muted mb-0 small">Consultez l'état des demandes de congé de vos agents</p>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-exchange-alt text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0 small">Total Demandes</p>
                                    <h4 class="mb-0 fw-bold">{{ number_format($leaveStats['total'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-clock text-warning fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0 small">En Attente</p>
                                    <h4 class="mb-0 fw-bold">{{ number_format($leaveStats['pending'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-check-circle text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0 small">Validées</p>
                                    <h4 class="mb-0 fw-bold">{{ number_format($leaveStats['approved'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-times-circle text-danger fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0 small">Rejetées</p>
                                    <h4 class="mb-0 fw-bold">{{ number_format($leaveStats['rejected'] ?? 0) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <x-leaves.filters 
        :currentStatut="$filterData['currentStatut'] ?? null"
        :currentYear="$filterData['currentYear'] ?? date('Y')"
        :currentMonth="$filterData['currentMonth'] ?? null"
    />

    <!-- Demandes Cards View -->
    <div class="row g-3">
        @forelse($demandes as $viewModel)
            <x-leaves.card-item :viewModel="$viewModel" />
        @empty
            <x-leaves.empty-state />
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($demandes->hasPages())
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <p class="mb-0 text-muted">
                            Affichage de <strong>{{ $demandes->firstItem() }}</strong> à <strong>{{ $demandes->lastItem() }}</strong> sur <strong>{{ $demandes->total() }}</strong> demande(s)
                        </p>
                    </div>
                    <div>
                        {{ $demandes->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    // Prevent loading state ("Envoi en cours...") for approve and reject forms
    // Run immediately to catch forms before global handlers attach
    function preventLoadingState() {
        const approveRejectForms = document.querySelectorAll('form[action*="approve-chef"], form[action*="reject-chef"]');
        
        approveRejectForms.forEach(function(form) {
            // Mark form to skip loading state
            form.setAttribute('data-skip-loading', 'true');
            
            // Intercept form submit before global handlers
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    // Mark button to prevent loading state
                    submitBtn.setAttribute('data-skip-loading', 'true');
                    submitBtn.setAttribute('data-submitting', 'true'); // Prevent global handler
                    
                    // Clean up any loading state that might have been added
                    const cleanup = function() {
                        const btnText = submitBtn.querySelector('.btn-text');
                        if (btnText && (btnText.textContent.includes('Envoi en cours') || btnText.textContent.includes('Envoi...'))) {
                            btnText.remove();
                        }
                        const spinner = submitBtn.querySelector('.loading-spinner');
                        if (spinner) {
                            spinner.remove();
                        }
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    };
                    
                    // Clean up immediately and after a short delay
                    cleanup();
                    setTimeout(cleanup, 50);
                    setTimeout(cleanup, 200);
                }
            }, true); // Capture phase - runs before other handlers
        });
    }
    
    // Run immediately if DOM is ready, otherwise wait
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', preventLoadingState);
    } else {
        preventLoadingState();
    }
    
    // Also run after a short delay to catch any late-loading scripts
    setTimeout(preventLoadingState, 100);
})();
</script>
@endpush

@endsection
