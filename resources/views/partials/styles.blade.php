<style>
    /* CSS Variables */
    :root {
        --primary-color: #4a7c59;
        --primary-dark: #3d6b4a;
        --accent-color: #e67e22;
        --secondary-color: #6b7280;
        --success-color: #10b981;
        --success-dark: #059669;
        --danger-color: #ef4444;
        --danger-dark: #dc2626;
        --info-color: #3b82f6;
        --warning-color: #f59e0b;
        --purple-color: #8b5cf6;
        
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        
        --background-light: #ffffff;
        --background-medium: #f9fafb;
        --background-dark: #f3f4f6;
        
        --border-light: #e5e7eb;
        --border-medium: #d1d5db;
        
        --shadow-light: rgba(0, 0, 0, 0.05);
        --shadow-medium: rgba(0, 0, 0, 0.1);
        --shadow-heavy: rgba(0, 0, 0, 0.15);
        --shadow-lighter: rgba(0, 0, 0, 0.025);
        
        --anef-green: #4a7c59;
        --anef-orange: #e67e22;
        --anef-dark-green: #3d6b4a;
        
        --google-text: #202124;
        --google-gray: #5f6368;
    }

    /* Dark Mode Variables */
    .dark-mode {
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --text-muted: #9ca3af;
        
        --background-light: #1f2937;
        --background-medium: #111827;
        --background-dark: #0f172a;
        
        --border-light: #374151;
        --border-medium: #4b5563;
        
        --shadow-light: rgba(0, 0, 0, 0.2);
        --shadow-medium: rgba(0, 0, 0, 0.3);
        --shadow-heavy: rgba(0, 0, 0, 0.4);
        --shadow-lighter: rgba(0, 0, 0, 0.1);
    }

    /* Content Container - Depends on App Overflow */
    .content-scroll-container {
        height: auto;
        min-height: auto;
        overflow-y: auto; /* Enable vertical scrolling with custom style */
        overflow-x: hidden; /* Hide horizontal overflow */
        padding: 0.5rem 0; /* Add some internal spacing */
        margin: 0 -0.5rem; /* Compensate for padding */
        position: relative;
        scrollbar-width: none; /* Hide default Firefox scrollbar */
        -ms-overflow-style: none; /* Hide default IE scrollbar */
    }

    /* Custom Sidebar-Style Scrollbar */
    .content-scroll-container::-webkit-scrollbar {
        width: 6px; /* Thin scrollbar like sidebar */
    }

    .content-scroll-container::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 3px;
    }

    .content-scroll-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 3px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .content-scroll-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        transform: scaleX(1.2);
        box-shadow: 0 2px 8px rgba(74, 124, 89, 0.3);
    }

    .content-scroll-container::-webkit-scrollbar-corner {
        background: transparent;
    }

    /* Dark Mode Sidebar-Style Scrollbar */
    .dark-mode .content-scroll-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark-mode .content-scroll-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        box-shadow: 0 2px 8px rgba(74, 124, 89, 0.5);
    }

    /* Top Bar Fixed Sizing */
    .top-bar {
        height: 70px; /* Fixed height */
        min-height: 70px;
        max-height: 70px;
        padding: 0 2.5rem; /* Increased horizontal padding for better spacing */
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        position: fixed;
        top: 0;
        left: 280px; /* Perfect alignment with expanded sidebar */
        right: 0;
        z-index: 1000;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Top Bar when sidebar is collapsed */
    .sidebar.collapsed ~ .content-wrapper .top-bar {
        left: 70px; /* Perfect alignment with collapsed sidebar */
        padding: 0 2rem; /* Slightly reduced padding when collapsed */
        transform: translateX(0); /* Smooth transition */
    }

    /* Top Bar when sidebar is expanded */
    .sidebar:not(.collapsed) ~ .content-wrapper .top-bar {
        left: 280px; /* Perfect alignment with expanded sidebar */
        padding: 0 2.5rem; /* Full padding when expanded */
        transform: translateX(0); /* Smooth transition */
    }

    /* Smooth transition for top bar content */
    .top-bar-content {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .top-bar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        gap: 2rem; /* Better spacing between left and right sections */
    }

    /* Top Bar Elements Styling */
    .top-bar-left {
        display: flex;
        align-items: center;
    }

    .breadcrumbs {
        display: flex;
        align-items: center;
        gap: 0.75rem; /* Increased gap between breadcrumb items */
        font-size: 0.875rem;
        color: var(--text-secondary);
        padding: 0.5rem 0; /* Add vertical padding for better touch targets */
    }

    .breadcrumb-item {
        color: var(--text-primary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .breadcrumb-separator {
        color: var(--text-muted);
    }

    .top-bar-right {
        display: flex;
        align-items: center;
    }

    .top-bar-actions {
        display: flex;
        align-items: center;
        gap: 1.5rem; /* Increased gap between action buttons */
        margin-left: auto; /* Push actions to the right */
    }

    .top-bar-btn {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 0.75rem 1rem; /* Increased horizontal padding */
        display: flex;
        align-items: center;
        gap: 0.75rem; /* Increased gap between icon and text */
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        color: var(--text-primary);
        font-size: 0.875rem;
        font-weight: 500;
        height: 48px; /* Slightly increased height for better proportions */
        min-width: 48px; /* Increased minimum width */
        box-sizing: border-box;
    }

    .top-bar-btn:hover {
        background: rgba(255, 255, 255, 0.95);
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(74, 124, 89, 0.2);
    }

    .top-bar-btn:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
        background: rgba(255, 255, 255, 0.95);
        border-color: var(--primary-color);
    }

    .top-bar-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(74, 124, 89, 0.3);
    }

    .notification-btn {
        position: relative;
        min-width: 48px; /* Match new button size */
        justify-content: center;
        padding: 0.75rem; /* Consistent padding */
    }

    .notification-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: linear-gradient(135deg, var(--danger-color), var(--danger-dark));
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .dark-mode-btn {
        min-width: 48px; /* Match new button size */
        justify-content: center;
        padding: 0.75rem; /* Consistent padding */
    }

    .profile-btn {
        min-width: auto;
        padding: 0.75rem 1.25rem; /* Increased horizontal padding */
        height: 48px; /* Match new button height */
    }

    .profile-avatar {
        width: 32px; /* Slightly larger avatar */
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 8px rgba(74, 124, 89, 0.2);
        overflow: hidden;
    }

    .profile-avatar .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .profile-avatar-large {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.5rem;
        border: 3px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 12px rgba(74, 124, 89, 0.3);
        overflow: hidden;
    }

    .profile-avatar-large .avatar-img-large {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .profile-avatar-large .avatar-initials {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
    }

    .profile-name {
        color: var(--text-primary);
        font-size: 0.875rem;
        font-weight: 500;
        margin-left: 0.5rem; /* Better spacing from avatar */
    }

    .profile-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .profile-details h6 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .profile-details span {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .profile-ppr {
        font-size: 0.75rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Dark Mode Top Bar Enhancements */
    .dark-mode .top-bar {
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.98) 0%, rgba(17, 24, 39, 0.98) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dark-mode .top-bar-btn {
        background: rgba(31, 41, 55, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }

    .dark-mode .top-bar-btn:hover {
        background: rgba(31, 41, 55, 0.95);
        border-color: var(--primary-color);
    }

    /* Content Area Styling */
    .content-area {
        padding: 1.5rem 2rem; /* Match top bar padding */
        padding-top: 1.5rem; /* Better top spacing for content */
        height: 100%;
        box-sizing: border-box;
        width: 100%;
        background: transparent; /* Inherit from content-wrapper */
        backdrop-filter: none; /* No additional blur */
        position: relative; /* For pseudo-element positioning */
    }

    /* Allow container-fluid to use full width */
    .content-area .container-fluid {
        max-width: 100%;
        padding-left: 0;
        padding-right: 0;
    }

    /* Main content positioning - now handled by content-wrapper */
    .main-content {
        padding: 0;
        min-height: 100vh;
        position: relative;
        box-sizing: border-box;
        background: transparent; /* Inherit from content-wrapper */
        backdrop-filter: none;
        border-bottom: none;
        box-shadow: none;
    }

    /* Content Wrapper positioning for sidebar toggle */
    .content-wrapper {
        margin-left: 280px; /* Perfect alignment with expanded sidebar */
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 100vh;
        position: relative;
        padding-top: 85px; /* Account for top bar height */
        box-sizing: border-box;
    }

    .sidebar.collapsed ~ .content-wrapper {
        margin-left: 70px; /* Perfect alignment with collapsed sidebar */
    }

    /* Content Header Styling */
    .content-header {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, var(--background-light) 100%);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        padding: 1.5rem 3rem;
        margin-bottom: 2rem;
        position: relative;
        border-radius: 16px;
        margin: 0 0 2rem 0;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }

    .greeting-section {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .greeting h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        background: linear-gradient(135deg, #1e293b 0%, #4a7c59 50%, #e67e22 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .greeting p {
        color: var(--text-secondary);
        margin: 0.5rem 0 0 0;
        font-size: 1rem;
        font-weight: 500;
    }

    /* Enhanced Content Spacing */
    .content-scroll-container > * {
        margin-bottom: 1.5rem; /* Consistent spacing between content blocks */
    }
    
    /* Enhanced Content Spacing */
    .content-scroll-container > * {
        margin-bottom: 1.5rem; /* Consistent spacing between content blocks */
    }
    
    .content-scroll-container > *:last-child {
        margin-bottom: 0; /* No margin on last element */
    }
    
    /* Content Wrapper Visual Integration with Top Bar */
    /* .content-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.5) 20%, 
            rgba(255, 255, 255, 0.8) 50%, 
            rgba(255, 255, 255, 0.5) 80%, 
            transparent 100%);
        z-index: 1;
    } */
    
    /* Content Area Visual Enhancement */
    /* .content-area::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, 
            var(--primary-color) 0%, 
            var(--accent-color) 25%, 
            var(--success-color) 50%, 
            var(--info-color) 75%, 
            var(--purple-color) 100%);
        opacity: 0.1;
        border-radius: 0 0 2px 2px;
    } */

    /* Dark Mode Content Enhancements */
    .dark-mode .content-wrapper {
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.98) 0%, rgba(17, 24, 39, 0.98) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dark-mode .content-area::before {
        opacity: 0.2; /* More visible in dark mode */
    }

    .dark-mode .content-header {
        background: linear-gradient(135deg, rgba(31, 41, 55, 0.95) 0%, var(--background-light) 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Card and Component Spacing */
    .card, .x-card, [class*="card"] {
        margin-bottom: 1.5rem;
        border-radius: 16px;
        /* box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); */
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card:hover, .x-card:hover, [class*="card"]:hover {
        transform: translateY(-2px);
        /* box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12); */
    }

    /* Dark Mode Card Enhancements */
    .dark-mode .card, .dark-mode .x-card, .dark-mode [class*="card"] {
        background: var(--background-light);
        border: 1px solid var(--border-light);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .dark-mode .card:hover, .dark-mode .x-card:hover, .dark-mode [class*="card"]:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
    }
    
    /* Stats Grid Spacing */
    .stats-grid, [class*="stats"] {
        margin-bottom: 2rem;
        gap: 1.5rem;
    }
    
    /* Filter Section Spacing */
    .filter-section, [class*="filter"] {
        margin-bottom: 2rem;
    }
    
    /* Data Table Spacing */
    .data-table, [class*="table"] {
        margin-bottom: 2rem;
    }
    
    /* Import Export Section Spacing */
    .import-export-section, [class*="import"], [class*="export"] {
        margin-bottom: 2rem;
    }
    
    /* Alert Spacing */
    .alert, [class*="alert"] {
        margin-bottom: 1.5rem;
        border-radius: 12px;
    }
    
    /* Form Spacing */
    form {
        margin-bottom: 1.5rem;
    }
    
    /* Button Group Spacing */
    .btn-group, .button-group, [class*="btn"], [class*="button"] {
        margin-bottom: 1rem;
    }
    
    /* Print Styles */
    @media print {
        .content-scroll-container {
            height: auto;
            overflow: visible;
        }
        
        .top-bar,
        .sidebar,
        .content-wrapper::before,
        .content-area::before {
            display: none; /* Hide decorative elements in print */
        }
        
        .content-wrapper {
            margin: 0;
            padding: 1rem;
            background: white;
            box-shadow: none;
        }
    }

    /* Performance Optimizations */
    .top-bar,
    .content-wrapper,
    .content-area {
        /* will-change: transform, left, margin-left; */
        backface-visibility: hidden;
    }

    /* Enhanced Mobile Responsive Design */
    
    /* Extra Small devices (phones, 576px and down) */
    @media (max-width: 575.98px) {
        .content-wrapper {
            margin-left: 0;
            padding: 0;
            padding-top: 70px;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 0;
        }
        
        .top-bar {
            height: 60px;
            min-height: 60px;
            max-height: 60px;
            padding: 0 0.75rem;
            left: 0;
            right: 0;
        }
        
        .top-bar-content {
            flex-direction: column;
            gap: 0.75rem;
            align-items: stretch;
        }
        
        .top-bar-left, .top-bar-right {
            justify-content: center;
            width: 100%;
        }
        
        .top-bar-actions {
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .top-bar-btn {
            height: 40px;
            min-width: 40px;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }
        
        .profile-btn {
            height: 40px;
            padding: 0.5rem 0.75rem;
        }
        
        .profile-avatar {
            width: 24px;
            height: 24px;
            font-size: 0.7rem;
        }
        
        .profile-name {
            display: none;
        }
        
        .breadcrumbs {
            font-size: 0.75rem;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .content-area {
            padding: 0.75rem 1rem;
            max-width: 100%;
            overflow: visible;
        }
        
        .content-header {
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .greeting h1 {
            font-size: 1.25rem;
            text-align: center;
        }
        
        .greeting p {
            font-size: 0.8rem;
            text-align: center;
        }
        
        .greeting-section {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        
        .content-scroll-container {
            height: auto;
            min-height: auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.125rem 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .content-scroll-container::-webkit-scrollbar {
            width: 4px;
        }
    }

    /* Small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .content-wrapper {
            margin-left: 0;
            padding: 0;
            padding-top: 75px;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 0;
        }
        
        .top-bar {
            height: 65px;
            min-height: 65px;
            max-height: 65px;
            padding: 0 1rem;
            left: 0;
            right: 0;
        }
        
        .top-bar-content {
            flex-direction: row;
            gap: 1.5rem;
            align-items: center;
        }
        
        .top-bar-btn {
            height: 42px;
            min-width: 42px;
            padding: 0.5rem 0.875rem;
            font-size: 0.825rem;
        }
        
        .profile-btn {
            height: 42px;
            padding: 0.5rem 1rem;
        }
        
        .profile-avatar {
            width: 26px;
            height: 26px;
            font-size: 0.75rem;
        }
        
        .content-area {
            padding: 1rem 1.5rem;
            max-width: 100%;
            overflow: visible;
        }
        
        .greeting h1 {
            font-size: 1.4rem;
        }
        
        .greeting p {
            font-size: 0.85rem;
        }
        
        .content-scroll-container {
            height: auto;
            min-height: auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.25rem 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
    }

    /* Medium devices (tablets, 768px and up) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .content-wrapper {
            margin-left: 0;
            padding: 0;
            padding-top: 83px;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 0;
        }
        
        .top-bar {
            height: 68px;
            min-height: 68px;
            max-height: 68px;
            padding: 0 1.5rem;
            left: 0;
            right: 0;
        }
        
        .top-bar-content {
            gap: 1.75rem;
        }
        
        .top-bar-btn {
            height: 46px;
            min-width: 46px;
            padding: 0.625rem 0.875rem;
        }
        
        .profile-btn {
            height: 46px;
            padding: 0.625rem 1.125rem;
        }
        
        .profile-avatar {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
        
        .content-area {
            padding: 1.25rem 1.75rem;
            max-width: 100%;
            overflow: visible;
        }
        
        .greeting h1 {
            font-size: 1.6rem;
        }
        
        .greeting p {
            font-size: 0.9rem;
        }
        
        .content-scroll-container {
            height: auto;
            min-height: auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.375rem 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
    }

    /* Large devices (desktops, 992px and up) */
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .content-wrapper {
            margin-left: 280px;
            padding-top: 85px;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 70px;
        }
        
        .top-bar {
            height: 70px;
            min-height: 70px;
            max-height: 70px;
            padding: 0 2rem;
            left: 280px;
            right: 0;
        }
        
        .sidebar.collapsed ~ .content-wrapper .top-bar {
            left: 70px;
        }
        
        .top-bar-content {
            gap: 2rem;
        }
        
        .top-bar-btn {
            height: 48px;
            min-width: 48px;
            padding: 0.75rem 1rem;
        }
        
        .profile-btn {
            height: 48px;
            padding: 0.75rem 1.25rem;
        }
        
        .profile-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
        
        .content-area {
            padding: 1.5rem 2rem;
        }
        
        .greeting h1 {
            font-size: 1.8rem;
        }
        
        .greeting p {
            font-size: 0.95rem;
        }
        
        .content-scroll-container {
            height: auto;
            min-height: auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0;
        }
    }

    /* Extra large devices (large desktops, 1200px and up) */
    @media (min-width: 1200px) {
        .content-wrapper {
            margin-left: 280px;
            padding-top: 85px;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 70px;
        }
        
        .top-bar {
            height: 70px;
            min-height: 70px;
            max-height: 70px;
            padding: 0 2.5rem;
            left: 280px;
            right: 0;
        }
        
        .sidebar.collapsed ~ .content-wrapper .top-bar {
            left: 70px;
        }
        
        .top-bar-content {
            gap: 2rem;
        }
        
        .top-bar-btn {
            height: 48px;
            min-width: 48px;
            padding: 0.75rem 1rem;
        }
        
        .profile-btn {
            height: 48px;
            padding: 0.75rem 1.25rem;
        }
        
        .profile-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
        
        .content-area {
            padding: 1.5rem 2rem;
        }
        
        .greeting h1 {
            font-size: 2rem;
        }
        
        .greeting p {
            font-size: 1rem;
        }
        
        .content-scroll-container {
            height: auto;
            min-height: auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0;
        }
    }

    /* Landscape orientation adjustments */
    @media (orientation: landscape) and (max-height: 500px) {
        .top-bar {
            height: 60px;
            min-height: 60px;
            max-height: 60px;
        }
        
        .content-wrapper {
            padding-top: 70px;
        }
        
        .content-area {
            padding: 1rem 1.5rem;
        }
        
        .content-header {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
        }
        
        .greeting h1 {
            font-size: 1.4rem;
        }
        
        .greeting p {
            font-size: 0.85rem;
        }
    }

    /* High DPI displays */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .top-bar {
            border-bottom-width: 0.5px;
        }
        
        .content-header {
            border-bottom-width: 0.5px;
        }
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
        .top-bar-btn {
            min-height: 44px; /* Minimum touch target size */
            min-width: 44px;
        }
        
        .profile-btn {
            min-height: 44px;
        }
        
        .top-bar-btn:hover {
            transform: none; /* Disable hover effects on touch devices */
        }
        
        .top-bar-btn:active {
            transform: scale(0.98);
        }
    }

    /* Accessibility improvements for mobile */
    @media (max-width: 768px) {
        .top-bar-btn:focus {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        .breadcrumb-item:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 1px;
        }
    }

    /* Reduced motion preferences */
    @media (prefers-reduced-motion: reduce) {
        .top-bar,
        .content-wrapper,
        .top-bar-btn,
        .profile-btn {
            transition: none;
        }
        
        .top-bar-btn:hover {
            transform: none;
        }
    }

    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .top-bar {
            border-bottom-width: 2px;
            border-bottom-color: #000;
        }
        
        .content-header {
            border-bottom-width: 2px;
            border-bottom-color: #000;
        }
        
        .top-bar-btn {
            border-width: 2px;
        }
    }

    /* Mobile Sidebar Responsive Design */
    
    /* Mobile Sidebar Toggle Button */
    .mobile-sidebar-toggle {
        display: none;
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1001;
        background: var(--primary-color);
        border: none;
        border-radius: 8px;
        padding: 0.75rem;
        color: white;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .mobile-sidebar-toggle:hover {
        background: var(--primary-dark);
        transform: scale(1.05);
    }

    .mobile-sidebar-toggle:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* Mobile Sidebar Overlay */
    .mobile-sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .mobile-sidebar-overlay.active {
        opacity: 1;
    }

    /* Mobile Sidebar Styles */
    @media (max-width: 991.98px) {
        .sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            height: 100vh;
            width: 280px;
            z-index: 1000;
            transition: left 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar.collapsed {
            width: 280px;
            left: -280px;
        }

        .sidebar.collapsed.active {
            left: 0;
        }

        .mobile-sidebar-toggle {
            display: block;
        }

        .mobile-sidebar-overlay {
            display: block;
        }

        .content-wrapper {
            margin-left: 0 !important;
            padding-top: 70px;
        }

        .top-bar {
            left: 0 !important;
            right: 0 !important;
            padding-left: 4rem;
        }

        /* Mobile Sidebar Content Adjustments */
        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav-item {
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 8px;
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        .sidebar-nav-item:hover {
            background: rgba(74, 124, 89, 0.1);
        }

        .sidebar-nav-item.active {
            background: var(--primary-color);
            color: white;
        }

        .sidebar-nav-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Mobile Sidebar Footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            border-top: 1px solid var(--border-light);
            background: var(--background-light);
        }

        .dark-mode .sidebar-footer {
            background: var(--background-light);
            border-top-color: var(--border-light);
        }
    }

    /* Extra Small Mobile Sidebar */
    @media (max-width: 575.98px) {
        .sidebar {
            width: 100vw;
            left: -100vw;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar.collapsed {
            width: 100vw;
            left: -100vw;
        }

        .sidebar.collapsed.active {
            left: 0;
        }

        .mobile-sidebar-toggle {
            top: 10px;
            left: 10px;
            padding: 0.5rem;
        }

        .top-bar {
            padding-left: 3.5rem;
        }

        .sidebar-header {
            padding: 0.75rem;
        }

        .sidebar-nav-item {
            padding: 0.625rem 0.75rem;
            margin: 0.125rem 0.25rem;
            font-size: 0.9rem;
        }

        .sidebar-nav-item i {
            margin-right: 0.5rem;
            width: 18px;
        }

        .sidebar-footer {
            padding: 0.75rem;
        }
    }

    /* Small Mobile Sidebar */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .sidebar {
            width: 320px;
            left: -320px;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar.collapsed {
            width: 320px;
            left: -320px;
        }

        .sidebar.collapsed.active {
            left: 0;
        }

        .top-bar {
            padding-left: 4.5rem;
        }
    }

    /* Tablet Sidebar */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .sidebar {
            width: 300px;
            left: -300px;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar.collapsed {
            width: 300px;
            left: -300px;
        }

        .sidebar.collapsed.active {
            left: 0;
        }

        .top-bar {
            padding-left: 5rem;
        }
    }

    /* Mobile Sidebar Animation */
    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .mobile-sidebar-overlay {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    }

    /* Mobile Sidebar Backdrop */
    @media (max-width: 991.98px) {
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.95) 0%, 
                rgba(248, 250, 252, 0.95) 100%);
            backdrop-filter: blur(20px);
            z-index: -1;
        }

        .dark-mode .sidebar::before {
            background: linear-gradient(135deg, 
                rgba(31, 41, 55, 0.95) 0%, 
                rgba(17, 24, 39, 0.95) 100%);
        }
    }

    /* Mobile Sidebar Scroll */
    @media (max-width: 991.98px) {
        .sidebar {
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    }

    /* Mobile Sidebar Focus States */
    @media (max-width: 991.98px) {
        .sidebar-nav-item:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }

        .mobile-sidebar-toggle:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    }

    /* Mobile Sidebar Print */
    @media print {
        .mobile-sidebar-toggle,
        .mobile-sidebar-overlay,
        .sidebar {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }

        .top-bar {
            position: static !important;
            padding-left: 0 !important;
        }
    }

    /* Mobile Sidebar Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .sidebar,
        .mobile-sidebar-overlay {
            transition: none;
        }

        .mobile-sidebar-toggle:hover {
            transform: none;
        }
    }

    /* Mobile Sidebar High Contrast */
    @media (prefers-contrast: high) {
        .sidebar {
            border-right: 2px solid #000;
        }

        .sidebar-nav-item {
            border: 1px solid transparent;
        }

        .sidebar-nav-item:hover,
        .sidebar-nav-item.active {
            border-color: #000;
        }
    }

    /* Data Table Global Styles - Fixed Layout */
    .data-table-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .table-container {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }

    .table-responsive {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
    }

    .data-table {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        table-layout: fixed !important;
        margin: 0 !important;
    }

    .table-header-cell,
    .table-cell {
        width: auto !important;
        min-width: auto !important;
        max-width: none !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }

    /* Ensure page content doesn't shift based on table width */
    .main-content {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .content-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    /* Container consistency */
    .container,
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    /* Card consistency */
    .card {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }

    .card-body {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
    }

    /* Sidebar Notifications */
    .sidebar-notifications {
        padding: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .notification-count {
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-left: auto;
        margin-right: 0.5rem;
    }

    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: 250px;
        background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .logo {
        padding: 1.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 700;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo-icon {
        font-size: 1.5rem;
        color: var(--accent-color);
    }

    .nav-menu {
        flex: 1;
        list-style: none;
        padding: 1rem 0;
        margin: 0;
        overflow-y: auto;
    }

    .nav-item {
        margin: 0.25rem 0;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1.5rem;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        border-right: 3px solid var(--accent-color);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    .sidebar-toggle {
        position: absolute;
        top: 1rem;
        right: -15px;
        background: var(--primary-color);
        border: none;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .sidebar-toggle:hover {
        background: var(--primary-dark);
        transform: scale(1.1);
    }

    .sidebar.collapsed {
        width: 70px;
    }

    .sidebar.collapsed .logo span,
    .sidebar.collapsed .nav-link span {
        display: none;
    }

    .sidebar.collapsed .nav-link {
        justify-content: center;
        padding: 0.75rem;
    }

    .sidebar.collapsed .sidebar-toggle i {
        transform: rotate(180deg);
    }

    /* Submenu Styles */
    .nav-item.has-submenu {
        position: relative;
    }

    .nav-link.has-submenu {
        cursor: pointer;
        position: relative;
    }

    .nav-link.has-submenu::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 1.5rem;
        transition: transform 0.3s ease;
    }

    .nav-link.has-submenu.expanded::after {
        transform: rotate(180deg);
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        padding-left: 0;
        margin: 0;
        list-style: none;
    }

    .submenu.expanded {
        max-height: 500px;
        opacity: 1;
    }

    .submenu-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1.5rem 0.5rem 3.5rem;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .submenu-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        padding-left: 4rem;
    }

    .submenu-item.active {
        background-color: rgba(255, 255, 255, 0.15);
        color: white;
        border-left: 3px solid var(--accent-color);
    }

    .submenu-item i {
        width: 16px;
        text-align: center;
        font-size: 0.85rem;
    }

    /* Collapsed sidebar submenu styles */
    .sidebar.collapsed .submenu {
        position: absolute;
        left: 70px;
        top: 0;
        background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        min-width: 200px;
        box-shadow: 4px 0 12px rgba(0, 0, 0, 0.2);
        border-radius: 0 8px 8px 0;
        z-index: 1001;
        padding: 0.5rem 0;
        max-height: none !important;
        opacity: 1 !important;
    }

    .sidebar.collapsed .submenu.expanded {
        display: block;
    }

    .sidebar.collapsed .submenu:not(.expanded) {
        display: none;
    }

    .sidebar.collapsed .nav-link.has-submenu::after {
        display: none;
    }

    .sidebar.collapsed .nav-link.has-submenu:hover + .submenu,
    .sidebar.collapsed .nav-item.has-submenu:hover .submenu {
        display: block;
    }

    .sidebar.collapsed .submenu-item {
        padding: 0.75rem 1rem;
        padding-left: 1rem;
        white-space: nowrap;
    }

    .sidebar.collapsed .submenu-item span {
        display: inline;
    }

    .sidebar.collapsed .submenu-item i {
        margin-right: 0.75rem;
    }

    /* ============================================
       UX IMPROVEMENTS - BUTTONS, LOADING, ANIMATIONS
       ============================================ */

    /* Enhanced Button Styles */
    .btn, button[type="submit"], button[type="button"], a.btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn:focus-visible {
        outline: 3px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* Button Loading State */
    .btn-loading {
        pointer-events: none;
        opacity: 0.7;
        position: relative;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to { transform: rotate(360deg); }
    }

    /* Skeleton Loaders */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s ease-in-out infinite;
        border-radius: 8px;
    }

    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .skeleton-text {
        height: 1em;
        margin: 0.5rem 0;
    }

    .skeleton-title {
        height: 2em;
        width: 60%;
        margin-bottom: 1rem;
    }

    .skeleton-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }

    /* Enhanced Form Inputs */
    .form-input, input[type="text"], input[type="number"], input[type="email"], textarea, select {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid var(--border-medium);
        background: rgba(255, 255, 255, 0.95);
    }

    .form-input:hover {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
    }

    .form-input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 4px rgba(74, 124, 89, 0.15);
        transform: translateY(-1px);
    }

    /* Enhanced Cards */
    .card, .bg-white, [class*="card"] {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-light);
        position: relative;
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, 
            var(--primary-color) 0%, 
            var(--accent-color) 50%, 
            var(--success-color) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .card:hover::before {
        opacity: 1;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    /* Enhanced Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Toast Notifications */
    .toast-container {
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .toast {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
        padding: 1rem 1.5rem;
        animation: toast-slide-in 0.3s ease-out;
        border-left: 4px solid var(--primary-color);
    }

    @keyframes toast-slide-in {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast-success {
        border-left-color: var(--success-color);
    }

    .toast-error {
        border-left-color: var(--danger-color);
    }

    .toast-info {
        border-left-color: var(--info-color);
    }

    .toast-warning {
        border-left-color: var(--warning-color);
    }

    /* Enhanced Empty States */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
    }

    .empty-state-icon {
        font-size: 4rem;
        opacity: 0.5;
        margin-bottom: 1.5rem;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .empty-state-text {
        font-size: 1rem;
        color: var(--text-secondary);
    }

    /* Enhanced Tables */
    .data-table tbody tr {
        transition: all 0.2s ease;
    }

    .data-table tbody tr:hover {
        background: rgba(74, 124, 89, 0.05);
        transform: scale(1.01);
    }

    .data-table tbody tr:active {
        transform: scale(0.99);
        background: rgba(74, 124, 89, 0.1);
    }

    /* Enhanced Search/Filter */
    .search-box, .filter-box {
        position: relative;
    }

    .search-box input:focus,
    .filter-box select:focus {
        padding-right: 2.5rem;
    }

    .search-icon, .filter-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }

    /* Enhanced Pagination */
    .pagination {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }

    .pagination a, .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0 0.75rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .pagination a:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .pagination .active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(74, 124, 89, 0.3);
    }

    /* Enhanced Stats Cards */
    .stat-card {
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stat-card:hover::after {
        opacity: 1;
    }

    /* Smooth Page Transitions */
    @keyframes page-fade-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .page-transition {
        animation: page-fade-in 0.4s ease-out;
    }

    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        /* Keep below Bootstrap modals/backdrops to avoid blocking them */
        z-index: 900;
    }

    /* If a Bootstrap modal is open, never let the loading overlay block it */
    body.modal-open .loading-overlay {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid var(--border-light);
        border-top-color: var(--primary-color);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Enhance Scrollbars */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 5px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    }

    /* Enhanced Focus States for Accessibility */
    *:focus-visible {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
        border-radius: 4px;
    }

    /* Smooth Mobile Touch Actions */
    @media (hover: none) and (pointer: coarse) {
        * {
            -webkit-tap-highlight-color: rgba(74, 124, 89, 0.1);
        }

        .btn:active, button:active, a:active {
            transform: scale(0.98);
        }
    }

    /* Print Optimizations */
    @media print {
        .btn-loading::after,
        .loading-overlay,
        .skeleton {
            display: none !important;
        }
    }

    /* Dark Mode Enhancements */
    .dark-mode .skeleton {
        background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
        background-size: 200% 100%;
    }

    .dark-mode .form-input {
        background: rgba(31, 41, 55, 0.95);
        border-color: var(--border-medium);
    }

    .dark-mode .toast {
        background: rgba(31, 41, 55, 0.95);
        color: var(--text-primary);
    }

    .dark-mode .loading-overlay {
        background: rgba(17, 24, 39, 0.9);
    }
</style>
