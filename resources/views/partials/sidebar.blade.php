<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <button class="sidebar-toggle" onclick="toggleSidebarCollapse()" title="Réduire/Agrandir le menu">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="logo">
            <i class="fas fa-tree logo-icon"></i>
            <h1>Capital</h1>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de Bord</span>
            </a>
        </div>

        <!-- RH Stats (Admin, Collaborateur Rh, super Collaborateur Rh) -->
        @php
            $sidebarUser = auth()->user();
            $hasRhStatsRole = false;
            if ($sidebarUser) {
                $hasRhStatsRole = $sidebarUser->hasRole('admin') || 
                                 $sidebarUser->hasRole('Collaborateur Rh') || 
                                 $sidebarUser->hasRole('super Collaborateur Rh');
            }
        @endphp
        @if($hasRhStatsRole)
        <div class="nav-item has-submenu">
            <a class="nav-link has-submenu {{ request()->routeIs('hr.stats') || request()->routeIs('hr.users.*') ? 'active' : '' }}" onclick="toggleSubmenu(this); return false;">
                <i class="fas fa-chart-line"></i>
                <span>RH Stats</span>
            </a>
            <div class="submenu {{ request()->routeIs('hr.stats') || request()->routeIs('hr.users.*') ? 'expanded' : '' }}">
                <a class="submenu-item {{ request()->routeIs('hr.stats') ? 'active' : '' }}" href="{{ route('hr.stats') }}">
                    <i class="fas fa-chart-bar"></i> <span>Statistiques</span>
                </a>
                @if(auth()->user()->hasRole('admin'))
                <a class="submenu-item {{ request()->routeIs('hr.users.*') ? 'active' : '' }}" href="{{ route('hr.users.index') }}">
                    <i class="fas fa-users-cog"></i> <span>Gestion des Utilisateurs</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- Congés -->
        <div class="nav-item has-submenu">
            <a class="nav-link has-submenu {{ request()->routeIs('hr.leaves.*') || request()->routeIs('leaves.*') ? 'active' : '' }}" onclick="toggleSubmenu(this); return false;">
                <i class="fas fa-calendar-alt"></i>
                <span>Congés</span>
            </a>
            <div class="submenu {{ request()->routeIs('hr.leaves.*') || request()->routeIs('leaves.*') ? 'expanded' : '' }}">
                <a class="submenu-item {{ request()->routeIs('hr.leaves.index') ? 'active' : '' }}" href="{{ route('hr.leaves.index') }}">
                    <i class="fas fa-plus-circle"></i> <span>Faire une demande</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('leaves.tracking') ? 'active' : '' }}" href="{{ route('leaves.tracking') }}">
                    <i class="fas fa-list-alt"></i> <span>Suivi mes Demandes</span>
                </a>
                @php
                    $isChef = auth()->user()->isChef() ?? false;
                @endphp
                @if($isChef)
                <a class="submenu-item {{ request()->routeIs('hr.leaves.agents') ? 'active' : '' }}" href="{{ route('hr.leaves.agents') }}">
                    <i class="fas fa-clipboard-list"></i> <span>Suivi de demandes de mes agents</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('hr.leaves.agents-solde') ? 'active' : '' }}" href="{{ route('hr.leaves.agents-solde') }}">
                    <i class="fas fa-wallet"></i> <span>Solde actuel de mes agents</span>
                </a>
                @endif
                @if(auth()->user()->hasRole('admin'))
                <a class="submenu-item {{ request()->routeIs('hr.leaves.central') ? 'active' : '' }}" href="{{ route('hr.leaves.central') }}">
                    <i class="fas fa-building"></i> <span>Central</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('hr.leaves.regional') ? 'active' : '' }}" href="{{ route('hr.leaves.regional') }}">
                    <i class="fas fa-map-marked-alt"></i> <span>Régional</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('hr.leaves.stats') ? 'active' : '' }}" href="{{ route('hr.leaves.stats') }}">
                    <i class="fas fa-chart-bar"></i> <span>Stats</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Mutations -->
        <div class="nav-item has-submenu">
            <a class="nav-link has-submenu {{ request()->routeIs('mutations.*') || request()->routeIs('affectation.*') ? 'active' : '' }}" onclick="toggleSubmenu(this); return false;">
                <i class="fas fa-exchange-alt"></i>
                <span>Mutations</span>
            </a>
            <div class="submenu {{ request()->routeIs('mutations.*') || request()->routeIs('affectation.*') ? 'expanded' : '' }}">
                <a class="submenu-item {{ request()->routeIs('mutations.create') ? 'active' : '' }}" href="{{ route('mutations.create') }}">
                    <i class="fas fa-plus-circle"></i> <span>Faire mutation</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('mutations.tracking') ? 'active' : '' }}" href="{{ route('mutations.tracking') }}">
                    <i class="fas fa-list-alt"></i> <span>Suivi mes demandes</span>
                </a>
                @php
                    $user = auth()->user();
                    $isDirector = $user && method_exists($user, 'isDirectorOfDirection') ? $user->isDirectorOfDirection() : false;
                    $isSpecialChef = false;
                    if ($user) {
                        $mutationService = app(\App\Services\MutationService::class);
                        $isSpecialChef = $mutationService->isChefOfSpecialEntity($user);
                    }
                @endphp
                @if($isDirector || $isSpecialChef)
                <a class="submenu-item {{ request()->routeIs('mutations.agent-requests') ? 'active' : '' }}" href="{{ route('mutations.agent-requests') }}">
                    <i class="fas fa-users"></i> <span>Suivi Demandes de Mutations</span>
                </a>
                @endif
                @php
                    $user = auth()->user();
                    $hasSuperRhRole = $user && $user->hasRole('super Collaborateur Rh');
                @endphp
                @if($hasSuperRhRole)
                <a class="submenu-item {{ request()->routeIs('mutations.super-rh.destination-requests') ? 'active' : '' }}" href="{{ route('mutations.super-rh.destination-requests') }}">
                    <i class="fas fa-file-alt"></i> <span>Demande de mutation</span>
                </a>
                @endif
                @if(auth()->user()->hasRole('admin'))
                <a class="submenu-item {{ request()->routeIs('mutations.stats') ? 'active' : '' }}" href="{{ route('mutations.stats') }}">
                    <i class="fas fa-chart-bar"></i> <span>Stats</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('affectation.*') ? 'active' : '' }}" href="{{ route('affectation.index') }}">
                    <i class="fas fa-user-check"></i> <span>Affectation</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Déplacements (Admin, Collaborateur Rh, super Collaborateur Rh) -->
        @if($hasRhStatsRole)
        <div class="nav-item has-submenu">
            <a class="nav-link has-submenu {{ request()->routeIs('deplacements.*') ? 'active' : '' }}" onclick="toggleSubmenu(this); return false;">
                <i class="fas fa-plane"></i>
                <span>Déplacement</span>
            </a>
            <div class="submenu {{ request()->routeIs('deplacements.*') ? 'expanded' : '' }}">
                <a class="submenu-item {{ request()->is('deplacements/central*') ? 'active' : '' }}" href="{{ route('deplacements.by-type', ['type' => 'central']) }}">
                    <i class="fas fa-building"></i> <span>Central</span>
                </a>
                <a class="submenu-item {{ request()->is('deplacements/regional*') ? 'active' : '' }}" href="{{ route('deplacements.by-type', ['type' => 'regional']) }}">
                    <i class="fas fa-map-marked-alt"></i> <span>Régional</span>
                </a>
                @if(auth()->user()->hasRole('admin'))
                <a class="submenu-item {{ request()->routeIs('deplacements.stats') ? 'active' : '' }}" href="{{ route('deplacements.stats') }}">
                    <i class="fas fa-chart-bar"></i> <span>Stats</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('montants.*') ? 'active' : '' }}" href="{{ route('montants.index') }}">
                    <i class="fas fa-money-bill-wave"></i> <span>Montants</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- Déplacements pour Chefs -->
        @php
            $isChef = auth()->user()->isChef() ?? false;
            $chefEntites = [];
            if ($isChef && !$hasRhStatsRole) {
                $chefEntites = \App\Models\Entite::where('chef_ppr', auth()->user()->ppr)
                    ->with('entiteInfo')
                    ->get();
            }
        @endphp
        @if($isChef && !$hasRhStatsRole && $chefEntites->count() > 0)
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('deplacements.chef.*') ? 'active' : '' }}" href="{{ route('deplacements.chef.index') }}">
                <i class="fas fa-plane"></i>
                <span>Déplacements</span>
            </a>
        </div>
        @endif

        <!-- Mes Agents (Chef and Admin only) -->
        @php
            $isChef = auth()->user()->isChef() ?? false;
        @endphp
        @if($isChef || auth()->user()->hasRole('admin'))
        <div class="nav-item has-submenu">
            <a class="nav-link has-submenu {{ request()->routeIs('agents.*') ? 'active' : '' }}" onclick="toggleSubmenu(this); return false;">
                <i class="fas fa-users-cog"></i>
                <span>Mes Agents</span>
            </a>
            <div class="submenu {{ request()->routeIs('agents.*') ? 'expanded' : '' }}">
                <a class="submenu-item {{ request()->routeIs('agents.consulter') ? 'active' : '' }}" href="{{ route('agents.consulter') }}">
                    <i class="fas fa-list"></i> <span>Consulter mes agents</span>
                </a>
                @if(auth()->user()->hasRole('admin'))
                <a class="submenu-item {{ request()->routeIs('agents.gerer-comptes') ? 'active' : '' }}" href="{{ route('agents.gerer-comptes') }}">
                    <i class="fas fa-user-cog"></i> <span>Gérer les Comptes</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- Mon Parcours (All users) -->
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('parcours.my') ? 'active' : '' }}" href="{{ route('parcours.my') }}">
                <i class="fas fa-route"></i>
                <span>Mon Parcours</span>
            </a>
        </div>

        <!-- Parcours Professionnels (Admin only) -->
        @if(auth()->user()->hasRole('admin'))
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('parcours.index') ? 'active' : '' }}" href="{{ route('parcours.index') }}">
                <i class="fas fa-route"></i>
                <span>Parcours Professionnels</span>
            </a>
        </div>
        @endif

        <!-- Jours Fériés (All users) -->
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('jours-feries.*') ? 'active' : '' }}" href="{{ route('jours-feries.index') }}">
                <i class="fas fa-calendar-check"></i>
                <span>Jours Fériés</span>
            </a>
        </div>

        <!-- Entities (Admin only) -->
        @if(auth()->user()->hasRole('admin'))
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('entities.*') ? 'active' : '' }}" href="{{ route('entities.index') }}">
                <i class="fas fa-building"></i>
                <span>Entités</span>
            </a>
        </div>
        @endif

        <!-- Rôles et Permissions (Admin only) -->
        @if(auth()->user()->hasRole('admin'))
        <div class="nav-item has-submenu">
            <a class="nav-link has-submenu {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}" onclick="toggleSubmenu(this); return false;">
                <i class="fas fa-shield-alt"></i>
                <span>Rôles et Permissions</span>
            </a>
            <div class="submenu {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'expanded' : '' }}">
                <a class="submenu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                    <i class="fas fa-user-tag"></i> <span>Rôles</span>
                </a>
                <a class="submenu-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}" href="{{ route('permissions.index') }}">
                    <i class="fas fa-key"></i> <span>Permissions</span>
                </a>
            </div>
        </div>
        @endif

        <!-- Notifications -->
        <div class="nav-item">
            <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                @php
                    $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="notification-count" style="background-color: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.7rem; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; margin-left: auto; margin-right: 0.5rem;">{{ $unreadCount }}</span>
                @endif
            </a>
        </div>
    </nav>
</nav>
