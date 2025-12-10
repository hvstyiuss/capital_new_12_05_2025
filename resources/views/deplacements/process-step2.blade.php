@extends('layouts.app')

@section('title', 'Étape 2 - Choisir les Dates')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Étape 2: Choisir les dates de début</h1>
            <p class="text-muted mb-0">{{ $entite->name }} - Période {{ $periode->name }}</p>
        </div>
        <a href="{{ route('deplacements.start-process', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Info Alert - Fixed with close button -->
    <div class="alert alert-warning alert-dismissible mb-4" role="alert" id="infoAlert" data-no-auto-hide>
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Important:</strong> Les dates de congés, jours fériés et weekends sont automatiquement bloquées. La date de fin est calculée automatiquement et ne peut pas être modifiée.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <form id="step2Form" action="{{ route('deplacements.finalize', ['type' => $type, 'periode' => $periode->id, 'entite' => $entite->id]) }}" method="POST">
        @csrf
        
        @foreach($agentsData as $agentData)
            @if(count($agentData['months']) > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-user me-2"></i>{{ $agentData['user']->fname }} {{ $agentData['user']->lname }}
                            <span class="badge bg-secondary ms-2">{{ $agentData['user']->ppr }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-3 py-3">Mois</th>
                                        <th class="px-3 py-3">Nombre de jours</th>
                                        <th class="px-3 py-3">Date de début *</th>
                                        <th class="px-3 py-3">Date de fin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agentData['months'] as $month => $monthData)
                                        @php
                                            $monthName = \Carbon\Carbon::create($currentYear, $month, 1)->locale('fr')->monthName;
                                            $blockedDates = $monthData['blocked_dates'] ?? [];
                                            $days = $monthData['days'] ?? 0;
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-3">
                                                <strong>{{ $monthName }}</strong>
                                            </td>
                                            <td class="px-3 py-3">
                                                <span class="badge bg-primary">{{ $days }} jours</span>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="position-relative">
                                                    <input 
                                                        type="text" 
                                                        name="dates[{{ $agentData['user']->ppr }}][{{ $month }}][date_debut_display]" 
                                                        class="form-control date-debut-display" 
                                                        readonly
                                                        placeholder="Cliquez pour sélectionner une date"
                                                        data-ppr="{{ $agentData['user']->ppr }}"
                                                        data-month="{{ $month }}"
                                                        style="cursor: pointer; background-color: white;"
                                                    >
                                                    <input 
                                                        type="hidden" 
                                                        name="dates[{{ $agentData['user']->ppr }}][{{ $month }}][date_debut]" 
                                                        class="date-debut-input" 
                                                        required
                                                        data-ppr="{{ $agentData['user']->ppr }}"
                                                        data-month="{{ $month }}"
                                                        data-days="{{ $days }}"
                                                        data-year="{{ $currentYear }}"
                                                        data-blocked='@json($blockedDates)'
                                                    >
                                                    <div class="position-absolute end-0 top-50 translate-middle-y pe-3" style="pointer-events: none;">
                                                        <i class="fas fa-calendar-alt text-muted"></i>
                                                    </div>
                                                    <!-- Custom Calendar Picker -->
                                                    <div class="custom-calendar-picker bg-white border rounded shadow-lg p-3" 
                                                         style="display: none; min-width: 320px;"
                                                         data-ppr="{{ $agentData['user']->ppr }}"
                                                         data-month="{{ $month }}">
                                                        <div class="calendar-container-{{ $agentData['user']->ppr }}-{{ $month }}"></div>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    Jours bloqués: {{ count($blockedDates) }} (congés, fériés, weekends)
                                                </small>
                                            </td>
                                            <td class="px-3 py-3">
                                                <input 
                                                    type="date" 
                                                    class="form-control date-fin-input" 
                                                    readonly
                                                    data-ppr="{{ $agentData['user']->ppr }}"
                                                    data-month="{{ $month }}"
                                                    style="background-color: #e9ecef;"
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="card border-0 shadow-sm">
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check me-2"></i>Finaliser
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Ensure calendar picker appears above everything */
    .custom-calendar-picker {
        z-index: 9999 !important;
        position: fixed !important;
        background: white !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
    }
    
    /* Ensure parent containers don't clip the calendar */
    .card-body {
        overflow: visible !important;
    }
    
    .table-responsive {
        overflow: visible !important;
    }
    
    /* Custom Calendar Styles - Same as avis de départ */
    [class^="calendar-container-"] {
        width: 100%;
        max-width: 350px;
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .calendar-month-year {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .calendar-nav-btn {
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        background: #f3f4f6;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .calendar-nav-btn:hover {
        background: #e5e7eb;
        transform: scale(1.1);
    }
    
    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.25rem;
        margin-bottom: 0.5rem;
    }
    
    .calendar-weekday {
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        padding: 0.5rem 0;
    }
    
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.25rem;
    }
    
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    
    .calendar-day.other-month {
        color: #d1d5db;
        background: #f9fafb;
    }
    
    .calendar-day.weekend {
        background: #fee2e2;
        color: #991b1b;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .calendar-day.holiday {
        background: #fed7aa;
        color: #9a3412;
        cursor: not-allowed;
        opacity: 0.7;
        position: relative;
    }
    
    .calendar-day.holiday::after {
        content: '🚫';
        position: absolute;
        font-size: 0.625rem;
        top: 2px;
        right: 2px;
    }
    
    .calendar-day.blocked {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.5;
    }
    
    .calendar-day.available {
        background: #ffffff;
        color: #1f2937;
        border: 1px solid #e5e7eb;
    }
    
    .calendar-day.available:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        transform: scale(1.1);
    }
    
    .calendar-day.today {
        background: #dbeafe;
        border: 2px solid #3b82f6;
        font-weight: 700;
    }
    
    .calendar-day.selected {
        background: #3b82f6;
        color: white;
        font-weight: 700;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebutDisplays = document.querySelectorAll('.date-debut-display');
    const dateDebutInputs = document.querySelectorAll('.date-debut-input');
    const form = document.getElementById('step2Form');
    
    // Helper functions
    function isWeekend(date) {
        const day = date.getDay();
        return day === 0 || day === 6; // Sunday or Saturday
    }
    
    function isDateBlocked(date, blockedDates) {
        const dateStr = formatDateForInput(date);
        return blockedDates.includes(dateStr);
    }
    
    function formatDateForInput(date) {
        if (!date) return '';
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function formatDateForDisplay(date) {
        if (!date) return '';
        const d = new Date(date);
        return d.toLocaleDateString('fr-FR', { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        });
    }
    
    // Function to calculate date fin
    function calculateDateFin(dateDebut, days, blockedDates) {
        if (!dateDebut || !days || days <= 0) {
            return null;
        }
        
        const start = new Date(dateDebut);
        let count = 0;
        let current = new Date(start);
        
        // Count working days (excluding weekends and blocked dates)
        while (count < days) {
            if (!isWeekend(current) && !isDateBlocked(current, blockedDates)) {
                count++;
            }
            if (count < days) {
                current.setDate(current.getDate() + 1);
            }
        }
        
        return current;
    }
    
    // Initialize calendars for each date input
    dateDebutDisplays.forEach(dateDebutDisplay => {
        const ppr = dateDebutDisplay.dataset.ppr;
        const month = parseInt(dateDebutDisplay.dataset.month);
        const calendarPicker = document.querySelector(`.custom-calendar-picker[data-ppr="${ppr}"][data-month="${month}"]`);
        const dateDebutInput = document.querySelector(`.date-debut-input[data-ppr="${ppr}"][data-month="${month}"]`);
        const dateFinInput = document.querySelector(`.date-fin-input[data-ppr="${ppr}"][data-month="${month}"]`);
        
        if (!calendarPicker || !dateDebutInput) return;
        
        const blockedDates = JSON.parse(dateDebutInput.dataset.blocked || '[]');
        const days = parseInt(dateDebutInput.dataset.days);
        const year = parseInt(dateDebutInput.dataset.year);
        
        let currentMonth = month - 1; // JavaScript months are 0-indexed
        let currentYear = year;
        let selectedDate = null;
        
        const calendarContainer = calendarPicker.querySelector(`.calendar-container-${ppr}-${month}`);
        
        // Show/hide calendar on click
        dateDebutDisplay.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close other calendars
            document.querySelectorAll('.custom-calendar-picker').forEach(cal => {
                if (cal !== calendarPicker) {
                    cal.style.display = 'none';
                }
            });
            
            // Calculate position for fixed positioning
            const rect = dateDebutDisplay.getBoundingClientRect();
            calendarPicker.style.position = 'fixed';
            calendarPicker.style.top = (rect.bottom + window.scrollY + 5) + 'px';
            calendarPicker.style.left = rect.left + 'px';
            calendarPicker.style.zIndex = '9999';
            calendarPicker.style.display = 'block';
            
            renderCalendar();
        });
        
        // Close calendar when clicking outside
        document.addEventListener('click', function(e) {
            if (!calendarPicker.contains(e.target) && !dateDebutDisplay.contains(e.target)) {
                calendarPicker.style.display = 'none';
            }
        });
        
        function renderCalendar() {
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                               'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            const weekDays = ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'];
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            let html = `
                <div class="calendar-header">
                    <div class="calendar-month-year">
                        <select class="border-none bg-transparent font-semibold text-gray-700 cursor-pointer calendar-month-select">
                            ${monthNames.map((monthName, idx) => 
                                `<option value="${idx}" ${idx === currentMonth ? 'selected' : ''}>${monthName}</option>`
                            ).join('')}
                        </select>
                        <select class="border-none bg-transparent font-semibold text-gray-700 cursor-pointer calendar-year-select">
                            ${[year - 1, year, year + 1].map(y => 
                                `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div class="flex gap-1">
                        <button class="calendar-nav-btn calendar-prev" title="Mois précédent">
                            <i class="fas fa-chevron-up text-xs"></i>
                        </button>
                        <button class="calendar-nav-btn calendar-next" title="Mois suivant">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                    </div>
                </div>
                <div class="calendar-weekdays">
                    ${weekDays.map(day => `<div class="calendar-weekday">${day}</div>`).join('')}
                </div>
                <div class="calendar-days">
            `;
            
            // Add empty cells for days before the first day of the month
            for (let i = 0; i < startingDayOfWeek; i++) {
                const prevMonthDate = new Date(currentYear, currentMonth, -i);
                const dateString = formatDateForInput(prevMonthDate);
                const isWeekendDay = isWeekend(prevMonthDate);
                const isBlocked = isDateBlocked(prevMonthDate, blockedDates);
                
                html += `<div class="calendar-day other-month ${isWeekendDay ? 'weekend' : ''} ${isBlocked ? 'blocked' : ''}">${prevMonthDate.getDate()}</div>`;
            }
            
            // Add days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(currentYear, currentMonth, day);
                date.setHours(0, 0, 0, 0);
                const dateString = formatDateForInput(date);
                const isWeekendDay = isWeekend(date);
                const isBlocked = isDateBlocked(date, blockedDates);
                const isToday = date.toDateString() === today.toDateString();
                const isSelected = selectedDate && dateString === formatDateForInput(selectedDate);
                const isInTargetMonth = (currentMonth + 1) === month && currentYear === year;
                
                let classes = 'calendar-day';
                
                if (isSelected) {
                    classes += ' selected';
                } else if (isToday) {
                    classes += ' today';
                } else if (isWeekendDay) {
                    classes += ' weekend';
                } else if (isBlocked) {
                    classes += ' blocked';
                } else if (!isInTargetMonth) {
                    classes += ' other-month';
                } else {
                    classes += ' available';
                }
                
                const isClickable = !isWeekendDay && !isBlocked && isInTargetMonth;
                
                let title = '';
                if (isBlocked) {
                    title = 'Date bloquée (congé, jour férié ou weekend)';
                } else if (isWeekendDay) {
                    title = 'Weekend';
                } else if (!isInTargetMonth) {
                    title = 'Hors du mois cible';
                } else {
                    title = 'Disponible';
                }
                
                html += `<div class="${classes}" data-date="${dateString}" ${isClickable ? `onclick="selectDate${ppr}${month}('${dateString}')"` : ''} title="${title}">${day}</div>`;
            }
            
            // Add empty cells for days after the last day of the month
            const remainingCells = 42 - (startingDayOfWeek + daysInMonth);
            for (let day = 1; day <= remainingCells && day <= 14; day++) {
                const nextMonthDate = new Date(currentYear, currentMonth + 1, day);
                const dateString = formatDateForInput(nextMonthDate);
                const isWeekendDay = isWeekend(nextMonthDate);
                const isBlocked = isDateBlocked(nextMonthDate, blockedDates);
                
                html += `<div class="calendar-day other-month ${isWeekendDay ? 'weekend' : ''} ${isBlocked ? 'blocked' : ''}">${nextMonthDate.getDate()}</div>`;
            }
            
            html += '</div></div>';
            calendarContainer.innerHTML = html;
            
            // Add event listeners
            const monthSelect = calendarContainer.querySelector('.calendar-month-select');
            const yearSelect = calendarContainer.querySelector('.calendar-year-select');
            const prevBtn = calendarContainer.querySelector('.calendar-prev');
            const nextBtn = calendarContainer.querySelector('.calendar-next');
            
            if (monthSelect) {
                monthSelect.addEventListener('change', function() {
                    currentMonth = parseInt(this.value);
                    renderCalendar();
                });
            }
            
            if (yearSelect) {
                yearSelect.addEventListener('change', function() {
                    currentYear = parseInt(this.value);
                    renderCalendar();
                });
            }
            
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    renderCalendar();
                });
            }
            
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                    renderCalendar();
                });
            }
        }
        
        // Select date function
        window[`selectDate${ppr}${month}`] = function(dateString) {
            const date = new Date(dateString);
            const blockedDates = JSON.parse(dateDebutInput.dataset.blocked || '[]');
            
            // Validate date
            if (isDateBlocked(date, blockedDates) || isWeekend(date)) {
                alert('Cette date est bloquée (congé, jour férié ou weekend). Veuillez choisir une autre date.');
                return;
            }
            
            if (date.getMonth() + 1 !== month || date.getFullYear() !== year) {
                alert('La date doit être dans le mois sélectionné.');
                return;
            }
            
            selectedDate = date;
            dateDebutInput.value = dateString;
            dateDebutDisplay.value = formatDateForDisplay(date);
            calendarPicker.style.display = 'none';
            
            // Calculate and set date fin
            if (dateFinInput && days > 0) {
                const dateFin = calculateDateFin(date, days, blockedDates);
                if (dateFin) {
                    dateFinInput.value = formatDateForInput(dateFin);
                }
            }
        };
        
        // Initialize selected date from hidden input if it exists
        if (dateDebutInput.value) {
            selectedDate = new Date(dateDebutInput.value);
            dateDebutDisplay.value = formatDateForDisplay(selectedDate);
        }
    });
    
    // Form validation and confirmation
    let isSubmitting = false;
    let confirmationShown = false;
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Check if listener is already attached
    if (!form.hasAttribute('data-submit-listener-added')) {
        form.setAttribute('data-submit-listener-added', 'true');
        
        const formSubmitHandler = function(e) {
            // If already submitting (after confirmation), allow default behavior
            if (isSubmitting) {
                // Don't prevent default - let the form submit naturally
                return true;
            }
            
            // Prevent form submission - we'll handle it after confirmation
            e.preventDefault();
            e.stopPropagation();
            
            // Prevent duplicate confirmation dialogs
            if (confirmationShown) {
                return false;
            }
            
            // Validate first
            let hasError = false;
            const errors = [];
            
            dateDebutInputs.forEach(input => {
                if (!input.value) {
                    hasError = true;
                    errors.push(`Veuillez sélectionner une date de début pour tous les mois.`);
                } else {
                    const selectedDate = new Date(input.value);
                    const blockedDates = JSON.parse(input.dataset.blocked || '[]');
                    
                    if (isDateBlocked(selectedDate, blockedDates) || isWeekend(selectedDate)) {
                        hasError = true;
                        errors.push(`La date sélectionnée est bloquée.`);
                    }
                }
            });
            
            if (hasError) {
                alert('Veuillez corriger les erreurs:\n' + [...new Set(errors)].join('\n'));
                return false;
            }
            
            // Mark that confirmation is being shown
            confirmationShown = true;
            
            // Show confirmation dialog
            if (confirm('Êtes-vous sûr de vouloir finaliser le processus de déplacement ?\n\nLes dates seront enregistrées et le processus sera terminé.')) {
                // User confirmed - set flag to allow submission
                isSubmitting = true;
                confirmationShown = false; // Reset for potential retry
                
                // Remove the event listener first
                form.removeEventListener('submit', formSubmitHandler);
                form.removeAttribute('data-submit-listener-added');
                
                // Submit the form using requestSubmit (respects HTML5 validation) or fallback to submit()
                setTimeout(function() {
                    if (typeof form.requestSubmit === 'function') {
                        // requestSubmit() respects HTML5 validation and triggers submit event
                        form.requestSubmit();
                    } else {
                        // Fallback: use native submit (bypasses submit event but submits the form)
                        form.submit();
                    }
                }, 50);
            } else {
                // User cancelled - reset the flag
                confirmationShown = false;
            }
            
            return false;
        };
        
        form.addEventListener('submit', formSubmitHandler);
    }
});
</script>
@endpush
@endsection

