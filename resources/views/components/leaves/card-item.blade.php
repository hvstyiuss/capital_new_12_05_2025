@props(['viewModel'])

<div class="col-12">
    <div class="card shadow-sm border-left-4 {{ $viewModel->getBorderColorClass() }}">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <!-- Left Section: Basic Info -->
                <div class="col-md-3">
                    @include('components.leaves.card-basic-info', ['viewModel' => $viewModel])
                </div>

                <!-- Middle Section: Avis de Départ -->
                <div class="col-md-4 border-start border-end px-3">
                    @include('components.leaves.card-avis-depart', ['viewModel' => $viewModel])
                </div>

                <!-- Right Section: Avis de Retour -->
                <div class="col-md-3 px-3">
                    @include('components.leaves.card-avis-retour', ['viewModel' => $viewModel])
                </div>

                <!-- Actions Section -->
                <div class="col-md-2 text-end">
                    @include('components.leaves.card-actions', ['viewModel' => $viewModel])
                </div>
            </div>
        </div>
    </div>
</div>

