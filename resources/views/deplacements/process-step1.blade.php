@extends('layouts.app')

@section('title', 'Étape 1 - Choisir les Jours')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Étape 1: Choisir le nombre de jours par mois</h1>
            <p class="text-muted mb-0">{{ $entite->name }} - Période {{ $periode->name }}</p>
        </div>
        <a href="{{ route('deplacements.preparer-periode', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Info Alert - Fixed with close button -->
    <div class="alert alert-info alert-dismissible mb-4" role="alert" id="infoAlert" data-no-auto-hide>
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note:</strong> Définissez le nombre total de jours pour le trimestre, puis répartissez-les entre les mois. Le total des mois ne peut pas dépasser le nombre de jours défini.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <form id="step1Form" action="{{ route('deplacements.process-step1', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" method="POST">
        @csrf
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-calendar-check me-2"></i>Définir les jours par mois
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">Agent</th>
                                <th class="px-3 py-3">PPR</th>
                                @foreach($months as $month)
                                    <th class="px-3 py-3 text-center">
                                        {{ \Carbon\Carbon::create($currentYear, $month, 1)->locale('fr')->monthName }}
                                        <br>
                                    </th>
                                @endforeach
                                <th class="px-3 py-3 text-center nombre-jours-column">Nombre de Jours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agentsData as $agentData)
                                @php
                                    $totalSuggested = 0;
                                    foreach($months as $month) {
                                        $totalSuggested += $agentData['months'][$month]['suggested'] ?? 0;
                                    }
                                    // Auto-calculate: use suggested total or max_jours, whichever is smaller
                                    $autoTotal = min($totalSuggested, $agentData['max_jours']);
                                @endphp
                                <tr>
                                    <td class="px-3 py-3">
                                        <div class="fw-semibold">{{ $agentData['user']->fname }} {{ $agentData['user']->lname }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-secondary">{{ $agentData['user']->ppr }}</span>
                                    </td>
                                    @foreach($months as $month)
                                        <td class="px-3 py-3 text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-decrease-month" 
                                                        data-ppr="{{ $agentData['user']->ppr }}"
                                                        data-month="{{ $month }}"
                                                        style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input 
                                                    type="number" 
                                                    name="days[{{ $agentData['user']->ppr }}][{{ $month }}]" 
                                                    class="form-control text-center days-input" 
                                                    value="{{ $agentData['months'][$month]['suggested'] ?? 0 }}"
                                                    min="0" 
                                                    data-ppr="{{ $agentData['user']->ppr }}"
                                                    data-month="{{ $month }}"
                                                    data-available="{{ $agentData['months'][$month]['available'] ?? 0 }}"
                                                    readonly
                                                    style="width: 80px;"
                                                >
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-increase-month" 
                                                        data-ppr="{{ $agentData['user']->ppr }}"
                                                        data-month="{{ $month }}"
                                                        style="width: 32px; height: 32px; padding: 0;">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-3 text-center nombre-jours-column">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-decrease-total" 
                                                    data-ppr="{{ $agentData['user']->ppr }}"
                                                    style="width: 32px; height: 32px; padding: 0;">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input 
                                                type="number" 
                                                name="total_days[{{ $agentData['user']->ppr }}]" 
                                                class="form-control text-center total-days-input" 
                                                value="{{ $autoTotal }}"
                                                min="0" 
                                                max="{{ $agentData['max_jours'] }}"
                                                data-ppr="{{ $agentData['user']->ppr }}"
                                                data-max="{{ $agentData['max_jours'] }}"
                                                readonly
                                                style="width: 80px;"
                                            >
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-increase-total" 
                                                    data-ppr="{{ $agentData['user']->ppr }}"
                                                    style="width: 32px; height: 32px; padding: 0;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-danger" id="btnVider">
                            <i class="fas fa-trash me-2"></i>Vider
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnActualiser">
                            <i class="fas fa-sync me-2"></i>Actualiser
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" id="btnContinueStep2" disabled>
                            <i class="fas fa-arrow-right me-2"></i>Continuer vers l'étape 2
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Style for "Nombre de Jours" column header */
    .nombre-jours-column {
        background-color: #e7f1ff !important;
        border-left: 3px solid #0d6efd !important;
        font-weight: 600;
        position: relative;
    }
    
    thead .nombre-jours-column {
        background-color: #e7f1ff !important;
        color: #0d6efd;
        font-weight: 700;
        text-align: center;
    }
    
    /* Style for number input controls - Blue square buttons */
    .btn-decrease-month,
    .btn-increase-month,
    .btn-decrease-total,
    .btn-increase-total {
        width: 40px !important;
        height: 40px !important;
        padding: 0 !important;
        background-color: #0d6efd !important;
        border: none !important;
        border-radius: 4px;
        color: #fff !important;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .btn-decrease-month:hover,
    .btn-increase-month:hover,
    .btn-decrease-total:hover,
    .btn-increase-total:hover {
        background-color: #0a58ca !important;
        color: #fff !important;
        transform: scale(1.05);
    }
    
    .btn-decrease-month:active,
    .btn-increase-month:active,
    .btn-decrease-total:active,
    .btn-increase-total:active {
        transform: scale(0.95);
    }
    
    .btn-decrease-month i,
    .btn-increase-month i,
    .btn-decrease-total i,
    .btn-increase-total i {
        font-size: 14px;
        font-weight: bold;
    }
    
    /* Style for input fields - White with blue border */
    .days-input,
    .total-days-input {
        background-color: #fff !important;
        border: 2px solid #0d6efd !important;
        border-radius: 4px;
        font-weight: 600;
        color: #333 !important;
        text-align: center;
        width: 100px !important;
        height: 40px;
        padding: 0.5rem;
    }
    
    .days-input:focus,
    .total-days-input:focus {
        border-color: #0a58ca !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        outline: none;
    }
    
    .nombre-jours-column .total-days-input {
        background-color: #fff !important;
        border: 2px solid #0d6efd !important;
        font-weight: 700;
        color: #0d6efd !important;
    }
    
    /* Input group spacing */
    .d-flex.gap-1 {
        gap: 8px !important;
    }
    
    /* Continue button styling */
    #btnContinueStep2 {
        background-color: #0d6efd !important;
        border: none !important;
        color: #fff !important;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 16px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    #btnContinueStep2:hover:not(:disabled) {
        background-color: #0a58ca !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }
    
    #btnContinueStep2:disabled {
        background-color: #6c757d !important;
        opacity: 0.6;
        cursor: not-allowed !important;
    }
    
    #btnContinueStep2 i {
        font-size: 14px;
    }
    
    /* Table cell padding for better spacing */
    td.px-3.py-3 {
        vertical-align: middle;
    }
    
    /* Remove default button outline styles */
    .btn:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Ensure input groups are properly centered */
    .d-flex.align-items-center.justify-content-center {
        flex-wrap: nowrap;
    }
    
    /* Better visual feedback for invalid inputs */
    .days-input.is-invalid,
    .total-days-input.is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
    }
    
    /* Card footer button alignment */
    .card-footer .d-flex {
        align-items: center;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('step1Form');
    
    // Get total days input and month inputs for a user
    function getTotalDaysInput(ppr) {
        return document.querySelector(`input.total-days-input[data-ppr="${ppr}"]`);
    }
    
    function getMonthInputs(ppr) {
        return document.querySelectorAll(`input.days-input[data-ppr="${ppr}"]`);
    }
    
    function getTotalDays(ppr) {
        const totalInput = getTotalDaysInput(ppr);
        return parseInt(totalInput?.value || 0);
    }
    
    function getMonthTotal(ppr) {
        const monthInputs = getMonthInputs(ppr);
        let total = 0;
        monthInputs.forEach(input => {
            total += parseInt(input.value || 0);
        });
        return total;
    }
    
    function updateMonthTotal(ppr) {
        const total = getMonthTotal(ppr);
        const monthInputs = getMonthInputs(ppr);
        const totalInput = getTotalDaysInput(ppr);
        
        if (!totalInput) return;
        
        const maxJours = parseInt(totalInput.dataset.max || 0);
        
        // Update "Nombre de Jours" to match the sum of months (but not exceed max_jours)
        // Only update if not being updated from button click on "Nombre de Jours" itself
        if (!totalInput.hasAttribute('data-updating-from-button')) {
            const newTotalDays = Math.min(total, maxJours);
            totalInput.setAttribute('data-updating-from-month-change', 'true');
            totalInput.value = newTotalDays;
            totalInput.removeAttribute('data-updating-from-month-change');
        }
        
        const totalDays = getTotalDays(ppr);
        
        // Update visual feedback - show red if sum exceeds "Nombre de Jours" or max_jours
        monthInputs.forEach(input => {
            if (total > totalDays || total > maxJours) {
                input.classList.add('is-invalid');
                input.style.borderColor = '#dc3545';
                input.style.backgroundColor = '#fff5f5';
            } else {
                input.classList.remove('is-invalid');
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        });
        
        // Update total input visual feedback
        if (total > maxJours) {
            totalInput.classList.add('is-invalid');
            totalInput.style.borderColor = '#dc3545';
            totalInput.style.backgroundColor = '#fff5f5';
        } else if (total > totalDays) {
            totalInput.classList.add('is-invalid');
            totalInput.style.borderColor = '#dc3545';
            totalInput.style.backgroundColor = '#fff5f5';
        } else {
            totalInput.classList.remove('is-invalid');
            totalInput.style.borderColor = '';
            totalInput.style.backgroundColor = '';
        }
        
        // Update submit button state
        validateForm();
    }
    
    // Function to validate the entire form
    function validateForm() {
        const btnContinue = document.getElementById('btnContinueStep2');
        if (!btnContinue) return;
        
        // Don't change button state if form is being submitted (user clicked continue)
        if (btnContinue.hasAttribute('data-submitting')) {
            return;
        }
        
        let hasError = false;
        
        // Check all agents
        document.querySelectorAll('.total-days-input').forEach(totalInput => {
            const ppr = totalInput.dataset.ppr;
            const totalDays = parseInt(totalInput.value || 0);
            const maxJours = parseInt(totalInput.dataset.max);
            const monthTotal = getMonthTotal(ppr);
            const monthInputs = getMonthInputs(ppr);
            
            // Check if total days exceeds max
            if (totalDays > maxJours) {
                hasError = true;
            }
            
            // Check if month total exceeds total days
            if (monthTotal > totalDays) {
                hasError = true;
            }
            
            // Check if any month input exceeds available days
            monthInputs.forEach(input => {
                const value = parseInt(input.value || 0);
                const available = parseInt(input.dataset.available || 0);
                if (value > available) {
                    hasError = true;
                }
            });
        });
        
        // Enable/disable button
        if (hasError) {
            btnContinue.disabled = true;
            btnContinue.classList.add('opacity-50');
            btnContinue.style.cursor = 'not-allowed';
        } else {
            btnContinue.disabled = false;
            btnContinue.classList.remove('opacity-50');
            btnContinue.style.cursor = 'pointer';
        }
    }
    
    // Auto-generate values on page load
    function autoGenerateValues() {
        document.querySelectorAll('.total-days-input').forEach(totalInput => {
            const ppr = totalInput.dataset.ppr;
            const monthInputs = getMonthInputs(ppr);
            const totalDays = getTotalDays(ppr);
            let currentMonthTotal = getMonthTotal(ppr);
            
            // If month total exceeds total days, adjust months
            if (currentMonthTotal > totalDays) {
                adjustMonthsToFit(ppr, totalDays);
            }
        });
    }
    
    // Handle total days increase/decrease (prevent duplicate listeners)
    document.querySelectorAll('.btn-increase-total').forEach(btn => {
        if (!btn.hasAttribute('data-listener-added')) {
            btn.setAttribute('data-listener-added', 'true');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const ppr = this.dataset.ppr;
                const totalInput = getTotalDaysInput(ppr);
                const maxJours = parseInt(totalInput.dataset.max);
                const current = parseInt(totalInput.value || 0);
                
                if (current < maxJours) {
                    const newTotal = current + 1;
                    totalInput.setAttribute('data-updating-from-button', 'true');
                    totalInput.value = newTotal;
                    
                    // Adjust months to match the new total
                    adjustMonthsToMatchTotal(ppr, newTotal);
                    
                    updateMonthTotal(ppr);
                }
                return false;
            });
        }
    });
    
    document.querySelectorAll('.btn-decrease-total').forEach(btn => {
        if (!btn.hasAttribute('data-listener-added')) {
            btn.setAttribute('data-listener-added', 'true');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const ppr = this.dataset.ppr;
                const totalInput = getTotalDaysInput(ppr);
                const current = parseInt(totalInput.value || 0);
                const monthTotal = getMonthTotal(ppr);
                
                if (current > 0) {
                    const newTotal = current - 1;
                    totalInput.setAttribute('data-updating-from-button', 'true');
                    totalInput.value = newTotal;
                    
                    // Always adjust months to match the new total
                    adjustMonthsToMatchTotal(ppr, newTotal);
                    
                    updateMonthTotal(ppr);
                }
                return false;
            });
        }
    });
    
    // Handle month days increase/decrease (prevent duplicate listeners)
    document.querySelectorAll('.btn-increase-month').forEach(btn => {
        if (!btn.hasAttribute('data-listener-added')) {
            btn.setAttribute('data-listener-added', 'true');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const ppr = this.dataset.ppr;
                const month = this.dataset.month;
                const monthInput = document.querySelector(`input.days-input[data-ppr="${ppr}"][data-month="${month}"]`);
                const available = parseInt(monthInput.dataset.available);
                const current = parseInt(monthInput.value || 0);
                
                // Only check if we don't exceed available days for this month
                // Allow exceeding total "Nombre de Jours" (will show red)
                if (current < available) {
                    monthInput.setAttribute('data-updating-from-button', 'true');
                    monthInput.value = current + 1;
                    updateMonthTotal(ppr);
                }
                return false;
            });
        }
    });
    
    document.querySelectorAll('.btn-decrease-month').forEach(btn => {
        if (!btn.hasAttribute('data-listener-added')) {
            btn.setAttribute('data-listener-added', 'true');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const ppr = this.dataset.ppr;
                const month = this.dataset.month;
                const monthInput = document.querySelector(`input.days-input[data-ppr="${ppr}"][data-month="${month}"]`);
                const current = parseInt(monthInput.value || 0);
                
                if (current > 0) {
                    monthInput.setAttribute('data-updating-from-button', 'true');
                    monthInput.value = current - 1;
                    updateMonthTotal(ppr);
                }
                return false;
            });
        }
    });
    
    // Adjust months to fit within total
    function adjustMonthsToFit(ppr, maxTotal) {
        const monthInputs = Array.from(getMonthInputs(ppr));
        let currentTotal = getMonthTotal(ppr);
        
        if (currentTotal <= maxTotal) {
            return;
        }
        
        // Reduce months starting from the last one
        for (let i = monthInputs.length - 1; i >= 0 && currentTotal > maxTotal; i--) {
            const input = monthInputs[i];
            const current = parseInt(input.value || 0);
            const reduction = Math.min(current, currentTotal - maxTotal);
            
            if (reduction > 0) {
                input.setAttribute('data-updating-from-button', 'true');
                input.value = current - reduction;
                currentTotal -= reduction;
            }
        }
    }
    
    // Adjust months to match exactly the total (distribute or reduce as needed)
    function adjustMonthsToMatchTotal(ppr, targetTotal) {
        const monthInputs = Array.from(getMonthInputs(ppr));
        let currentTotal = getMonthTotal(ppr);
        
        if (currentTotal === targetTotal) {
            return; // Already matches
        }
        
        if (currentTotal > targetTotal) {
            // Need to reduce - reduce from last month first
            let toReduce = currentTotal - targetTotal;
            for (let i = monthInputs.length - 1; i >= 0 && toReduce > 0; i--) {
                const input = monthInputs[i];
                const current = parseInt(input.value || 0);
                const reduction = Math.min(current, toReduce);
                
                if (reduction > 0) {
                    input.setAttribute('data-updating-from-button', 'true');
                    input.value = current - reduction;
                    toReduce -= reduction;
                }
            }
        } else {
            // Need to increase - distribute proportionally based on available days
            let toAdd = targetTotal - currentTotal;
            const monthData = monthInputs.map(input => ({
                input: input,
                current: parseInt(input.value || 0),
                available: parseInt(input.dataset.available || 0),
                canAdd: Math.min(parseInt(input.dataset.available || 0) - parseInt(input.value || 0), toAdd)
            })).filter(data => data.canAdd > 0);
            
            // Distribute evenly across available months
            while (toAdd > 0 && monthData.length > 0) {
                const perMonth = Math.max(1, Math.floor(toAdd / monthData.length));
                
                for (let i = 0; i < monthData.length && toAdd > 0; i++) {
                    const data = monthData[i];
                    const add = Math.min(perMonth, data.canAdd, toAdd);
                    
                    if (add > 0) {
                        data.input.setAttribute('data-updating-from-button', 'true');
                        data.input.value = data.current + add;
                        toAdd -= add;
                        data.current += add;
                        data.canAdd -= add;
                        
                        // Remove if can't add more
                        if (data.canAdd <= 0) {
                            monthData.splice(i, 1);
                            i--;
                        }
                    }
                }
                
                // If still have toAdd but no months can take more, break
                if (monthData.length === 0 || monthData.every(d => d.canAdd === 0)) {
                    break;
                }
            }
        }
    }
    
    // Form validation and confirmation (prevent duplicate listeners)
    if (form && !form.hasAttribute('data-submit-listener-added')) {
        form.setAttribute('data-submit-listener-added', 'true');
        form.addEventListener('submit', function(e) {
            // Prevent multiple submissions and duplicate confirmations
            if (this.hasAttribute('data-processing-submit')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            const btnContinue = document.getElementById('btnContinueStep2');
            
            // Mark form as processing to prevent duplicate confirmations
            this.setAttribute('data-processing-submit', 'true');
            
            // Prevent form submission - we'll handle it after confirmation
            e.preventDefault();
            e.stopPropagation();
            
            // Validate form first
            let hasError = false;
            const errors = [];
            
            document.querySelectorAll('.total-days-input').forEach(totalInput => {
                const ppr = totalInput.dataset.ppr;
                const totalDays = parseInt(totalInput.value || 0);
                const maxJours = parseInt(totalInput.dataset.max);
                const monthTotal = getMonthTotal(ppr);
                const monthInputs = getMonthInputs(ppr);
                
                if (totalDays > maxJours) {
                    hasError = true;
                    errors.push(`Agent ${ppr}: Le nombre de jours (${totalDays}) dépasse le maximum autorisé (${maxJours})`);
                }
                
                if (monthTotal > totalDays) {
                    hasError = true;
                    errors.push(`Agent ${ppr}: Le total des mois (${monthTotal}) dépasse le nombre de jours défini (${totalDays})`);
                }
                
                // Check if any month input exceeds available days
                monthInputs.forEach(input => {
                    const value = parseInt(input.value || 0);
                    const available = parseInt(input.dataset.available || 0);
                    if (value > available) {
                        hasError = true;
                        errors.push(`Agent ${ppr}: Le nombre de jours pour un mois dépasse les jours disponibles`);
                    }
                });
            });
            
            if (hasError) {
                // Remove processing flag on error
                form.removeAttribute('data-processing-submit');
                if (btnContinue) {
                    btnContinue.removeAttribute('data-submitting');
                }
                
                // Show alert with errors
                alert('Erreurs de validation:\n\n' + errors.join('\n'));
                
                // Re-validate form to restore button state
                validateForm();
                return false;
            }
            
            // Show confirmation dialog
            if (confirm('Êtes-vous sûr de vouloir continuer vers l\'étape 2 ?\n\nLes valeurs seront enregistrées et vous passerez à la sélection des dates.')) {
                // User confirmed - submit the form
                if (btnContinue) {
                    btnContinue.setAttribute('data-submitting', 'true');
                }
                form.submit();
            } else {
                // User cancelled - remove processing flag
                form.removeAttribute('data-processing-submit');
                if (btnContinue) {
                    btnContinue.removeAttribute('data-submitting');
                }
                // Re-validate form to restore button state
                validateForm();
            }
            
            return false;
        });
    }
    
    // Add event listeners to all inputs to validate on change (only for direct input, not button clicks)
    document.addEventListener('input', function(e) {
        // Only handle if it's a direct input change (not from button clicks)
        if (e.target.classList.contains('total-days-input')) {
            // Skip if updating from month change or button click
            if (e.target.hasAttribute('data-updating-from-month-change')) {
                e.target.removeAttribute('data-updating-from-month-change');
                return;
            }
            if (!e.target.hasAttribute('data-updating-from-button')) {
                const ppr = e.target.dataset.ppr;
                if (ppr) {
                    const newTotal = parseInt(e.target.value || 0);
                    // Adjust months to match the new total when user directly types in "Nombre de Jours"
                    adjustMonthsToMatchTotal(ppr, newTotal);
                    updateMonthTotal(ppr);
                }
            } else {
                // Remove the flag after handling
                e.target.removeAttribute('data-updating-from-button');
            }
        } else if (e.target.classList.contains('days-input')) {
            // For month inputs, update "Nombre de Jours" to match the sum
            // But skip if updating from button (Vider/Actualiser) - we want to keep "Nombre de Jours" unchanged
            if (!e.target.hasAttribute('data-updating-from-button')) {
                const ppr = e.target.dataset.ppr;
                if (ppr) {
                    updateMonthTotal(ppr);
                }
            }
            // Note: We don't remove the flag here - it will be removed after all inputs are processed
        }
    });
    
    // Vider button - Clear all month inputs to 0, but keep "Nombre de Jours" unchanged
    const btnVider = document.getElementById('btnVider');
    if (btnVider && !btnVider.hasAttribute('data-listener-added')) {
        btnVider.setAttribute('data-listener-added', 'true');
        btnVider.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Set all month inputs to 0, but keep "Nombre de Jours" unchanged
            document.querySelectorAll('.days-input').forEach(input => {
                input.setAttribute('data-updating-from-button', 'true');
                input.value = 0;
            });
            
            // Remove flags after a short delay to allow all inputs to be processed
            setTimeout(() => {
                document.querySelectorAll('.days-input').forEach(input => {
                    input.removeAttribute('data-updating-from-button');
                });
            }, 100);
            
            // Don't update "Nombre de Jours" - it should stay the same
            // Just validate the form
            validateForm();
            return false;
        });
    }
    
    // Actualiser button - Regenerate month values randomly based on current "Nombre de Jours"
    const btnActualiser = document.getElementById('btnActualiser');
    if (btnActualiser && !btnActualiser.hasAttribute('data-listener-added')) {
        btnActualiser.setAttribute('data-listener-added', 'true');
        btnActualiser.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.total-days-input').forEach(totalInput => {
                    const ppr = totalInput.dataset.ppr;
                    const totalDays = parseInt(totalInput.value || 0);
                    const monthInputs = Array.from(getMonthInputs(ppr));
                    
                    if (monthInputs.length === 0 || totalDays === 0) {
                        return;
                    }
                    
                    // Get available days for each month
                    const monthData = monthInputs.map(input => ({
                        input: input,
                        available: parseInt(input.dataset.available || 0),
                        month: parseInt(input.dataset.month || 0)
                    }));
                    
                    // Generate random distribution that sums to totalDays
                    // Simple approach: randomly distribute totalDays across months
                    let remaining = totalDays;
                    const values = new Array(monthData.length).fill(0);
                    
                    // Generate random values for each month (except last)
                    for (let i = 0; i < monthData.length - 1; i++) {
                        const data = monthData[i];
                        const remainingMonths = monthData.length - i - 1;
                        
                        // Calculate max for this month (don't leave remaining months with 0 if possible)
                        const maxForThisMonth = Math.min(
                            data.available,
                            remaining - remainingMonths // Ensure we can give at least 1 to each remaining month
                        );
                        
                        // Generate random value between 0 and maxForThisMonth
                        if (maxForThisMonth > 0 && remaining > remainingMonths) {
                            values[i] = Math.floor(Math.random() * (maxForThisMonth + 1));
                        } else {
                            values[i] = 0;
                        }
                        
                        remaining -= values[i];
                    }
                    
                    // Last month gets all remaining (capped at available)
                    const lastIndex = monthData.length - 1;
                    values[lastIndex] = Math.min(monthData[lastIndex].available, Math.max(0, remaining));
                    remaining -= values[lastIndex];
                    
                    // If there's still remaining, distribute it randomly to months that can take more
                    if (remaining > 0) {
                        const availableIndices = [];
                        for (let i = 0; i < monthData.length; i++) {
                            const canAdd = Math.min(remaining, monthData[i].available - values[i]);
                            if (canAdd > 0) {
                                availableIndices.push(i);
                            }
                        }
                        
                        while (remaining > 0 && availableIndices.length > 0) {
                            const randomIdx = Math.floor(Math.random() * availableIndices.length);
                            const monthIdx = availableIndices[randomIdx];
                            const canAdd = Math.min(remaining, monthData[monthIdx].available - values[monthIdx]);
                            
                            if (canAdd > 0) {
                                const add = Math.min(remaining, canAdd);
                                values[monthIdx] += add;
                                remaining -= add;
                                
                                // Remove if can't add more
                                if (values[monthIdx] >= monthData[monthIdx].available) {
                                    availableIndices.splice(randomIdx, 1);
                                }
                            } else {
                                availableIndices.splice(randomIdx, 1);
                            }
                        }
                    }
                    
                    // If we have negative remaining (over-allocated), reduce from months
                    if (remaining < 0) {
                        let toReduce = Math.abs(remaining);
                        for (let i = monthData.length - 1; i >= 0 && toReduce > 0; i--) {
                            const reduce = Math.min(toReduce, values[i]);
                            values[i] -= reduce;
                            toReduce -= reduce;
                        }
                    }
                    
                    // Apply the generated values
                    monthData.forEach((data, index) => {
                        const newValue = Math.max(0, Math.min(data.available, values[index] || 0));
                        data.input.setAttribute('data-updating-from-button', 'true');
                        data.input.value = newValue;
                    });
                    
                    // Remove flags after a short delay to allow all inputs to be processed
                    setTimeout(() => {
                        monthData.forEach((data) => {
                            data.input.removeAttribute('data-updating-from-button');
                        });
                    }, 100);
                    
                    // Don't update "Nombre de Jours" - it should stay the same
                    // The sum of months might be less than "Nombre de Jours", which is fine
                });
                
            // Validate form after regenerating
            validateForm();
            return false;
        });
    }
    
    // Initialize: auto-generate and validate
    autoGenerateValues();
    document.querySelectorAll('.total-days-input').forEach(totalInput => {
        const ppr = totalInput.dataset.ppr;
        updateMonthTotal(ppr);
    });
    
    // Initial validation
    validateForm();
});
</script>
@endpush
@endsection

