<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Capital')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Component Styles -->
    @stack('styles')
    
    <style>
        :root {
            --primary-color: #059669;
            --secondary-color: #7c2d12;
            --success-color: #16a34a;
            --warning-color: #ca8a04;
            --danger-color: #dc2626;
            --info-color: #0891b2;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            line-height: 1.6;
            color: #374151;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            width: 280px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .sidebar-header h1,
        .sidebar.collapsed .sidebar-header p,
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 1rem;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .submenu {
            display: none;
        }

        .sidebar-toggle {
            position: absolute;
            top: 1rem;
            right: 14px;
            background: #ffffff;
            color: #059669;
            border: 2px solid #059669;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            z-index: 1001;
            font-size: 0.7rem;
        }

        .sidebar-toggle:hover {
            background: #059669;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .sidebar-toggle i {
            color: inherit;
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }

        .sidebar-header {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: visible;
            z-index: 1;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .sidebar-header p {
            font-size: 0.875rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
            background: rgba(255, 255, 255, 0.5);
        }

        .nav-item {
            margin: 0.5rem 1rem;
            position: relative;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            background: transparent;
            border: 1px solid transparent;
        }

        .nav-link:hover {
            color: #059669;
            background: rgba(5, 150, 105, 0.08);
            transform: translateX(6px);
            border-color: rgba(5, 150, 105, 0.2);
            box-shadow: 0 4px 20px rgba(5, 150, 105, 0.15);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            transform: translateX(6px);
            border-color: #047857;
        }

        .nav-link i {
            margin-right: 1rem;
            width: 1.5rem;
            text-align: center;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .nav-link.has-submenu::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 1rem;
            transition: transform 0.3s ease;
        }
        
        .nav-link.has-submenu.expanded::after {
            transform: rotate(180deg);
        }
        
        /* Submenu styling */
        .submenu {
            margin-left: 1rem;
            margin-top: 0.5rem;
            border-left: 2px solid rgba(5, 150, 105, 0.2);
            padding-left: 1rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }
        
        .submenu.expanded {
            max-height: 500px;
            opacity: 1;
        }

        .submenu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #64748b;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            margin: 0.25rem 0;
            position: relative;
        }

        .submenu-item:hover {
            background: rgba(5, 150, 105, 0.08);
            color: #059669;
            transform: translateX(4px);
        }

        .submenu-item.active {
            background: rgba(5, 150, 105, 0.15);
            color: #059669;
            font-weight: 600;
        }

        .submenu-item i {
            margin-right: 0.75rem;
            width: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(5, 150, 105, 0.3);
            border-radius: 2px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(5, 150, 105, 0.5);
        }

        /* Main Content */
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 1rem;
            margin: 1rem;
            margin-left: calc(280px + 1rem);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
            /* Create isolated stacking context to prevent interference with modals */
            isolation: isolate;
        }

        .sidebar.collapsed ~ .content-wrapper {
            margin-left: calc(70px + 1rem);
        }

        /* Top Header */
        .top-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-header-left {
            display: flex;
            align-items: center;
            min-width: 0;
            flex: 1;
        }

        .top-header-right {
            display: flex;
            align-items: center;
                gap: 1rem;
            flex-shrink: 0;
        }

        .mobile-menu-btn {
            padding: 0.5rem;
            border-radius: 0.5rem;
            color: #6b7280;
            background: transparent;
            border: none;
            cursor: pointer;
            margin-right: 1rem;
            display: none;
        }

        .mobile-menu-btn:hover {
            color: #374151;
            background: #f3f4f6;
        }

        .mobile-menu-btn i {
            font-size: 1.25rem;
        }

        .top-header-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .notification-icon-btn {
            position: relative;
            padding: 0.5rem;
            color: #6b7280;
            background: transparent;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .notification-icon-btn:hover {
            color: #374151;
            background: #f3f4f6;
        }

        .notification-icon-btn i {
            font-size: 1.25rem;
        }

        .notification-badge {
            position: absolute;
            top: -0.25rem;
            right: -0.25rem;
            min-width: 1.25rem;
            height: 1.25rem;
            background: #ef4444;
            color: white;
            border-radius: 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.375rem;
            border: 2px solid white;
        }

        .notification-dropdown-wrapper {
            position: relative;
            display: inline-block;
        }

        .notification-dropdown-panel {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 1050;
            overflow: hidden;
            display: none;
            flex-direction: column;
        }

        .notification-dropdown-panel.show {
            display: flex;
        }

        .notification-dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
        }

        .notification-dropdown-header h6 {
            margin: 0;
            color: #1f2937;
            font-size: 0.875rem;
        }

        .btn-mark-all-read {
            background: transparent;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-mark-all-read:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .notification-dropdown-list {
            overflow-y: auto;
            max-height: 400px;
        }

        .notification-dropdown-item {
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.75rem;
            transition: background 0.2s;
        }

        .notification-dropdown-item:hover {
            background: #f9fafb;
        }

        .notification-dropdown-item.unread {
            background: #eff6ff;
        }

        .notification-dropdown-item.unread:hover {
            background: #dbeafe;
        }

        .notification-dropdown-content {
            display: flex;
            gap: 0.75rem;
            flex: 1;
            min-width: 0;
        }

        .notification-dropdown-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            flex-shrink: 0;
        }

        .notification-dropdown-item.unread .notification-dropdown-icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .notification-dropdown-text {
            flex: 1;
            min-width: 0;
        }

        .notification-dropdown-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .notification-dropdown-message {
            font-size: 0.8125rem;
            color: #6b7280;
            margin-bottom: 0.375rem;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .notification-dropdown-time {
            font-size: 0.75rem;
            color: #9ca3af;
        }

        .notification-dropdown-actions {
            display: flex;
            gap: 0.25rem;
            flex-shrink: 0;
        }

        .btn-notification-action {
            width: 2rem;
            height: 2rem;
            border: none;
            background: transparent;
            border-radius: 0.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            transition: all 0.2s;
        }

        .btn-notification-action:hover {
            background: #e5e7eb;
        }

        .btn-notification-action.btn-read:hover {
            color: #2563eb;
            background: #dbeafe;
        }

        .btn-notification-action.btn-delete:hover {
            color: #dc2626;
            background: #fee2e2;
        }

        .notification-dropdown-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: #9ca3af;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .notification-dropdown-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
            color: #d1d5db;
        }

        .notification-dropdown-empty p {
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .notification-dropdown-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            text-align: center;
        }

        .notification-view-all {
            color: #2563eb;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .notification-view-all:hover {
            color: #1d4ed8;
        }

        @media (max-width: 640px) {
            .notification-dropdown-panel {
                width: calc(100vw - 2rem);
                max-width: 320px;
                right: 0;
            }
        }

        /* Ensure dropdown is above other content */
        .top-header-right {
            position: relative;
            z-index: 100;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            background: transparent;
            border: none;
        }

        .user-profile:hover {
            background: #f3f4f6;
        }

        .profile-pic {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.875rem;
        }

        .user-email {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .content-area {
            padding: 2rem;
            min-height: calc(100vh - 2rem);
        }

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 1rem !important;
            }

            .mobile-menu-btn {
                display: block;
            }

            .top-header {
            padding: 1rem;
            }

            .user-name {
                display: none;
            }
        }

        @media (min-width: 1025px) {
            .sidebar {
                transform: translateX(0);
            }
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <!-- Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>
    
    <div class="main-wrapper">
        <!-- Left Sidebar -->
        @include('partials.sidebar')

        <!-- Main Content -->
        <div class="content-wrapper flex-1">
            <!-- Top Header -->
            <header class="top-header">
                <div class="top-header-left">
                    <!-- Mobile menu button -->
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <h2 class="top-header-title">@yield('title', 'Capital')</h2>
                </div>
                
                <div class="top-header-right">
                    <!-- Notifications Dropdown -->
                    <div class="notification-dropdown-wrapper">
                    <button type="button" class="notification-icon-btn" onclick="toggleNotificationDropdown(event)" id="notificationDropdownBtn" aria-label="Notifications" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        @if($unreadNotificationCount > 0)
                            <span class="notification-badge">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                        @endif
                    </button>
                        
                        <div class="notification-dropdown-panel" id="notificationDropdownPanel">
                            <div class="notification-dropdown-header">
                                <h6 class="mb-0 fw-bold">Notifications</h6>
                                @if($unreadNotificationCount > 0)
                                    <button class="btn-mark-all-read" onclick="markAllNotificationsAsRead()" title="Marquer tout comme lu">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="notification-dropdown-list">
                                @if($recentNotifications->count() > 0)
                                    @foreach($recentNotifications as $notification)
                                        <div class="notification-dropdown-item {{ !$notification->read_at ? 'unread' : '' }}" data-notification-id="{{ $notification->id }}">
                                            <div class="notification-dropdown-content">
                                                <div class="notification-dropdown-icon">
                                                    @if(isset($notification->data['icon']))
                                                        <i class="{{ $notification->data['icon'] }}"></i>
                                                    @else
                                                        <i class="fas fa-bell"></i>
                                                    @endif
                                                </div>
                                                <div class="notification-dropdown-text">
                                                    <div class="notification-dropdown-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                                    <div class="notification-dropdown-message">{{ $notification->data['message'] ?? $notification->data['body'] ?? '' }}</div>
                                                    <div class="notification-dropdown-time">{{ $notification->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                            <div class="notification-dropdown-actions">
                                                @if(!$notification->read_at)
                                                    <button class="btn-notification-action btn-read" onclick="markNotificationAsRead({{ $notification->id }})" title="Marquer comme lu">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn-notification-action btn-delete" onclick="deleteNotification({{ $notification->id }})" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="notification-dropdown-empty">
                                        <i class="fas fa-bell-slash"></i>
                                        <p class="mb-0 text-muted">Aucune notification</p>
                                    </div>
                                @endif
                            </div>
                            @if($recentNotifications->count() > 0)
                                <div class="notification-dropdown-footer">
                                    <a href="{{ route('notifications.index') }}" class="notification-view-all">
                                        Voir toutes les notifications
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- User menu -->
                    <div class="relative profile-dropdown-app">
                        <button onclick="toggleProfileApp()" class="user-profile">
                            @php
                                $currentUser = auth()->user();
                            @endphp
                            @if($currentUser && $currentUser->image)
                                <img src="{{ asset('storage/' . $currentUser->image) }}" alt="Profile" class="profile-pic">
                            @elseif($currentUser && $currentUser->userInfo && $currentUser->userInfo->photo)
                                <img src="{{ $currentUser->userInfo->photo_url }}" alt="Profile" class="profile-pic">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($currentUser->name ?? 'User') }}&background=059669&color=fff" alt="Profile" class="profile-pic">
                            @endif
                            <div class="hidden sm:block">
                                <div class="user-name">{{ $currentUser->name ?? 'Utilisateur' }}</div>
                                @if($currentUser && $currentUser->ppr)
                                    <div class="user-email">PPR: {{ $currentUser->ppr }}</div>
                                @endif
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-500 hidden sm:block"></i>
                        </button>
                        
                        <div id="profilePanelApp" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                             style="display: none; position: absolute; right: 0; margin-top: 0.5rem; width: 12rem; background: white; border-radius: 0.375rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); padding: 0.25rem 0; z-index: 50;">
                            <a href="{{ route('auth.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" style="display: block; padding: 0.5rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;">
                                <i class="fas fa-user mr-2"></i>Mon Profil
                            </a>
                            <a href="{{ route('parcours.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" style="display: block; padding: 0.5rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;">
                                <i class="fas fa-route mr-2"></i>Mon Parcours
                            </a>
                            <a href="{{ route('mutations.tracking') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" style="display: block; padding: 0.5rem 1rem; font-size: 0.875rem; color: #374151; text-decoration: none;">
                                <i class="fas fa-exchange-alt mr-2"></i>Mes Mutations
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" style="display: block; width: 100%; text-align: left; padding: 0.5rem 1rem; font-size: 0.875rem; color: #374151; background: none; border: none; cursor: pointer;">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="content-area" id="main-content">
                @include('partials.content-wrapper')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    @include('partials.scripts')

    <!-- Additional Scripts -->
    @stack('scripts')

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            if (window.innerWidth < 1024) {
                    sidebar.classList.toggle('open');
                if (backdrop) backdrop.classList.toggle('active');
            }
        }

        function closeSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
                sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('active');
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth < 1024 && 
                sidebar && 
                sidebar.classList.contains('open') &&
                !sidebar.contains(event.target) && 
                (!sidebarToggle || !sidebarToggle.contains(event.target))) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not([data-no-auto-hide])');
                alerts.forEach(function(alert) {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                });
            }, 5000);

            const activeSubmenuItem = document.querySelector('.submenu-item.active');
            if (activeSubmenuItem) {
                const submenu = activeSubmenuItem.closest('.submenu');
                const navLink = submenu ? submenu.previousElementSibling : null;
                if (navLink && navLink.classList.contains('has-submenu')) {
                    navLink.classList.add('expanded');
                    if (submenu) {
                        submenu.classList.add('expanded');
                    }
                }
            }
        });

        function toggleNotificationDropdown(event) {
            if (event) {
                event.stopPropagation();
            }
            const panel = document.getElementById('notificationDropdownPanel');
            const btn = document.getElementById('notificationDropdownBtn');
            if (panel && btn) {
                const isVisible = panel.classList.contains('show');
                if (isVisible) {
                    panel.classList.remove('show');
                    btn.setAttribute('aria-expanded', 'false');
                } else {
                    panel.classList.add('show');
                    btn.setAttribute('aria-expanded', 'true');
                    // Close when clicking outside
                    setTimeout(() => {
                        const closeDropdown = function(e) {
                            const wrapper = document.querySelector('.notification-dropdown-wrapper');
                            if (wrapper && !wrapper.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
                                panel.classList.remove('show');
                                btn.setAttribute('aria-expanded', 'false');
                                document.removeEventListener('click', closeDropdown);
                            }
                        };
                        document.addEventListener('click', closeDropdown);
                    }, 100);
                }
            }
        }

        function markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.classList.remove('unread');
                        const readBtn = notificationItem.querySelector('.btn-read');
                        if (readBtn) {
                            readBtn.remove();
                        }
                        // Update badge count
                        updateNotificationBadge(data.unread_count || 0);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors du marquage de la notification comme lue.');
            });
        }

        function deleteNotification(notificationId) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                return;
            }

            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Erreur lors de la suppression de la notification.');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.style.transition = 'opacity 0.3s ease';
                        notificationItem.style.opacity = '0';
                        setTimeout(() => {
                            notificationItem.remove();
                            // Check if list is empty
                            const list = document.querySelector('.notification-dropdown-list');
                            if (list && list.children.length === 0) {
                                list.innerHTML = `
                                    <div class="notification-dropdown-empty">
                                        <i class="fas fa-bell-slash"></i>
                                        <p class="mb-0 text-muted">Aucune notification</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                    // Update badge count
                    updateNotificationBadge(data.unread_count || 0);
                } else {
                    alert(data.message || 'Erreur lors de la suppression de la notification.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Erreur lors de la suppression de la notification.');
            });
        }

        function markAllNotificationsAsRead() {
            fetch('/notifications/mark-all-read', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread class from all items
                    document.querySelectorAll('.notification-dropdown-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        const readBtn = item.querySelector('.btn-read');
                        if (readBtn) {
                            readBtn.remove();
                        }
                    });
                    // Remove mark all read button
                    const markAllBtn = document.querySelector('.btn-mark-all-read');
                    if (markAllBtn) {
                        markAllBtn.remove();
                    }
                    // Update badge
                    updateNotificationBadge(0);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors du marquage de toutes les notifications comme lues.');
            });
        }

        function updateNotificationBadge(count) {
            const badge = document.querySelector('.notification-badge');
            if (count > 0) {
                if (badge) {
                    badge.textContent = count > 9 ? '9+' : count;
                    badge.style.display = 'flex';
                } else {
                    // Create badge if it doesn't exist
                    const btn = document.getElementById('notificationDropdownBtn');
                    if (btn) {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'notification-badge';
                        newBadge.textContent = count > 9 ? '9+' : count;
                        btn.appendChild(newBadge);
                    }
                }
            } else {
                if (badge) {
                    badge.remove();
                }
            }
        }

        function toggleSubmenu(element) {
            const navItem = element.closest('.nav-item');
            const submenu = navItem.querySelector('.submenu');
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = sidebar && sidebar.classList.contains('collapsed');
            
            // Close other submenus
            document.querySelectorAll('.nav-link.has-submenu').forEach(link => {
                if (link !== element) {
                    link.classList.remove('expanded');
                    const otherSubmenu = link.closest('.nav-item').querySelector('.submenu');
                    if (otherSubmenu) {
                        otherSubmenu.classList.remove('expanded');
                    }
                }
            });
            
            // Toggle current submenu
            element.classList.toggle('expanded');
            if (submenu) {
                submenu.classList.toggle('expanded');
                
                // If sidebar is collapsed, position submenu correctly
                if (isCollapsed) {
                    const rect = navItem.getBoundingClientRect();
                    submenu.style.top = rect.top + 'px';
                }
            }
        }

        // Handle hover for collapsed sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                const handleSidebarHover = function() {
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    if (isCollapsed) {
                        const navItems = sidebar.querySelectorAll('.nav-item.has-submenu');
                        navItems.forEach(navItem => {
                            const navLink = navItem.querySelector('.nav-link.has-submenu');
                            const submenu = navItem.querySelector('.submenu');
                            
                            navItem.addEventListener('mouseenter', function() {
                                if (submenu && !submenu.classList.contains('expanded')) {
                                    const rect = navItem.getBoundingClientRect();
                                    submenu.style.top = rect.top + 'px';
                                    submenu.classList.add('expanded');
                                }
                            });
                            
                            navItem.addEventListener('mouseleave', function() {
                                if (submenu && !navLink.classList.contains('expanded')) {
                                    submenu.classList.remove('expanded');
                                }
                            });
                        });
                    }
                };
                
                // Check on load
                handleSidebarHover();
                
                // Check when sidebar state changes
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            handleSidebarHover();
                        }
                    });
                });

                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
        });

        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('collapsed');
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        }

        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
                    const sidebar = document.getElementById('sidebar');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebar && isCollapsed) {
                sidebar.classList.add('collapsed');
            }
        });
    </script>
</body>
</html> 
