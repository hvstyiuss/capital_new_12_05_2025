<!-- Main Content Area -->
<main>
    <!-- Content Area -->
    <div class="content-area">
        <!-- Session Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible show mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg" role="alert" data-no-auto-hide="true">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            @php
                $errorMessage = session('error');
                $isMutationPendingError = $errorMessage === "Vous avez déjà une demande de mutation en attente. Vous ne pouvez pas créer une nouvelle demande tant que la précédente n'est pas traitée.";
            @endphp
            <div 
                class="alert alert-danger mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg {{ $isMutationPendingError ? 'alert-dismissible fade show' : '' }}" 
                id="sessionErrorAlert"
                @if($isMutationPendingError) data-no-auto-hide="true" @endif
            >
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $errorMessage }}
                @if($isMutationPendingError)
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="float: right;"></button>
                @endif
            </div>
        @endif

        <!-- Page Content -->
        <div class="content-scroll-container">
            @yield('content')
        </div>
    </div>
</main>
