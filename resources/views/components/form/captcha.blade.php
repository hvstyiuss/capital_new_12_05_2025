@props([
    'name' => 'captcha',
    'label' => 'Résolvez cette addition simple',
    'help' => '(Protection contre les attaques automatisées)',
    'question' => null,
    'answer' => null,
    'min' => 1,
    'max' => 10,
    'required' => true,
    'disabled' => false
])

<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($help)
            <span class="form-text">{{ $help }}</span>
        @endif
    </label>

    <div class="captcha-container">
        <div class="captcha-question" id="{{ $name }}-question">
            <span id="{{ $name }}-question-text">{{ $question ?? '5 + 3' }}</span>
            <button type="button" class="captcha-refresh" id="{{ $name }}-refresh" title="Nouvelle question" {{ $disabled ? 'disabled' : '' }}>
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

        <input type="number"
               class="form-control @error($name) is-invalid @enderror"
               id="{{ $name }}"
               name="{{ $name }}"
               placeholder="Votre réponse"
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               autocomplete="off"
               min="0"
               value="{{ old($name, '') }}"
               aria-describedby="{{ $name }}-help">
    </div>

    @error($name)
        <div class="invalid-feedback">
            <i class="fas fa-exclamation-circle"></i>
            {{ $message }}
        </div>
    @enderror

    <div id="{{ $name }}-help" class="form-text">
        <i class="fas fa-info-circle"></i>
        Réponse attendue entre {{ $min }} et {{ $max }}
    </div>
</div>

@push('scripts')
<script>
(function(){
    'use strict';
    const name = @json($name);
    let currentAnswer = @json($answer ?? 8);
    
    console.log('CAPTCHA initialized with answer:', currentAnswer);

    function updateQuestion(question, answer) {
        const questionText = document.getElementById(name + '-question-text');
        const input = document.getElementById(name);
        const questionEl = document.getElementById(name + '-question');
        
        if (questionText) {
            questionText.textContent = question;
        }
        if (input) {
            input.value = '';
            input.classList.remove('is-valid', 'is-invalid');
        }
        if (questionEl) {
            questionEl.classList.remove('valid', 'invalid');
        }
        currentAnswer = answer;
        console.log('CAPTCHA updated - Question:', question, 'Answer:', answer);
    }

    function validateCaptcha() {
        const input = document.getElementById(name);
        const questionEl = document.getElementById(name + '-question');
        
        if (!input) {
            console.error('CAPTCHA input not found');
            return false;
        }
        
        const inputValue = input.value.trim();
        const val = parseInt(inputValue);

        if (questionEl) {
            questionEl.classList.remove('valid', 'invalid');
        }
        input.classList.remove('is-valid', 'is-invalid');

        if (!inputValue || inputValue === '') {
            return false;
        }
        
        if (isNaN(val)) {
            console.log('CAPTCHA validation: Not a number');
            if (questionEl) questionEl.classList.add('invalid');
            input.classList.add('is-invalid');
            return false;
        }
        
        if (val < {{ (int) $min }} || val > {{ (int) $max }}) {
            console.log('CAPTCHA validation: Out of range', val);
            if (questionEl) questionEl.classList.add('invalid');
            input.classList.add('is-invalid');
            return false;
        }
        
        console.log('CAPTCHA validation: Comparing', val, 'with', currentAnswer);
        if (val === currentAnswer) {
            console.log('CAPTCHA validation: CORRECT');
            // Don't add visual success indicators - just return true
            return true;
        }
        
        console.log('CAPTCHA validation: INCORRECT');
        if (questionEl) questionEl.classList.add('invalid');
        input.classList.add('is-invalid');
        return false;
    }

    document.addEventListener('DOMContentLoaded', function(){
        console.log('CAPTCHA DOMContentLoaded');
        const refreshBtn = document.getElementById(name + '-refresh');
        const input = document.getElementById(name);
        const questionEl = document.getElementById(name + '-question');
        
        // Clear server-side errors on load if input is empty
        if (input) {
            if (input.value === '' || input.value === null) {
                input.classList.remove('is-invalid');
                if (questionEl) {
                    questionEl.classList.remove('invalid');
                }
            }
        }
        
        // Refresh button handler
        if (refreshBtn) {
            console.log('CAPTCHA refresh button found');
            refreshBtn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                
                // Don't refresh if disabled
                if (this.disabled || input.disabled) {
                    return;
                }
                
                console.log('CAPTCHA refresh clicked');
                const original = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch('/captcha/refresh', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('CAPTCHA refresh response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('CAPTCHA refresh response data:', data);
                    if (data && data.question && data.answer !== undefined) {
                        updateQuestion(data.question, data.answer);
                    } else {
                        throw new Error('Invalid response format: ' + JSON.stringify(data));
                    }
                })
                .catch(error => {
                    console.error('Error refreshing captcha:', error);
                    alert('Erreur lors du rafraîchissement. Veuillez réessayer.');
                })
                .finally(() => {
                    this.innerHTML = original;
                    this.disabled = false;
                });
            });
        } else {
            console.error('CAPTCHA refresh button not found');
        }
        
        // Input validation handlers
        if (input) {
            input.addEventListener('input', function() {
                // Clear errors when user types
                this.classList.remove('is-invalid');
                if (questionEl) {
                    questionEl.classList.remove('invalid');
                }
                // Hide error message
                const errorDiv = this.closest('.form-group')?.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
                // Validate
                validateCaptcha();
            });
            
            input.addEventListener('focus', function() {
                // Clear errors on focus
                this.classList.remove('is-invalid');
                if (questionEl) {
                    questionEl.classList.remove('invalid');
                }
            });
            
            input.addEventListener('blur', function() {
                validateCaptcha();
            });
        } else {
            console.error('CAPTCHA input not found');
        }
    });
})();
</script>
@endpush
