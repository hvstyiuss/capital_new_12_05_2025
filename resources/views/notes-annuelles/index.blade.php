@extends('layouts.app')

@section('title', 'تتبع تقييمي السنوي')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">تتبع تقييمي السنوي</h1>
    </div>

    <!-- Informational Box -->
    <div class="alert alert-info mb-4 d-flex align-items-start" id="permanentAlertNotes" style="display: block !important; visibility: visible !important;">
        <i class="fas fa-info-circle me-3 mt-1"></i>
        <div class="flex-grow-1">
            <p class="mb-0">
                نشكركم على جهودكم وتفانيكم في العمل. نؤكد أن التقييم السنوي هو فرصة لتقدير جهودكم وتحفيزكم، 
                وأنه يعكس أداء كل موظف بصدق وأمانة. يمكنكم الاطلاع على تقييمكم السنوي ولديكم الحق في تقديم تظلم 
                إذا كنتم تعتقدون أن التقييم لا يعكس أداءكم الفعلي.
            </p>
        </div>
        <button class="btn btn-info btn-sm ms-3">
            <i class="fas fa-paper-plane me-1"></i>
            تقديم تظلم
        </button>
    </div>

    <!-- Table Controls -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <label class="form-label me-2 mb-0">Afficher</label>
                    <select class="form-select form-select-sm d-inline-block w-auto">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ms-2">lignes par page</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <label class="form-label me-2 mb-0">Rechercher :</label>
                    <input type="text" class="form-control form-control-sm d-inline-block w-auto" placeholder="Rechercher..." id="searchInput">
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" id="notesTable">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle text-center" style="min-width: 80px;">PPR</th>
                            <th rowspan="2" class="align-middle" style="min-width: 150px;">Nom Complet</th>
                            <th rowspan="2" class="align-middle text-center" style="min-width: 120px;">Grade</th>
                            @foreach($years as $year)
                                <th colspan="2" class="text-center {{ $year % 2 == 0 ? 'bg-warning' : 'bg-success' }} text-white" style="min-width: 200px;">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $year }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($years as $year)
                                <th class="text-center {{ $year % 2 == 0 ? 'bg-warning' : 'bg-success' }} text-white" style="min-width: 100px;">Note annuelle</th>
                                <th class="text-center {{ $year % 2 == 0 ? 'bg-warning' : 'bg-success' }} text-white" style="min-width: 100px;">Observation</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tableData as $row)
                            <tr>
                                <td class="text-center fw-bold">{{ $row['ppr'] }}</td>
                                <td>{{ $row['nom_complet'] }}</td>
                                <td class="text-center">{{ $row['grade'] }}</td>
                                @foreach($years as $year)
                                    @php
                                        $note = $row['notes'][$year] ?? null;
                                    @endphp
                                    <td class="text-center">
                                        @if($note && isset($note['note']))
                                            <strong class="text-primary">{{ $note['note'] }} / 20</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($note && isset($note['observation']))
                                            <span class="badge {{ $note['note'] >= 16 ? 'bg-success' : ($note['note'] >= 12 ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $note['observation'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-muted">Affichage de <strong>{{ count($tableData) }}</strong> résultat(s)</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-weight: 600;
        vertical-align: middle;
        white-space: nowrap;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .bg-success {
        background-color: #28a745 !important;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
    }
    
    .badge {
        padding: 0.4em 0.7em;
        font-size: 0.85em;
        font-weight: 500;
    }
    
    .table-responsive {
        overflow-x: auto;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    #notesTable {
        font-size: 0.9rem;
    }

    #notesTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
    }

    #permanentAlertNotes {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    #permanentAlertNotes .btn-close {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('notesTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            Array.from(rows).forEach(function(row) {
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                Array.from(cells).forEach(function(cell) {
                    if (cell.textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                    }
                });
                
                row.style.display = found ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection

