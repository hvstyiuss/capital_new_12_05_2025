@props(['viewModel'])

<div class="d-flex align-items-center mb-2">
    <span class="badge bg-secondary me-2">#{{ $viewModel->demande->id }}</span>
    <h6 class="mb-0">
        <a href="{{ route('hr.leaves.user-info', $viewModel->demande->ppr) }}" 
           class="text-decoration-none text-success fw-bold">
            {{ $viewModel->getUserName() }}
        </a>
    </h6>
</div>

<div class="text-muted small">
    <div>
        <i class="fas fa-calendar-alt me-1" aria-hidden="true"></i>
        {{ $viewModel->getFormattedDateDepot() }}
    </div>
    <div class="mt-1">
        <i class="fas fa-tag me-1" aria-hidden="true"></i>
        {{ $viewModel->getTypeDemande() }}
    </div>
</div>

