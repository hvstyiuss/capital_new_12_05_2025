<div class="table-responsive">
    <table class="table table-hover mb-0 align-middle no-row-click">
        <thead class="table-light">
            <tr>
                <th class="fw-semibold">PPR</th>
                <th class="fw-semibold">Nom</th>
                <th class="fw-semibold">Email</th>
                <th class="fw-semibold">Rôles</th>
                <th class="fw-semibold">Statut</th>
                <th class="fw-semibold">Date de création</th>
                <th class="fw-semibold text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="usersTableBody">
            @forelse($users as $user)
                @php
                    $email = $user->email ?? $user->userInfo->email ?? null;
                @endphp
                <tr>
                    <td>
                        <span class="ppr-badge">{{ $user->ppr }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle me-2" 
                                     width="32" height="32" style="object-fit: cover;">
                            @else
                                <div class="bg-info rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 32px; height: 32px;">
                                    <span class="text-white fw-bold small">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($email)
                            <span class="text-dark">{{ $email }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @forelse($user->roles as $role)
                            <span class="badge bg-info me-1 mb-1 rounded-pill">
                                <i class="fas fa-shield-alt me-1"></i>{{ ucfirst($role->name) }}
                            </span>
                        @empty
                            <span class="badge bg-light text-dark rounded-pill">Aucun rôle</span>
                        @endforelse
                    </td>
                    <td>
                        @if($user->is_active && !$user->is_deleted)
                            <span class="badge bg-success rounded-pill">
                                <i class="fas fa-check-circle me-1"></i>Actif
                            </span>
                        @else
                            <span class="badge bg-danger rounded-pill">
                                <i class="fas fa-times-circle me-1"></i>Inactif
                            </span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('hr.users.show', $user) }}" 
                               class="btn btn-sm btn-outline-info" 
                               title="Voir les détails"
                               onclick="event.stopPropagation();">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('hr.users.edit', $user) }}" 
                               class="btn btn-sm btn-outline-secondary" 
                               title="Modifier"
                               onclick="event.stopPropagation();">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(auth()->user()->hasRole(['admin', 'Collaborateur Rh', 'super Collaborateur Rh']))
                            <a href="{{ route('hr.users.transfer', $user) }}" 
                               class="btn btn-sm btn-outline-success" 
                               title="Transférer vers une autre entité"
                               onclick="event.stopPropagation();">
                                <i class="fas fa-exchange-alt"></i>
                            </a>
                            @endif
                            <button type="button" 
                                    data-user-ppr="{{ $user->ppr }}"
                                    data-user-status="{{ $user->is_active ? 'active' : 'inactive' }}"
                                    class="btn btn-sm {{ $user->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }} toggle-status-btn" 
                                    title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}"
                                    onclick="event.stopPropagation(); handleToggleStatus('{{ $user->ppr }}', '{{ $user->is_active ? 'active' : 'inactive' }}');">
                                @if($user->is_active)
                                    <i class="fas fa-user-times"></i>
                                @else
                                    <i class="fas fa-user-check"></i>
                                @endif
                            </button>
                            <form action="{{ route('hr.users.destroy', $user) }}" method="POST" class="d-inline" onclick="event.stopPropagation();">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="event.stopPropagation(); return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Aucun utilisateur trouvé</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

