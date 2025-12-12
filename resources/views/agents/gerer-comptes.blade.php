@extends('layouts.app')

@section('title', 'Gérer les Comptes')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-cog me-2"></i>
                Gérer les Comptes
            </h1>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('agents.gerer-comptes') }}" class="row align-items-end">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="search" class="form-label mb-1">Rechercher</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nom, PPR ou email...">
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="status" class="form-label mb-1">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>
                        Rechercher
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('agents.gerer-comptes') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Réinitialiser
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Agents Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>PPR</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Entité</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                            <tr>
                                <td>
                                    <strong>{{ $agent->ppr }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $agent->fname }} {{ $agent->lname }}</strong>
                                </td>
                                <td>{{ $agent->email ?? '-' }}</td>
                                <td>
                                    @if($agent->activeParcours && $agent->activeParcours->entite)
                                        {{ $agent->activeParcours->entite->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($agent->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-{{ $agent->is_active ? 'warning' : 'success' }} toggle-status" 
                                                data-ppr="{{ $agent->ppr }}"
                                                data-status="{{ $agent->is_active ? 'active' : 'inactive' }}"
                                                title="{{ $agent->is_active ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $agent->is_active ? 'user-times' : 'user-check' }}"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal{{ $agent->ppr }}"
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $agent->ppr }}" tabindex="-1" aria-labelledby="editModalLabel{{ $agent->ppr }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel{{ $agent->ppr }}">Modifier {{ $agent->fname }} {{ $agent->lname }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('agents.update', $agent) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="fname{{ $agent->ppr }}" class="form-label">Prénom</label>
                                                            <input type="text" class="form-control" id="fname{{ $agent->ppr }}" name="fname" value="{{ $agent->fname }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="lname{{ $agent->ppr }}" class="form-label">Nom</label>
                                                            <input type="text" class="form-control" id="lname{{ $agent->ppr }}" name="lname" value="{{ $agent->lname }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="email{{ $agent->ppr }}" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="email{{ $agent->ppr }}" name="email" value="{{ $agent->email }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="is_active{{ $agent->ppr }}" name="is_active" value="1" {{ $agent->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="is_active{{ $agent->ppr }}">
                                                                    Compte actif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun agent trouvé</p>
                                    @if(request('search') || request('status'))
                                        <a href="{{ route('agents.gerer-comptes') }}" class="btn btn-sm btn-outline-primary mt-2">
                                            Afficher tous les agents
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($agents->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-muted">
                                Affichage de {{ $agents->firstItem() }} à {{ $agents->lastItem() }} sur {{ $agents->total() }} agents
                            </p>
                        </div>
                        <div>
                            {{ $agents->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status functionality
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const ppr = this.getAttribute('data-ppr');
            const currentStatus = this.getAttribute('data-status');
            const newStatus = currentStatus === 'active' ? false : true;
            const action = newStatus ? 'activer' : 'désactiver';
            
            if (confirm(`Êtes-vous sûr de vouloir ${action} ce compte ?`)) {
                fetch(`/agents/${ppr}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        is_active: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
            }
        });
    });
});
</script>
@endpush
@endsection

