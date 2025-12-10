@extends('layouts.app')

@section('title', 'Détails de la Permission')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gradient-primary rounded-3 p-3 shadow-sm">
                <i class="fas fa-key text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Détails de la Permission</h1>
                <p class="text-muted mb-0">Informations complètes de la permission <strong>{{ $permission->name }}</strong></p>
            </div>
        </div>
        <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Permission Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-info-circle text-info"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Informations de la Permission</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Nom de la permission</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info fs-6 px-3 py-2">{{ $permission->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Date de création</label>
                            <p class="mb-0 fw-semibold">{{ $permission->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Dernière mise à jour</label>
                            <p class="mb-0 fw-semibold">{{ $permission->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Nombre de rôles</label>
                            <p class="mb-0 fw-semibold">{{ $permission->roles->count() }} rôle(s)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-shield-alt text-primary"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Rôles associés</h5>
                        </div>
                        <span class="badge bg-primary fs-6">{{ $permission->roles->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($permission->roles->count() > 0)
                        <div class="row g-3">
                            @foreach($permission->roles as $role)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-primary fs-6 mb-1">{{ $role->name }}</span>
                                                <div class="small text-muted mt-1">
                                                    {{ $role->users_count ?? 0 }} utilisateur(s)
                                                </div>
                                            </div>
                                            <a href="{{ route('roles.show', $role) }}" class="text-primary" title="Voir les détails">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucun rôle associé à cette permission</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-tools text-warning"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Actions</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ? Cette action est irréversible.');" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-chart-bar text-info"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Statistiques</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <div class="text-muted small">Rôles</div>
                                <div class="fs-4 fw-bold text-primary">{{ $permission->roles->count() }}</div>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shield-alt text-primary fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <div class="text-muted small">Utilisateurs totaux</div>
                                <div class="fs-4 fw-bold text-success">
                                    {{ $permission->roles->sum('users_count') }}
                                </div>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-success fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
