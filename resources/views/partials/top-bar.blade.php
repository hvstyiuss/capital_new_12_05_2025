<!-- Top Bar -->
<div class="top-bar">
    <div class="top-bar-content">
        <div class="top-bar-left">
            <div class="breadcrumbs">
                <div class="breadcrumb-item">
                    <i class="fas fa-home"></i>
                    <span>Accueil</span>
                </div>
                @if(request()->routeIs('dashboard'))
                    <!-- Already on dashboard -->
                @elseif(request()->routeIs('settings.*'))
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-item">Paramètres</span>
                    @if(request()->routeIs('settings.localisations'))
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-item">Localisations</span>
                    @endif
                @elseif(request()->routeIs('auth.users.*'))
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-item">Utilisateurs</span>
                    @if(request()->routeIs('auth.users.create'))
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-item">Nouvel Utilisateur</span>
                    @elseif(request()->routeIs('auth.users.edit'))
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-item">Modifier Utilisateur</span>
                    @endif
                @elseif(request()->routeIs('auth.profile'))
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-item">Mon Profil</span>
                @endif
            </div>
        </div>
        <div class="top-bar-right">
            <div class="top-bar-actions">
                <!-- Notifications -->
                <div class="notification-dropdown">
                    <button class="top-bar-btn notification-btn" onclick="toggleNotifications()" title="Notifications">
                        <i class="fas fa-bell"></i>
                        @if($unreadNotificationCount > 0)
                            <span class="notification-badge">{{ $unreadNotificationCount }}</span>
                        @endif
                    </button>
                    <div class="notification-panel" id="notificationPanel">
                        <div class="notification-header">
                            <h6>Notifications</h6>
                            @if($unreadNotificationCount > 0)
                                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="mark-all-read">Tout marquer comme lu</button>
                                </form>
                            @endif
                        </div>
                        <div class="notification-list">
                            @if($recentNotifications->count() > 0)
                                @foreach($recentNotifications as $notification)
                                    <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                        <div class="notification-icon">
                                            @if(isset($notification->data['icon']))
                                                <i class="{{ $notification->data['icon'] }} text-{{ $notification->data['color'] ?? 'primary' }}"></i>
                                            @else
                                                <i class="fas fa-bell text-primary"></i>
                                            @endif
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                            <div class="notification-text">{{ $notification->data['message'] ?? $notification->data['body'] ?? '' }}</div>
                                            <div class="notification-time">{{ $notification->time_ago ?? $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="notification-item">
                                    <div class="notification-content text-center py-3">
                                        <div class="notification-text text-muted">Aucune notification</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Dark Mode Toggle -->
                <button class="top-bar-btn dark-mode-btn" onclick="toggleDarkMode()" title="Basculer le mode sombre">
                    <i class="fas fa-moon" id="top-dark-mode-icon"></i>
                </button>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown">
                    <button class="top-bar-btn profile-btn" onclick="toggleProfile()" title="Mon profil">
                                <div class="profile-avatar">
                                    @if($currentUser && $currentUser->image)
                                        <img src="{{ asset('storage/' . $currentUser->image) }}" alt="{{ $currentUser->name }}" class="avatar-img">
                                    @elseif($currentUser && $currentUser->userInfo && $currentUser->userInfo->photo)
                                        <img src="{{ $currentUser->userInfo->photo_url }}" alt="{{ $currentUser->name }}" class="avatar-img">
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                <span class="profile-name">{{ $currentUser->name ?? 'Utilisateur' }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="profile-panel" id="profilePanel">
                        <div class="profile-header">
                            <div class="profile-info">
                                <div class="profile-avatar-large">
                                    @if($currentUser && $currentUser->image)
                                        <img src="{{ asset('storage/' . $currentUser->image) }}" alt="{{ $currentUser->name }}" class="avatar-img-large">
                                    @elseif($currentUser && $currentUser->userInfo && $currentUser->userInfo->photo)
                                        <img src="{{ $currentUser->userInfo->photo_url }}" alt="{{ $currentUser->name }}" class="avatar-img-large">
                                    @else
                                        <div class="avatar-initials">
                                            {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="profile-details">
                                    <h6>{{ $currentUser->name ?? 'Utilisateur' }}</h6>
                                    <span>{{ $currentUser->email ?? 'email@example.com' }}</span>
                                    @if($currentUser && $currentUser->ppr)
                                        <span class="profile-ppr">PPR: {{ $currentUser->ppr }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="profile-menu">
                            <a href="{{ route('auth.profile') }}" class="profile-menu-item">
                                <i class="fas fa-user"></i>
                                <span>Mon Profil</span>
                            </a>
                            <a href="{{ route('parcours.my') }}" class="profile-menu-item">
                                <i class="fas fa-route"></i>
                                <span>Mon Parcours</span>
                            </a>
                            <a href="{{ route('mutations.tracking') }}" class="profile-menu-item">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Mes Mutations</span>
                            </a>
                            <div class="profile-menu-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="profile-menu-item logout-btn">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
