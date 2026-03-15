// admin/assets/js/admin.js

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin JS loaded');

    // Initialize sidebar state
    initSidebar();
});

// Sidebar toggle function
function toggleSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');

        // Save state to localStorage
        const isActive = sidebar.classList.contains('active');
        localStorage.setItem('sidebarState', isActive ? 'active' : 'collapsed');
    }
}

// Initialize sidebar based on saved state
function initSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    if (!sidebar) return;

    // Check for saved state
    const savedState = localStorage.getItem('sidebarState');

    // Handle mobile vs desktop
    if (window.innerWidth <= 768) {
        // On mobile, start collapsed
        sidebar.classList.remove('active');
    } else {
        // On desktop, use saved state or default to collapsed
        if (savedState === 'active') {
            sidebar.classList.add('active');
        } else {
            sidebar.classList.remove('active');
        }
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    initSidebar();
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(e) {
    const sidebar = document.querySelector('.admin-sidebar');
    const toggle = document.querySelector('.sidebar-toggle');

    if (window.innerWidth <= 768) {
        if (sidebar && toggle) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    }
});

// Prevent multiple form submissions
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
    });
});

// Confirmation dialog for delete actions
function confirmDelete(message = 'Are you sure you want to delete this item? This action cannot be undone.') {
    return confirm(message);
}

// Format numbers as currency
function formatCurrency(amount) {
    return 'KES ' + new Intl.NumberFormat().format(amount);
}

// Show notification
function showNotification(message, type = 'info', duration = 3000) {
    // Remove existing notification
    const existing = document.querySelector('.admin-notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `admin-notification admin-notification-${type}`;

    const icon = type === 'success' ? 'check-circle' :
        type === 'error' ? 'exclamation-circle' :
        type === 'warning' ? 'exclamation-triangle' : 'info-circle';

    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;

    // Style
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px 20px';
    notification.style.background = type === 'success' ? '#1cc88a' :
        type === 'error' ? '#e74a3b' :
        type === 'warning' ? '#f6c23e' : '#4e73df';
    notification.style.color = type === 'warning' ? '#333' : 'white';
    notification.style.borderRadius = '8px';
    notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    notification.style.zIndex = '9999';
    notification.style.display = 'flex';
    notification.style.alignItems = 'center';
    notification.style.gap = '10px';
    notification.style.animation = 'slideIn 0.3s ease';
    notification.style.fontWeight = '500';

    document.body.appendChild(notification);

    // Remove after duration
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, duration);
}

// Add animation styles if not already present
if (!document.getElementById('admin-notification-styles')) {
    const style = document.createElement('style');
    style.id = 'admin-notification-styles';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .admin-sidebar {
            transition: width 0.3s, transform 0.3s;
        }
        
        .admin-sidebar.active {
            width: 250px !important;
            transform: translateX(0) !important;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                position: fixed !important;
                z-index: 1000;
            }
            .admin-sidebar.active {
                transform: translateX(0);
            }
            .admin-main {
                margin-left: 0 !important;
            }
        }
    `;
    document.head.appendChild(style);
}

// Handle AJAX requests
async function fetchData(url, options = {}) {
    try {
        const response = await fetch(url, options);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        showNotification('Network error: ' + error.message, 'error');
        throw error;
    }
}

// Export functions for global use
window.toggleSidebar = toggleSidebar;
window.confirmDelete = confirmDelete;
window.formatCurrency = formatCurrency;
window.showNotification = showNotification;