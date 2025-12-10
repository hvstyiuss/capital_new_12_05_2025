@php /** @var \App\Models\User $user */ @endphp
<div class="flex items-center gap-2">
    <a href="{{ route('hr.users.show', $user) }}" 
       class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg transition-colors duration-200" 
       title="Voir les détails">
        <i class="fas fa-eye text-sm"></i>
    </a>

    <a href="{{ route('hr.users.edit', $user) }}" 
       class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 hover:bg-orange-200 text-orange-600 rounded-lg transition-colors duration-200" 
       title="Modifier l'utilisateur">
        <i class="fas fa-edit text-sm"></i>
    </a>

    <button type="button" 
            data-user-ppr="{{ $user->ppr }}"
            data-user-status="{{ $user->is_active ? 'active' : 'inactive' }}"
            data-action="toggle-status"
            class="inline-flex items-center justify-center w-8 h-8 {{ $user->is_active ? 'bg-yellow-100 hover:bg-yellow-200 text-yellow-600' : 'bg-green-100 hover:bg-green-200 text-green-600' }} rounded-lg transition-colors duration-200 toggle-status-btn" 
            title="{{ $user->is_active ? 'Désactiver l\'utilisateur' : 'Activer l\'utilisateur' }}">
        @if($user->is_active)
            <i class="fas fa-user-times text-sm"></i>
        @else
            <i class="fas fa-user-check text-sm"></i>
        @endif
    </button>

    <form action="{{ route('hr.users.destroy', $user) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" 
                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                class="inline-flex items-center justify-center w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors duration-200" 
                title="Supprimer l'utilisateur">
            <i class="fas fa-trash text-sm"></i>
        </button>
    </form>
</div>
