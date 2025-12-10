<!-- Material Design Web Components JS -->
<script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Mobile Sidebar Functions
    function toggleMobileSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.mobile-sidebar-overlay');
        
        if (sidebar) {
            sidebar.classList.toggle('active');
        }
        if (overlay) {
            overlay.classList.toggle('active');
        }
        
        // Prevent body scroll when sidebar is open
        if (sidebar && sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    function closeMobileSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.mobile-sidebar-overlay');
        
        if (sidebar) {
            sidebar.classList.remove('active');
        }
        if (overlay) {
            overlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    // Close mobile sidebar when clicking on a link
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarLinks = document.querySelectorAll('.sidebar a, .sidebar button');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991.98) {
                    closeMobileSidebar();
                }
            });
        });
    });

    // Close mobile sidebar on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    // Handle window resize for mobile sidebar
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991.98) {
            closeMobileSidebar();
        }
    });

    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            } else {
                sidebar.classList.add('show');
            }
        }
    }

    function toggleSidebarCollapse() {
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebar) {
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
            } else {
                sidebar.classList.add('collapsed');
            }
        }
    }
    
    function toggleSubmenu(element) {
        const navItem = element.closest('.nav-item');
        const submenu = navItem.querySelector('.submenu');
        const isExpanded = element.classList.contains('expanded');
        
        // Close all other submenus
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
        }
    }
    
    // Auto-expand submenu if current page is active

    // Select Search Component Functions
    // Global state for select components
    window.selectSearchComponents = window.selectSearchComponents || {};

    // Initialize select search component
    function initSelectSearch(selectId) {
        if (window.selectSearchComponents[selectId]) {
            return; // Already initialized
        }

        const component = {
            id: selectId,
            isOpen: false,
            selectedValues: [],
            selectedLabels: [],
            multiple: false,
            maxItems: null
        };

        // Get component configuration
        const wrapper = document.getElementById(`select-wrapper-${selectId}`);
        const hiddenInput = document.getElementById(selectId);
        const display = wrapper.querySelector('.select-search-display');
        const dropdown = wrapper.querySelector('.select-search-dropdown');
        const options = wrapper.querySelectorAll('.option-item');

        // Check if multiple selection is enabled
        component.multiple = wrapper.querySelector('.select-search-display')?.classList.contains('multiple') || false;
        
        // Get max items if specified
        const maxItemsAttr = wrapper.querySelector('.select-search-container')?.getAttribute('data-max-items');
        component.maxItems = maxItemsAttr ? parseInt(maxItemsAttr) : null;

        // Initialize selected values
        if (hiddenInput.value) {
            if (component.multiple) {
                component.selectedValues = hiddenInput.value.split(',').filter(v => v.trim());
            } else {
                component.selectedValues = [hiddenInput.value];
            }
        }

        // Update selected labels
        component.selectedLabels = [];
        options.forEach(option => {
            if (component.selectedValues.includes(option.dataset.value)) {
                component.selectedLabels.push(option.dataset.label);
            }
        });

        // Store component reference
        window.selectSearchComponents[selectId] = component;

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!wrapper.contains(event.target)) {
                closeSelectSearch(selectId);
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeSelectSearch(selectId);
            }
        });
    }

    // Toggle select dropdown
    function toggleSelectSearch(selectId) {
        const component = window.selectSearchComponents[selectId];
        if (!component) {
            initSelectSearch(selectId);
        }

        if (component.isOpen) {
            closeSelectSearch(selectId);
        } else {
            openSelectSearch(selectId);
        }
    }

    // Open select dropdown
    function openSelectSearch(selectId) {
        const wrapper = document.getElementById(`select-wrapper-${selectId}`);
        const display = wrapper.querySelector('.select-search-display');
        const dropdown = wrapper.querySelector('.select-search-dropdown');
        const searchInput = dropdown.querySelector('.search-input');

        // Close other open dropdowns
        Object.keys(window.selectSearchComponents).forEach(id => {
            if (id !== selectId) {
                closeSelectSearch(id);
            }
        });

        // Open this dropdown
        display.classList.add('open');
        dropdown.style.display = 'block';
        window.selectSearchComponents[selectId].isOpen = true;

        // Focus search input
        setTimeout(() => {
            searchInput.focus();
        }, 100);
    }

    // Close select dropdown
    function closeSelectSearch(selectId) {
        const wrapper = document.getElementById(`select-wrapper-${selectId}`);
        const display = wrapper.querySelector('.select-search-display');
        const dropdown = wrapper.querySelector('.select-search-dropdown');

        display.classList.remove('open');
        dropdown.style.display = 'none';
        window.selectSearchComponents[selectId].isOpen = false;
    }

    // Filter options based on search input
    function filterSelectOptions(selectId, searchTerm) {
        const wrapper = document.getElementById(`select-wrapper-${selectId}`);
        const options = wrapper.querySelectorAll('.option-item');
        const searchLower = searchTerm.toLowerCase();

        options.forEach(option => {
            const text = option.dataset.label.toLowerCase();
            if (text.includes(searchLower)) {
                option.classList.remove('hidden');
            } else {
                option.classList.add('hidden');
            }
        });
    }

    // Select an option
    function selectOption(selectId, value, label, multiple = false) {
        const component = window.selectSearchComponents[selectId];
        const wrapper = document.getElementById(`select-wrapper-${selectId}`);
        const hiddenInput = document.getElementById(selectId);
        const selectedItems = wrapper.querySelector('.selected-items');
        const placeholder = wrapper.querySelector('.placeholder-text');

        if (multiple) {
            // Multiple selection
            if (component.selectedValues.includes(value)) {
                // Remove if already selected
                component.selectedValues = component.selectedValues.filter(v => v !== value);
                component.selectedLabels = component.selectedLabels.filter(l => l !== label);
            } else {
                // Add if not selected
                if (component.maxItems && component.selectedValues.length >= component.maxItems) {
                    alert(`Vous ne pouvez sélectionner que ${component.maxItems} éléments maximum.`);
                    return;
                }
                component.selectedValues.push(value);
                component.selectedLabels.push(label);
            }
        } else {
            // Single selection
            component.selectedValues = [value];
            component.selectedLabels = [label];
            closeSelectSearch(selectId);
        }

        // Update hidden input
        hiddenInput.value = component.selectedValues.join(',');

        // Update display
        updateSelectDisplay(selectId);

        // Update option visual states
        updateOptionStates(selectId);
    }

    // Remove selected item (for multiple selection)
    function removeSelectedItem(selectId, label) {
        const component = window.selectSearchComponents[selectId];
        const value = component.selectedValues[component.selectedLabels.indexOf(label)];

        component.selectedValues = component.selectedValues.filter(v => v !== value);
        component.selectedLabels = component.selectedLabels.filter(l => l !== label);

        const hiddenInput = document.getElementById(selectId);
        hiddenInput.value = component.selectedValues.join(',');

        updateSelectDisplay(selectId);
        updateOptionStates(selectId);
    }

    // Clear selection
    function clearSelection(selectId) {
        const component = window.selectSearchComponents[selectId];
        component.selectedValues = [];
        component.selectedLabels = [];

        const hiddenInput = document.getElementById(selectId);
        hiddenInput.value = '';

        updateSelectDisplay(selectId);
        updateOptionStates(selectId);
        closeSelectSearch(selectId);
    }

    // Update select display
    function updateSelectDisplay(selectId) {
        const wrapper = document.getElementById(`select-wrapper-${selectId}`);
        const selectedItems = wrapper.querySelector('.selected-items');
        const placeholder = wrapper.querySelector('.placeholder-text');

        if (window.selectSearchComponents[selectId].selectedValues.length > 0) {
            // Show selected items
            if (window.selectSearchComponents[selectId].multiple) {
                selectedItems.innerHTML = '';
                window.selectSearchComponents[selectId].selectedLabels.forEach(label => {
                    const itemSpan = document.createElement('span');
                    itemSpan.className = 'selected-item';
                    itemSpan.innerHTML = `
                        ${label}
                        <button type="button" class="remove-item" onclick="removeSelectedItem('${selectId}', '${label}')">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    selectedItems.appendChild(itemSpan);
                });
                placeholder.style.display = 'none';
            } else {
                selectedItems.innerHTML = `<span class="selected-text">${window.selectSearchComponents[selectId].selectedLabels[0]}</span>`;
                placeholder.style.display = 'none';
            }
        } else {
            // Show placeholder
            selectedItems.innerHTML = '';
            placeholder.style.display = 'inline';
        }
    }

    // Update option visual states
    function updateOptionStates(selectId) {
        const wrapper = document.getElementById(`select-wrapper-${selectId}`);
        const options = wrapper.querySelectorAll('.option-item');
        const component = window.selectSearchComponents[selectId];

        options.forEach(option => {
            const value = option.dataset.value;
            if (component.selectedValues.includes(value)) {
                option.classList.add('selected');
                option.querySelector('.selected-icon')?.remove();
                const icon = document.createElement('i');
                icon.className = 'fas fa-check selected-icon';
                option.appendChild(icon);
            } else {
                option.classList.remove('selected');
                option.querySelector('.selected-icon')?.remove();
            }
        });
    }

    // Initialize all select search components on page load
    document.addEventListener('DOMContentLoaded', function() {
        const selectWrappers = document.querySelectorAll('.select-search-wrapper');
        selectWrappers.forEach(wrapper => {
            const selectId = wrapper.querySelector('[id]').id;
            initSelectSearch(selectId);
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const activeSubmenuItem = document.querySelector('.submenu-item.active');
        if (activeSubmenuItem) {
            const submenu = activeSubmenuItem.closest('.submenu');
            const navLink = submenu ? submenu.previousElementSibling : null;
            if (navLink) {
                navLink.classList.add('expanded');
            }
            if (submenu) {
                submenu.classList.add('expanded');
            }
        }
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.sidebar');
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        
        if (sidebar && mobileToggle && window.innerWidth <= 768 && 
            !sidebar.contains(event.target) && 
            !mobileToggle.contains(event.target) &&
            sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar && window.innerWidth > 768) {
            sidebar.classList.remove('show');
        }
    });

    // Notification functions
    function toggleNotifications() {
        const panel = document.getElementById('notificationPanel');
        panel.classList.toggle('show');
        
        // Close profile panel if open
        const profilePanel = document.getElementById('profilePanel');
        if (profilePanel) {
            profilePanel.classList.remove('show');
        }
    }

    function markAllAsRead() {
        const unreadItems = document.querySelectorAll('.notification-item.unread');
        unreadItems.forEach(item => {
            item.classList.remove('unread');
        });
        
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            badge.style.display = 'none';
        }
    }

    // Profile functions
    function toggleProfile() {
        const panel = document.getElementById('profilePanel');
        panel.classList.toggle('show');
        
        // Close notification panel if open
        const notificationPanel = document.getElementById('notificationPanel');
        if (notificationPanel) {
            notificationPanel.classList.remove('show');
        }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const notificationDropdown = document.querySelector('.notification-dropdown');
        const profileDropdown = document.querySelector('.profile-dropdown');
        
        // Close notification panel if clicking outside
        if (notificationDropdown && !notificationDropdown.contains(event.target)) {
            const notificationPanel = document.getElementById('notificationPanel');
            if (notificationPanel) {
                notificationPanel.classList.remove('show');
            }
        }
        
        // Close profile panel if clicking outside
        if (profileDropdown && !profileDropdown.contains(event.target)) {
            const profilePanel = document.getElementById('profilePanel');
            if (profilePanel) {
                profilePanel.classList.remove('show');
            }
        }
    });

    // Close dropdowns when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const notificationPanel = document.getElementById('notificationPanel');
            const profilePanel = document.getElementById('profilePanel');
            
            if (notificationPanel) {
                notificationPanel.classList.remove('show');
            }
            if (profilePanel) {
                profilePanel.classList.remove('show');
            }
        }
    });

    // Dark Mode Toggle
    function toggleDarkMode() {
        const body = document.body;
        const isDarkMode = body.classList.contains('dark-mode');
        const newDarkMode = !isDarkMode;
        
        if (newDarkMode) {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
            updateDarkModeIcons(true);
        } else {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
            updateDarkModeIcons(false);
        }
        
        // Save to database via AJAX if user is authenticated
        @auth
        fetch('{{ route('user-settings.update-ajax') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                dark_mode: newDarkMode
            })
        }).catch(err => console.error('Failed to save dark mode:', err));
        @endauth
    }

    function updateDarkModeIcons(isDarkMode) {
        const sidebarIcon = document.getElementById('dark-mode-icon');
        const topBarIcon = document.getElementById('top-dark-mode-icon');
        
        if (sidebarIcon) {
            sidebarIcon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
        }
        if (topBarIcon) {
            topBarIcon.className = isDarkMode ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    // Initialize dark mode from database or localStorage
    document.addEventListener('DOMContentLoaded', function() {
        @auth
        // Use database setting if available, fallback to localStorage
        const dbDarkMode = {{ isset($userSettings) && $userSettings->dark_mode ? 'true' : 'false' }};
        const localDarkMode = localStorage.getItem('darkMode') === 'true';
        const isDarkMode = dbDarkMode || localDarkMode;
        @else
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        @endauth
        
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            document.documentElement.classList.add('dark');
            updateDarkModeIcons(true);
        }
    });
</script>

@stack('scripts')

<script>
    // Initialize Material Design Components globally
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all Material Design buttons
        const buttons = document.querySelectorAll('.mdc-button');
        buttons.forEach(button => {
            mdc.ripple.MDCRipple.attachTo(button);
        });
        
        // Initialize all Material Design text fields
        const textFields = document.querySelectorAll('.mdc-text-field');
        textFields.forEach(field => {
            new mdc.textField.MDCTextField(field);
        });
        
        // Initialize all Material Design selects
        const selects = document.querySelectorAll('.mdc-select');
        selects.forEach(select => {
            new mdc.select.MDCSelect(select);
        });
        
        // Initialize all Material Design data tables
        const dataTables = document.querySelectorAll('.mdc-data-table');
        dataTables.forEach(table => {
            new mdc.dataTable.MDCDataTable(table);
        });
        
        // Initialize all Material Design cards
        const cards = document.querySelectorAll('.mdc-card');
        cards.forEach(card => {
            new mdc.card.MDCCard(card);
        });
    });

    // ============================================
    // COMPREHENSIVE UX IMPROVEMENTS
    // ============================================

    // Button Loading States - EXCLUDE MODAL FORMS
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading state to submit buttons on form submission
        // Exclude logout forms and MODAL FORMS to prevent interference
        document.querySelectorAll('form:not([action*="logout"]):not(.modal form)').forEach(form => {
            // Double check - skip if inside modal
            if (form.closest('.modal')) {
                return;
            }
            
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.classList.contains('btn-loading') && !submitBtn.closest('form[action*="logout"]')) {
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;
                }
            });
        });
    });

    // Toast Notification System
    window.showToast = function(message, type = 'success') {
        const container = document.querySelector('.toast-container') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas ${getToastIcon(type)} text-${type === 'success' ? 'green' : type === 'error' ? 'red' : type === 'warning' ? 'yellow' : 'blue'}-600"></i>
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'toast-slide-out 0.3s ease-out';
            toast.style.animationFillMode = 'forwards';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    function getToastIcon(type) {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    // Add CSS for toast slide out animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes toast-slide-out {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Enhanced Form Validation with Real-time Feedback
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input, textarea, select').forEach(input => {
            // Add validation classes on blur
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('border-red-500');
                    this.classList.remove('border-green-500');
                } else if (this.value.trim()) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                }
            });

            // Remove error on input
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                }
            });
        });
    });

    // Smooth Page Transitions
    window.addEventListener('DOMContentLoaded', function() {
        document.body.classList.add('page-transition');
    });

    // Enhanced Table Row Selection
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.data-table tbody tr').forEach(row => {
            // Skip rows in tables that should not have row click behavior
            if (row.closest('.no-row-click, table.no-row-click')) {
                return;
            }
            row.style.cursor = 'pointer';
            row.addEventListener('click', function(e) {
                // Don't trigger if clicking on a button/link or if event was stopped
                if (!e.target.closest('a, button') && !e.defaultPrevented) {
                    const link = row.querySelector('a');
                    if (link) {
                        link.click();
                    }
                }
            });
        });
    });

    // Auto-focus first input in forms
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form').forEach(form => {
            const firstInput = form.querySelector('input:not([type="hidden"]), textarea, select');
            if (firstInput && !firstInput.hasAttribute('autofocus')) {
                setTimeout(() => firstInput.focus(), 100);
            }
        });
    });

    // Debounce Function for Search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Apply debounce to search inputs
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="search"], input[name*="search"]').forEach(input => {
            let form = input.closest('form');
            if (form) {
                const debouncedSubmit = debounce(() => {
                    form.submit();
                }, 500);
                input.addEventListener('input', debouncedSubmit);
            }
        });
    });

    // Enhanced Dropdown Menus
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const dropdown = this.closest('.dropdown');
                document.querySelectorAll('.dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.remove('show');
                });
                dropdown.classList.toggle('show');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('show'));
        });
    });

    // Print Functionality
    window.printPage = function() {
        window.print();
    };

    // Export Functions
    window.exportToExcel = function(url) {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
        document.body.appendChild(loadingOverlay);
        
        window.location.href = url;
        
        setTimeout(() => loadingOverlay.remove(), 2000);
    };

    // Copy to Clipboard
    window.copyToClipboard = function(text, showToast = true) {
        navigator.clipboard.writeText(text).then(() => {
            if (showToast) {
                showToast('Copié dans le presse-papiers!', 'success');
            }
        }).catch(err => {
            console.error('Failed to copy:', err);
            if (showToast) {
                showToast('Échec de la copie', 'error');
            }
        });
    };

    // Enhanced Mobile Experience
    if (window.innerWidth <= 991.98) {
        // Prevent zoom on double tap for buttons
        document.addEventListener('touchend', function(e) {
            const button = e.target.closest('button, .btn, a[role="button"]');
            if (button) {
                button.click();
            }
        }, { passive: true });
    }

    // Image Lazy Loading Enhancement
    if ('loading' in HTMLImageElement.prototype) {
        document.querySelectorAll('img[loading="lazy"]').forEach(img => {
            img.src = img.dataset.src || img.src;
        });
    } else {
        // Fallback for browsers that don't support lazy loading
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/lazysizes@5.3.2/lazysizes.min.js';
        document.body.appendChild(script);
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K for search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[type="search"], input[name*="search"]');
            if (searchInput) {
                searchInput.focus();
            }
        }

        // Escape to close modals - Use Bootstrap's native handler instead
        // Bootstrap already handles Escape key, so we don't need custom handler
    });

    // Preload Hints for Better Performance
    if ('requestIdleCallback' in window) {
        requestIdleCallback(() => {
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.setAttribute('rel', 'prefetch');
                }, { once: true });
            });
        });
    }

    // Performance Monitoring
    if ('PerformanceObserver' in window) {
        const perfObserver = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                console.log(entry.name, entry.duration);
            }
        });
        
        try {
            perfObserver.observe({ entryTypes: ['measure', 'mark'] });
        } catch (e) {
            // Not all browsers support this
        }
    }

    // Analytics Event Tracking
    window.trackEvent = function(category, action, label) {
        if (window.gtag) {
            gtag('event', action, {
                event_category: category,
                event_label: label
            });
        }
    };
</script>
