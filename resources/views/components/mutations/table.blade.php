@props([
    'mutations' => [],
    'headers' => [],
    'emptyMessage' => 'Aucune demande de mutation trouvée',
    'emptySubmessage' => 'Il n\'y a pas de demandes de mutation en attente d\'approbation de réception',
    'pagination' => null,
    'showPagination' => true,
])

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if(count($mutations) > 0)
            <!-- Horizontal Scrollable Table Container -->
            <div class="table-responsive-wrapper">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 mutations-table">
                        <thead class="table-light">
                            <tr>
                                {{ $headers }}
                            </tr>
                        </thead>
                        <tbody>
                            {{ $slot }}
                        </tbody>
                    </table>
                </div>
            </div>

            @if($showPagination && $pagination)
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div class="text-muted small">
                        @if(is_object($mutations) && method_exists($mutations, 'firstItem'))
                            Affichage de {{ $mutations->firstItem() ?? 0 }} à {{ $mutations->lastItem() ?? 0 }} sur {{ $mutations->total() }} demande(s)
                        @else
                            Affichage de {{ count($mutations) }} demande(s)
                        @endif
                    </div>
                    <div>
                        {{ $pagination }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-inbox fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">{{ $emptyMessage }}</h5>
                <p class="text-muted small">{{ $emptySubmessage }}</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Table Responsive Wrapper - Desktop: fit screen, Mobile: horizontal scroll */
    .table-responsive-wrapper {
        width: 100%;
        position: relative;
    }
    
    /* Desktop: No horizontal scroll, table fits screen */
    @media (min-width: 992px) {
        .table-responsive-wrapper {
            overflow-x: visible;
            overflow-y: visible;
        }
        
        .table-responsive {
            overflow-x: visible;
            overflow-y: visible;
        }
        
        /* Table fits available width on desktop */
        .mutations-table {
            width: 100%;
            table-layout: auto;
            margin: 0;
            border-collapse: collapse;
        }
        
        /* Allow text wrapping on desktop for better fit */
        .mutations-table th,
        .mutations-table td {
            white-space: normal;
            word-wrap: break-word;
            vertical-align: middle;
            padding: 0.75rem;
        }
        
        /* Specific column widths for better desktop layout */
        .mutations-table th:first-child,
        .mutations-table td:first-child {
            width: 60px;
            min-width: 60px;
            text-align: center;
        }
        
        /* Agent column */
        .mutations-table th:nth-child(2),
        .mutations-table td:nth-child(2) {
            min-width: 150px;
            max-width: 200px;
        }
        
        /* Entity columns */
        .mutations-table th:nth-child(3),
        .mutations-table td:nth-child(3),
        .mutations-table th:nth-child(4),
        .mutations-table td:nth-child(4) {
            min-width: 180px;
            max-width: 250px;
        }
        
        /* Date column */
        .mutations-table th:nth-child(5),
        .mutations-table td:nth-child(5) {
            width: 120px;
            min-width: 120px;
            text-align: center;
        }
        
        /* Type column */
        .mutations-table th:nth-child(6),
        .mutations-table td:nth-child(6) {
            width: 100px;
            min-width: 100px;
            text-align: center;
        }
        
        /* Validé Par column */
        .mutations-table th:nth-child(7),
        .mutations-table td:nth-child(7) {
            min-width: 200px;
            max-width: 250px;
        }
        
        /* Statut column */
        .mutations-table th:nth-child(8),
        .mutations-table td:nth-child(8) {
            width: 180px;
            min-width: 180px;
            text-align: center;
        }
        
        /* Actions column */
        .mutations-table th:last-child,
        .mutations-table td:last-child {
            width: 150px;
            min-width: 150px;
            text-align: center;
        }
    }
    
    /* Mobile: Horizontal scroll enabled */
    @media (max-width: 991px) {
        .table-responsive-wrapper {
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive {
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Table has minimum width on mobile for readability */
        .mutations-table {
            width: 100%;
            min-width: 800px;
            table-layout: auto;
            margin: 0;
            border-collapse: collapse;
        }
        
        /* No text wrapping on mobile - keep content readable */
        .mutations-table th,
        .mutations-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 0.75rem;
        }
        
        /* Sticky first column on mobile for better UX */
        .mutations-table th:first-child,
        .mutations-table td:first-child {
            position: sticky;
            left: 0;
            background-color: #fff;
            z-index: 1;
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.05);
        }
        
        .mutations-table thead th:first-child {
            background-color: #f8f9fa;
            z-index: 2;
        }
        
        /* Custom scrollbar styling for mobile */
        .table-responsive-wrapper::-webkit-scrollbar,
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-responsive-wrapper::-webkit-scrollbar-track,
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .table-responsive-wrapper::-webkit-scrollbar-thumb,
        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .table-responsive-wrapper::-webkit-scrollbar-thumb:hover,
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    }
    
    /* Dark mode support */
    .dark .mutations-table th:first-child,
    .dark .mutations-table td:first-child {
        background-color: #1f2937;
    }
    
    .dark .mutations-table thead th:first-child {
        background-color: #374151;
    }
</style>
@endpush

