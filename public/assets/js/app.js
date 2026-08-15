/**
 * Portail Numérique du Ministère des Transports
 * JavaScript principal
 */

// CSRF token for AJAX requests (see <meta name="csrf-token"> in layouts)
const CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
        });
    }

    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.admin-sidebar');
        const menuBtn = document.getElementById('mobile-menu-btn');
        
        if (sidebar && menuBtn && window.innerWidth < 768) {
            if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
            }
        }
    });

    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.3s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Format numbers on page load
    formatNumbers();
});

function formatNumbers() {
    const numberElements = document.querySelectorAll('[data-number]');
    numberElements.forEach(function(el) {
        const value = parseFloat(el.getAttribute('data-number'));
        if (!isNaN(value)) {
            el.textContent = new Intl.NumberFormat('fr-FR').format(value);
        }
    });
}

// Export for use in inline scripts
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
};

window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
};

// Confirm delete
window.confirmDelete = function(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ?');
};

// Search functionality
window.performSearch = function(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toLowerCase();
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(function(row) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
};
