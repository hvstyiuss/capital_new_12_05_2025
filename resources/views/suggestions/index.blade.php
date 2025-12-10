@extends('layouts.app')

@section('title', 'Boîte à Suggestions')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-comments text-success me-2"></i>
                    Boîte à Suggestions
                </h1>
            </div>

            <!-- Information Banner -->
            <div class="alert alert-info mb-4" id="permanentAlertSuggestions" style="display: block !important; visibility: visible !important;">
                <p class="mb-0 text-gray-800">
                    Votre avis nous intéresse ! N'hésitez pas à partager vos idées, suggestions ou remarques pour améliorer le fonctionnement de notre platforme. Vous pouvez également signaler une erreur concernant vos données personnelles ou solliciter un accompagnement en cas de besoin.
                </p>
            </div>

            <!-- Suggestion Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Envoyer une suggestion</h5>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('suggestions.store') }}" method="POST" novalidate autocomplete="off">
                        @csrf
                        
                        <!-- Subject Field -->
                        <div class="mb-4">
                            <label for="sujet" class="form-label text-gray-700 fw-semibold">
                                Objet de la suggestion :
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('sujet') is-invalid @enderror" 
                                id="sujet" 
                                name="sujet" 
                                value="{{ old('sujet') }}"
                                placeholder="Ex : Amélioration des outils de communication">
                            @error('sujet')
                                <div class="invalid-feedback validation-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message Field -->
                        <div class="mb-4">
                            <label for="message" class="form-label text-gray-700 fw-semibold">
                                Message :
                            </label>
                            <textarea 
                                class="form-control @error('message') is-invalid @enderror" 
                                id="message" 
                                name="message" 
                                rows="8"
                                placeholder="Exprimez-vous en toute liberté... (Arabe ou Français)">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback validation-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-start">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5 mb-4">
                <p class="text-muted mb-1">
                    Email : <a href="mailto:capitalhumain@eauxetforets.gov.ma" class="text-decoration-none">capitalhumain@eauxetforets.gov.ma</a>
                </p>
                <p class="text-muted mb-0">
                    Copyright © ANEF {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .alert-info {
        background-color: #e7f3ff;
        border-color: #b3d9ff;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        padding: 0.5rem 2rem;
        font-weight: 500;
    }
    
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    /* Hide duplicate validation messages */
    .validation-error {
        display: block !important;
    }

    .invalid-feedback.validation-error ~ .invalid-feedback.validation-error,
    .invalid-feedback.validation-error + .invalid-feedback:not(.validation-error) {
        display: none !important;
    }

    /* Hide browser's native validation tooltips */
    input:invalid,
    textarea:invalid {
        box-shadow: none;
    }

    input:invalid:focus,
    textarea:invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    #permanentAlertSuggestions {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
    }

    #permanentAlertSuggestions .btn-close {
        display: none !important;
    }

    /* Prevent any Bootstrap dismiss functionality */
    #permanentAlertSuggestions.alert-dismissible {
        padding-right: 1rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[novalidate]');
    const sujetInput = document.getElementById('sujet');
    const messageInput = document.getElementById('message');
    
    // Clear any custom validity messages
    sujetInput.setCustomValidity('');
    messageInput.setCustomValidity('');
    
    // Remove duplicate validation error messages
    function removeDuplicateErrors() {
        const errorDivs = document.querySelectorAll('.invalid-feedback');
        errorDivs.forEach((div, index) => {
            if (index > 0 && div.textContent === errorDivs[0].textContent) {
                div.style.display = 'none';
            }
        });
    }
    
    // Validate on input
    sujetInput.addEventListener('input', function() {
        if (this.value.trim()) {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
        removeDuplicateErrors();
    });
    
    messageInput.addEventListener('input', function() {
        if (this.value.trim() && this.value.trim().length >= 10) {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
        removeDuplicateErrors();
    });
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const sujet = sujetInput.value.trim();
        const message = messageInput.value.trim();
        
        // Clear previous errors
        sujetInput.classList.remove('is-invalid');
        messageInput.classList.remove('is-invalid');
        sujetInput.setCustomValidity('');
        messageInput.setCustomValidity('');
        
        // Validate subject
        if (!sujet) {
            isValid = false;
            sujetInput.classList.add('is-invalid');
            sujetInput.setCustomValidity('Ce champ est requis');
        }
        
        // Validate message
        if (!message) {
            isValid = false;
            messageInput.classList.add('is-invalid');
            messageInput.setCustomValidity('Ce champ est requis');
        } else if (message.length < 10) {
            isValid = false;
            messageInput.classList.add('is-invalid');
            messageInput.setCustomValidity('Le message doit contenir au moins 10 caractères');
        }
        
        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
            
            // Focus on first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            // Remove duplicate errors
            removeDuplicateErrors();
            return false;
        }
    });
    
    // Initial cleanup
    removeDuplicateErrors();

    // Ensure permanent alert stays visible
    const permanentAlert = document.getElementById('permanentAlertSuggestions');
    if (permanentAlert) {
        // Remove any dismiss functionality
        permanentAlert.classList.remove('alert-dismissible', 'fade', 'show');
        
        // Remove any close buttons
        const closeButtons = permanentAlert.querySelectorAll('.btn-close');
        closeButtons.forEach(btn => btn.remove());
        
        // Force display
        permanentAlert.style.display = 'block';
        permanentAlert.style.visibility = 'visible';
        permanentAlert.style.opacity = '1';
        
        // Prevent hiding via Bootstrap
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    permanentAlert.style.display = 'block';
                    permanentAlert.style.visibility = 'visible';
                    permanentAlert.style.opacity = '1';
                }
            });
        });
        
        observer.observe(permanentAlert, {
            attributes: true,
            attributeFilter: ['style', 'class']
        });
    }
});
</script>
@endpush
@endsection

