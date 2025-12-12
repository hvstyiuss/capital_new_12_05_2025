@props([
    'currentStatut' => null,
    'currentYear' => null,
    'currentMonth' => null,
])

@php
    $months = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre',
    ];
    
    $currentYear = $currentYear ?? date('Y');
    $years = range(date('Y'), date('Y') - 5);
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('hr.leaves.agents') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="statut" class="form-label mb-1">Statut</label>
                <select class="form-select" id="statut" name="statut" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    <option value="pending" @selected($currentStatut === 'pending')>En attente</option>
                    <option value="approved" @selected($currentStatut === 'approved')>Validé</option>
                    <option value="rejected" @selected($currentStatut === 'rejected')>Rejeté</option>
                    <option value="cancelled" @selected($currentStatut === 'cancelled')>Annulé</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="year" class="form-label mb-1">Année</label>
                <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                    @foreach($years as $year)
                        <option value="{{ $year }}" @selected($currentYear == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="month" class="form-label mb-1">Mois</label>
                <select class="form-select" id="month" name="month" onchange="this.form.submit()">
                    <option value="">Tous les mois</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" @selected($currentMonth == $num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('hr.leaves.agents') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2" aria-hidden="true"></i>
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

