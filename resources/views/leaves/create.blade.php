@extends('layouts.app')

@section('title', 'Avis de Départ - Demande de Congé')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Avis de Départ</h1>
                <p class="text-muted mb-0">Créer une nouvelle demande de congé administratif annuel</p>
            </div>
            <a href="{{ route('hr.leaves.annuel') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('hr.leaves.store') }}" method="POST" id="leaveRequestForm" novalidate>
                        @csrf
                        
                        <!-- User Info (Hidden) -->
                        <input type="hidden" name="ppr" value="{{ auth()->user()->ppr }}">

                        <!-- Date de Départ -->
                        <div class="mb-4">
                            <label for="date_depart" class="form-label fw-semibold mb-2">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                Date de Départ <span class="text-danger">*</span>
                            </label>
                            <div class="position-relative" style="overflow: visible;">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           id="date_depart" 
                                           value="{{ old('date_depart') ? \Carbon\Carbon::parse(old('date_depart'))->locale('fr')->isoFormat('dddd D MMMM YYYY') : '' }}"
                                           readonly
                                           class="form-control form-control-lg @error('date_depart') is-invalid @enderror"
                                           placeholder="Cliquez pour sélectionner une date"
                                           style="cursor: pointer;">
                                    @error('date_depart')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <input type="hidden" id="date_depart_hidden" name="date_depart" value="{{ old('date_depart') }}">
                                
                                <!-- Custom Calendar Picker -->
                                <div id="custom_calendar" class="position-absolute top-100 start-0 mt-2 bg-white border border-2 rounded shadow-lg p-3" style="display: none; min-width: 350px; z-index: 9999; overflow: visible;">
                                    <div class="calendar-container"></div>
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Vous pouvez choisir parmi les 3 derniers jours ouvrables ou n'importe quelle date future (weekends et jours fériés exclus).
                            </small>
                        </div>

                        <!-- Nombre de Jours -->
                        <div class="mb-4">
                            <label for="nb_jours_demandes" class="form-label fw-semibold mb-2">
                                <i class="fas fa-calendar-day me-2 text-primary"></i>
                                Nombre de Jours <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-hashtag text-muted"></i>
                                </span>
                                <input type="number" 
                                       id="nb_jours_demandes" 
                                       name="nb_jours_demandes" 
                                       min="1"
                                       max="22"
                                       value="{{ old('nb_jours_demandes') }}"
                                       class="form-control form-control-lg @error('nb_jours_demandes') is-invalid @enderror"
                                       required>
                                <span class="input-group-text bg-light">jours</span>
                                @error('nb_jours_demandes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Maximum 22 jours. Les weekends et jours fériés ne sont pas comptés.
                            </small>
                        </div>

                        <!-- Date de Retour (Auto-calculated) -->
                        <div class="mb-4">
                            <label for="date_retour_display" class="form-label fw-semibold mb-2">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>
                                Date de Retour <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar text-muted"></i>
                                </span>
                                <div class="form-control form-control-lg bg-light" id="date_retour_display" style="cursor: not-allowed;">
                                    <span class="text-muted">-</span>
                                </div>
                            </div>
                            <input type="hidden" id="date_retour" name="date_retour" value="{{ old('date_retour') }}">
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Calculée automatiquement en excluant les weekends et jours fériés.
                            </small>
                            @error('date_retour')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                            <a href="{{ route('hr.leaves.annuel') }}" 
                               class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" 
                                    id="submitBtn"
                                    class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-paper-plane me-2" id="submitIcon"></i>
                                <span id="submitText">Soumettre la Demande</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom Calendar Styles */
    #custom_calendar {
        overflow: visible !important;
        position: absolute !important;
    }
    
    /* Ensure parent containers don't clip the calendar */
    .card-body {
        overflow: visible !important;
    }
    
    .position-relative {
        overflow: visible !important;
    }
    
    .calendar-container {
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
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .calendar-nav-btn {
        background: #f3f4f6;
        border: none;
        border-radius: 0.5rem;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .calendar-nav-btn:hover {
        background: #e5e7eb;
        transform: scale(1.1);
    }
    
    .calendar-nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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
    
    .calendar-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 2px solid #e5e7eb;
    }
    
    .calendar-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .calendar-btn-clear {
        background: #f3f4f6;
        color: #374151;
    }
    
    .calendar-btn-clear:hover {
        background: #e5e7eb;
    }
    
    .calendar-btn-today {
        background: #3b82f6;
        color: white;
    }
    
    .calendar-btn-today:hover {
        background: #2563eb;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDepart = document.getElementById('date_depart');
    const dateDepartHidden = document.getElementById('date_depart_hidden');
    const dateRetour = document.getElementById('date_retour');
    const dateRetourDisplay = document.getElementById('date_retour_display');
    const nbJoursInput = document.getElementById('nb_jours_demandes');
    const customCalendar = document.getElementById('custom_calendar');
    const form = document.getElementById('leaveRequestForm');
    
    if (!customCalendar) {
        console.error('Calendar element not found');
        return;
    }
    
    const calendarContainer = customCalendar.querySelector('.calendar-container');
    if (!calendarContainer) {
        console.error('Calendar container not found');
        return;
    }
    
    // Calendar state
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDate = null;
    
    // Get holidays from server
    let holidays = [];
    let holidaysMap = {};
    
    // Initialize calendar immediately
    renderCalendar();
    
    fetch('{{ route("hr.leaves.holidays") }}')
        .then(response => response.json())
        .then(data => {
            holidays = data.map(h => h.date);
            data.forEach(h => {
                holidaysMap[h.date] = h.name || 'Jour férié';
            });
            renderCalendar(); // Re-render with holidays
        })
        .catch(error => {
            console.error('Error fetching holidays:', error);
            renderCalendar(); // Re-render even if holidays fail
        });
    
    function isHoliday(dateString) {
        return holidays.includes(dateString);
    }
    
    function isWeekend(date) {
        const day = date.getDay();
        return day === 0 || day === 6; // Sunday or Saturday
    }
    
    function getHolidayName(dateString) {
        return holidaysMap[dateString] || 'Jour férié';
    }
    
    function formatDateForDisplay(date) {
        if (!date) return '';
        const d = new Date(date);
        const weekday = d.toLocaleDateString('fr-FR', { weekday: 'long' });
        const day = d.getDate();
        const month = d.toLocaleDateString('fr-FR', { month: 'long' });
        const year = d.getFullYear();
        
        const capitalizedWeekday = weekday.charAt(0).toUpperCase() + weekday.slice(1);
        const capitalizedMonth = month.charAt(0).toUpperCase() + month.slice(1);
        
        return `${capitalizedWeekday} ${day} ${capitalizedMonth} ${year}`;
    }
    
    function formatDateForInput(date) {
        if (!date) return '';
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function renderCalendar() {
        if (!calendarContainer) {
            console.error('Calendar container not available');
            return;
        }
        
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay();
        
        const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                           'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        const weekDays = ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'];
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Allow dates from 3 days ago onwards
        const minAllowedDate = new Date(today);
        minAllowedDate.setDate(minAllowedDate.getDate() - 3);
        minAllowedDate.setHours(0, 0, 0, 0);
        const maxDate = new Date(today.getFullYear(), 11, 31); // End of current year
        
        let html = `
            <div class="calendar-header">
                <div class="calendar-month-year">
                    <select id="calendar-month-select" class="border-0 bg-transparent font-semibold text-gray-700 cursor-pointer">
                        ${monthNames.map((monthName, idx) => 
                            `<option value="${idx}" ${idx === currentMonth ? 'selected' : ''}>${monthName}</option>`
                        ).join('')}
                    </select>
                    <select id="calendar-year-select" class="border-0 bg-transparent font-semibold text-gray-700 cursor-pointer">
                        ${Array.from({length: 3}, (_, i) => today.getFullYear() + i - 1).map(y => 
                            `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="flex gap-1">
                    <button class="calendar-nav-btn" id="calendar-prev" title="Mois précédent">
                        <i class="fas fa-chevron-up text-xs"></i>
                    </button>
                    <button class="calendar-nav-btn" id="calendar-next" title="Mois suivant">
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
            const isHolidayDay = isHoliday(dateString);
            
            html += `<div class="calendar-day other-month ${isWeekendDay ? 'weekend' : ''} ${isHolidayDay ? 'holiday' : ''}">${prevMonthDate.getDate()}</div>`;
        }
        
        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(currentYear, currentMonth, day);
            const dateString = formatDateForInput(date);
            const isWeekendDay = isWeekend(date);
            const isHolidayDay = isHoliday(dateString);
            const isToday = date.toDateString() === today.toDateString();
            const isSelected = selectedDate && dateString === formatDateForInput(selectedDate);
            const isAfterMaxDate = date > maxDate;
            const isBeforeMinAllowed = date < minAllowedDate;
            
            let classes = 'calendar-day';
            
            if (isSelected) {
                classes += ' selected';
            } else if (isToday) {
                classes += ' today';
            } else if (isWeekendDay) {
                classes += ' weekend';
            } else if (isHolidayDay) {
                classes += ' holiday';
            } else if (isAfterMaxDate) {
                classes += ' blocked';
            } else if (isBeforeMinAllowed) {
                classes += ' blocked';
            } else {
                classes += ' available';
            }
            
            // Date is clickable if: not weekend, not holiday, not before min allowed, not after max date
            const isClickable = !isWeekendDay && !isHolidayDay && !isBeforeMinAllowed && !isAfterMaxDate;
            
            const holidayName = isHolidayDay ? getHolidayName(dateString) : '';
            let title = '';
            if (isHolidayDay) {
                title = holidayName;
            } else if (isWeekendDay) {
                title = 'Weekend';
            } else if (isAfterMaxDate) {
                title = 'Hors de l\'année en cours';
            } else if (isBeforeMinAllowed) {
                title = 'Date antérieure à 3 jours';
            } else {
                title = 'Disponible';
            }
            
            html += `<div class="${classes}" data-date="${dateString}" ${isClickable ? 'onclick="selectDate(this)"' : ''} title="${title}">${day}</div>`;
        }
        
        // Add empty cells for days after the last day of the month
        const remainingCells = 42 - (startingDayOfWeek + daysInMonth);
        for (let day = 1; day <= remainingCells && day <= 14; day++) {
            const nextMonthDate = new Date(currentYear, currentMonth + 1, day);
            const dateString = formatDateForInput(nextMonthDate);
            const isWeekendDay = isWeekend(nextMonthDate);
            const isHolidayDay = isHoliday(dateString);
            
            html += `<div class="calendar-day other-month ${isWeekendDay ? 'weekend' : ''} ${isHolidayDay ? 'holiday' : ''}">${nextMonthDate.getDate()}</div>`;
        }
        
        html += `
            </div>
            <div class="calendar-footer">
                <button class="calendar-btn calendar-btn-clear" onclick="clearSelectedDate()">Effacer</button>
                <button class="calendar-btn calendar-btn-today" onclick="goToToday()">Aujourd'hui</button>
            </div>
        `;
        
        calendarContainer.innerHTML = html;
        
        // Add event listeners
        const prevBtn = document.getElementById('calendar-prev');
        const nextBtn = document.getElementById('calendar-next');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar();
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar();
            });
        }
        
        const monthSelect = document.getElementById('calendar-month-select');
        const yearSelect = document.getElementById('calendar-year-select');
        if (monthSelect) {
            monthSelect.addEventListener('change', (e) => {
                currentMonth = parseInt(e.target.value);
                renderCalendar();
            });
        }
        if (yearSelect) {
            yearSelect.addEventListener('change', (e) => {
                currentYear = parseInt(e.target.value);
                renderCalendar();
            });
        }
    }
    
    // Global functions for calendar
    window.selectDate = function(element) {
        const dateString = element.getAttribute('data-date');
        if (!dateString) return;
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return;
        
        selectedDate = date;
        currentMonth = date.getMonth();
        currentYear = date.getFullYear();
        
        dateDepart.value = formatDateForDisplay(date);
        dateDepartHidden.value = dateString;
        
        customCalendar.style.display = 'none';
        renderCalendar();
        calculateReturnDate();
    };
    
    window.clearSelectedDate = function() {
        selectedDate = null;
        dateDepart.value = '';
        dateDepartHidden.value = '';
        customCalendar.style.display = 'none';
        dateRetourDisplay.innerHTML = '<span class="text-muted">-</span>';
        dateRetour.value = '';
    };
    
    window.goToToday = function() {
        const today = new Date();
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
        renderCalendar();
    };
    
    // Show calendar when clicking on input
    dateDepart.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Toggle calendar visibility
        const isVisible = customCalendar.style.display === 'block';
        customCalendar.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible) {
            // Update month/year if date is selected, otherwise use current
            if (selectedDate) {
                currentMonth = selectedDate.getMonth();
                currentYear = selectedDate.getFullYear();
            } else {
                const today = new Date();
                currentMonth = today.getMonth();
                currentYear = today.getFullYear();
            }
            renderCalendar();
        }
    });
    
    // Also handle focus event
    dateDepart.addEventListener('focus', function(e) {
        e.preventDefault();
        if (customCalendar.style.display === 'none' || !customCalendar.style.display) {
            const today = new Date();
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            renderCalendar();
            customCalendar.style.display = 'block';
        }
    });
    
    // Close calendar when clicking outside
    document.addEventListener('click', function(e) {
        if (customCalendar && (customCalendar.style.display === 'block' || customCalendar.style.display === '')) {
            const isClickInside = customCalendar.contains(e.target) || 
                                  dateDepart.contains(e.target) || 
                                  dateDepart === e.target ||
                                  (dateDepart.parentElement && dateDepart.parentElement.contains(e.target));
            if (!isClickInside) {
                customCalendar.style.display = 'none';
            }
        }
    }, true);
    
    // Initialize if date is already set
    if (dateDepartHidden.value) {
        const date = new Date(dateDepartHidden.value);
        if (!isNaN(date.getTime())) {
            selectedDate = date;
            dateDepart.value = formatDateForDisplay(date);
            currentMonth = date.getMonth();
            currentYear = date.getFullYear();
        }
    }
    
    function calculateReturnDate() {
        const dateDepartValue = dateDepartHidden.value;
        const nbJoursValue = nbJoursInput.value;
        
        if (!dateDepartValue || !nbJoursValue || parseInt(nbJoursValue) < 1) {
            dateRetourDisplay.innerHTML = '<span class="text-muted">-</span>';
            dateRetour.value = '';
            return;
        }
        
        const startDate = new Date(dateDepartValue);
        let nbJours = parseInt(nbJoursValue);
        if (nbJours > 22) {
            nbJours = 22;
            nbJoursInput.value = 22;
        }
        
        let currentDate = new Date(startDate);
        let daysCounted = 0;
        
        // Calculate return date excluding weekends and holidays
        while (daysCounted < nbJours) {
            currentDate.setDate(currentDate.getDate() + 1);
            const dateString = currentDate.toISOString().split('T')[0];
            
            // Skip weekends and holidays
            if (!isWeekend(currentDate) && !isHoliday(dateString)) {
                daysCounted++;
            }
        }
        
        // Ensure the return date is not a weekend or holiday
        let returnDateString = currentDate.toISOString().split('T')[0];
        while (isWeekend(currentDate) || isHoliday(returnDateString)) {
            currentDate.setDate(currentDate.getDate() + 1);
            returnDateString = currentDate.toISOString().split('T')[0];
        }
        
        const formattedDate = formatDateForDisplay(returnDateString);
        dateRetourDisplay.innerHTML = `<span class="text-primary fw-bold">${formattedDate}</span>`;
        dateRetour.value = returnDateString;
    }
    
    // Calculate return date when date or number of days changes
    dateDepartHidden.addEventListener('change', calculateReturnDate);
    nbJoursInput.addEventListener('input', calculateReturnDate);
    nbJoursInput.addEventListener('change', calculateReturnDate);
    
    // Form submission
    form.addEventListener('submit', function(e) {
        // Validate date_depart
        if (!dateDepartHidden.value) {
            e.preventDefault();
            alert('Veuillez sélectionner une date de départ');
            dateDepart.focus();
            return false;
        }
        
        const selectedDate = new Date(dateDepartHidden.value);
        const dateString = dateDepartHidden.value;
        
        if (isWeekend(selectedDate)) {
            e.preventDefault();
            alert('La date de départ ne peut pas être un weekend');
            dateDepart.focus();
            return false;
        }
        
        if (isHoliday(dateString)) {
            e.preventDefault();
            const holidayName = getHolidayName(dateString);
            alert(`La date de départ ne peut pas être un jour férié (${holidayName})`);
            dateDepart.focus();
            return false;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        const submitIcon = document.getElementById('submitIcon');
        const submitText = document.getElementById('submitText');
        
        if (submitBtn && submitIcon && submitText) {
            submitBtn.disabled = true;
            submitIcon.className = 'fas fa-spinner fa-spin me-2';
            submitText.textContent = 'Traitement...';
        }
    });
    
    // Initialize if old values exist
    if (dateDepartHidden.value) {
        calculateReturnDate();
    }
});
</script>
@endpush
@endsection
