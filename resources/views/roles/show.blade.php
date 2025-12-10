@extends('layouts.app')

@section('title', 'Détails du Rôle')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-gradient-primary rounded-3 p-3 shadow-sm">
                <i class="fas fa-shield-alt text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1 fw-bold text-dark">Détails du Rôle</h1>
                <p class="text-muted mb-0">Informations complètes du rôle <strong>{{ $role->name }}</strong></p>
            </div>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Role Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-info-circle text-primary"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Informations du Rôle</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Nom du rôle</label>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary fs-6 px-3 py-2">{{ $role->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Date de création</label>
                            <p class="mb-0 fw-semibold">{{ $role->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Dernière mise à jour</label>
                            <p class="mb-0 fw-semibold">{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Nombre de permissions</label>
                            <p class="mb-0 fw-semibold">{{ $role->permissions->count() }} permission(s)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-key text-info"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Permissions associées</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                        <div class="row g-3">
                            @foreach($role->permissions as $permission)
                                <div class="col-md-6 col-lg-4">
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold text-dark">{{ $permission->name }}</span>
                                            <a href="{{ route('permissions.show', $permission) }}" class="text-info" title="Voir les détails">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-key fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucune permission associée à ce rôle</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Users Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="fas fa-users text-success"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Utilisateurs avec ce rôle</h5>
                        </div>
                        <span class="badge bg-success fs-6">{{ $role->users->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th>PPR</th>
                                        <th class="text-center">Statut</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($role->users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->name ?? ($user->fname . ' ' . $user->lname) }}</div>
                                                        <small class="text-muted">{{ $user->fname ?? '' }} {{ $user->lname ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $user->email }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $user->ppr }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($user->is_active ?? true)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Actif
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle me-1"></i>Inactif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('hr.users.show', $user->ppr) }}" class="btn btn-sm btn-outline-primary" title="Voir le profil">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucun utilisateur n'a ce rôle pour le moment</p>
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
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action est irréversible.');" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add User to Role Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-user-plus text-primary"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Ajouter un utilisateur</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.add-user', $role) }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-12">
                            <label for="ppr" class="form-label text-muted small mb-1">
                                PPR de l'utilisateur
                            </label>
                            <input type="text"
                                   name="ppr"
                                   id="ppr"
                                   class="form-control @error('ppr') is-invalid @enderror"
                                   placeholder="Ex: 001340"
                                   value="{{ old('ppr') }}">
                            @error('ppr')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-12 d-grid mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Ajouter au rôle
                            </button>
                        </div>
                    </form>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Saisissez le PPR exact de l'utilisateur à ajouter à ce rôle.
                    </small>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="fas fa-chart-bar text-success"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Statistiques</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <div class="text-muted small">Utilisateurs</div>
                                <div class="fs-4 fw-bold text-success">{{ $role->users->count() }}</div>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-users text-success fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <div>
                                <div class="text-muted small">Permissions</div>
                                <div class="fs-4 fw-bold text-info">{{ $role->permissions->count() }}</div>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-key text-info fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
