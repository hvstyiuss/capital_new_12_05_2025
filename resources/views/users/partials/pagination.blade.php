@if($users->hasPages())
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="text-muted small">
        Affichage de <strong>{{ $users->firstItem() }}</strong> à <strong>{{ $users->lastItem() }}</strong> 
        sur <strong>{{ $users->total() }}</strong> résultat(s)
    </div>
    <nav aria-label="Pagination">
        <ul class="pagination pagination-modern mb-0">
            {{-- Previous Page Link --}}
            @if ($users->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Précédent">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $users->appends(request()->query())->previousPageUrl() }}" 
                       aria-label="Précédent" data-page="{{ $users->currentPage() - 1 }}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach (($paginationData ?? []) as $pageInfo)
                @if (isset($pageInfo['disabled']))
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @elseif ($pageInfo['page'] == $users->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $pageInfo['page'] }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $pageInfo['url'] }}" data-page="{{ $pageInfo['page'] }}">{{ $pageInfo['page'] }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($users->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $users->appends(request()->query())->nextPageUrl() }}" 
                       aria-label="Suivant" data-page="{{ $users->currentPage() + 1 }}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-label="Suivant">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif

