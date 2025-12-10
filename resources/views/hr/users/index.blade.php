@extends('layouts.app')

@section('title', 'User Information')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">User Information</h2>
                    <p class="text-muted">Manage and view user profiles and details</p>
                </div>
                <div>
                    <a href="{{ route('hr.users.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add New User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- User Profile Cards -->
    <div class="row">
        @forelse($users as $user)
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card user-card h-100">
                <div class="card-body">
                    <!-- User Avatar and Basic Info -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-lg me-3">
                            @if($user->userInfo && $user->userInfo->photo)
                                <img src="{{ asset('storage/' . $user->userInfo->photo) }}" alt="Avatar" class="rounded-circle img-fluid">
                            @else
                                <div class="avatar-lg bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                    <span class="fs-4 fw-bold">{{ substr($user->name, 0, 2) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0 small">PPR: {{ $user->ppr }}</p>
                            <div class="mt-1">
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="user-details">
                        @if($user->userInfo)
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="detail-item">
                                    <i class="fas fa-envelope text-info me-2"></i>
                                    <span class="small">{{ $user->userInfo->email ?: 'No email' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-item">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    <span class="small">{{ $user->userInfo->gsm ?: 'No phone' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="detail-item">
                                    <i class="fas fa-id-card text-info me-2"></i>
                                    <span class="small">{{ $user->userInfo->cin ?: 'No CIN' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-item">
                                    <i class="fas fa-briefcase text-secondary me-2"></i>
                                    <span class="small">{{ $user->userInfo->corps ?: 'Non renseigné' }}</span>
                                </div>
                            </div>
                        </div>

                        @if($user->userInfo->grade)
                        <div class="mb-3">
                            <div class="detail-item">
                                <i class="fas fa-graduation-cap text-secondary me-2"></i>
                                <span class="small">{{ $user->userInfo->grade->name }}</span>
                                @if($user->userInfo->grade->Echelle)
                                    <span class="text-muted">({{ $user->userInfo->grade->Echelle->name }})</span>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endif

                        <!-- User Roles -->
                        @if($user->roles && $user->roles->count() > 0)
                        <div class="mb-3">
                            <h6 class="small text-muted mb-2">Roles:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                <span class="badge bg-light text-dark border">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- User Entities -->
                        @if($user->entites && $user->entites->count() > 0)
                        <div class="mb-3">
                            <h6 class="small text-muted mb-2">Entities:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($user->entites as $entite)
                                <span class="badge bg-info">{{ $entite->entiteInfo->fname ?? 'Unknown' }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('hr.users.show', $user) }}" class="btn btn-outline-info btn-sm flex-fill">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="{{ route('hr.users.edit', $user) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <form action="{{ route('hr.users.destroy', $user) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Users Found</h4>
                    <p class="text-muted mb-4">Get started by adding your first user to the system.</p>
                    <a href="{{ route('hr.users.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add First User
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.user-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e9ecef;
}

.user-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.avatar-lg {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
}

.avatar-lg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.detail-item i {
    width: 16px;
    text-align: center;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.page-title {
    color: #2c3e50;
    font-weight: 600;
}

.user-details {
    border-top: 1px solid #f8f9fa;
    padding-top: 1rem;
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    font-size: 0.8rem;
    padding: 0.375rem 0.75rem;
}

@media (max-width: 768px) {
    .user-card .d-flex {
        flex-direction: column;
        text-align: center;
    }
    
    .avatar-lg {
        margin: 0 auto 1rem auto;
    }
    
    .detail-item {
        justify-content: center;
    }
}
</style>
@endsection





