/* app.js – Shared utilities for Sharon Store */

// ======================== AUTH & ROLES ========================

// Role hierarchy: Staff < Manager < Admin
const ROLE_LEVELS = { Staff: 1, Manager: 2, Admin: 3 };

// Nav IDs that Staff cannot access
const STAFF_BLOCKED_NAV = ['nav-inventory', 'nav-forecasting'];

function getSession() {
    const s = localStorage.getItem('sharonstore_session');
    return s ? JSON.parse(s) : null;
}

function getRole() {
    const s = getSession();
    return s ? (s.role || 'Staff') : null;
}

function hasRole(allowedRoles) {
    return allowedRoles.includes(getRole());
}

/**
 * Call at the top of any protected page.
 * Redirects to dashboard if the current user's role is not allowed.
 */
function requireRole(allowedRoles) {
    if (!hasRole(allowedRoles)) {
        sessionStorage.setItem('sharonstore_access_denied', '1');
        window.location.href = 'dashboard.php';
    }
}

function checkAuth() {
    const session = localStorage.getItem('sharonstore_session');
    if (!session || !JSON.parse(session).loggedIn) {
        window.location.href = 'login.php';
        return null;
    }
    const s = JSON.parse(session);
    const role = s.role || 'Staff';

    // Populate sidebar user info
    const nameEl   = document.getElementById('sidebarName');
    const roleEl   = document.getElementById('sidebarRole');
    const avatarEl = document.getElementById('sidebarAvatar');
    if (nameEl)   nameEl.textContent   = s.fullName || s.username;
    if (roleEl) {
        roleEl.textContent = role;
        const colours = { Admin: '#e91e8c', Manager: '#a855f7', Staff: '#7c6d94' };
        roleEl.style.color = colours[role] || colours.Staff;
    }
    if (avatarEl) avatarEl.textContent = (s.fullName || s.username || 'A')[0].toUpperCase();

    // Lock sidebar nav items restricted for Staff
    if (role === 'Staff') {
        STAFF_BLOCKED_NAV.forEach(navId => {
            const el = document.getElementById(navId);
            if (!el) return;
            el.style.opacity       = '0.4';
            el.style.cursor        = 'not-allowed';
            el.style.pointerEvents = 'none';
            el.setAttribute('title', 'Admin / Manager only');
            // Append lock icon
            const lock = document.createElement('i');
            lock.className = 'fa fa-lock';
            lock.style.cssText = 'font-size:10px; margin-left:auto; color:#7c6d94;';
            el.appendChild(lock);
        });
    }

    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('sharonstore_session');
            window.location.href = 'login.php';
        });
    }

    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar    = document.getElementById('sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    return s;
}

function initNav(activeId) {
    const el = document.getElementById(activeId);
    if (el) {
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        el.classList.add('active');
    }
}

// Show access-denied toast when redirected from a protected page
document.addEventListener('DOMContentLoaded', () => {
    if (sessionStorage.getItem('sharonstore_access_denied')) {
        sessionStorage.removeItem('sharonstore_access_denied');
        showToast('Access denied. Admin or Manager role required.', 'error');
    }
});

// ======================== INVENTORY ========================
const DEFAULT_INVENTORY = [
    { id: 1,  name: 'Century Tuna',       category: 'Canned Goods',        price: 35.00, stock: 150, unit: 'Pieces',    reorderLevel: 20 },
    { id: 2,  name: 'Corned Beef',        category: 'Canned Goods',        price: 45.00, stock: 100, unit: 'Pieces',    reorderLevel: 25 },
    { id: 3,  name: 'Sardines in Tomato', category: 'Canned Goods',        price: 22.00, stock: 10,  unit: 'Pieces',    reorderLevel: 30 },
    { id: 4,  name: 'Jasmine Rice 1kg',   category: 'Dry Commodities',     price: 55.00, stock: 80,  unit: 'Kilograms', reorderLevel: 20 },
    { id: 5,  name: 'Brown Sugar 1kg',    category: 'Dry Commodities',     price: 60.00, stock: 15,  unit: 'Kilograms', reorderLevel: 25 },
    { id: 6,  name: 'All-Purpose Flour',  category: 'Dry Commodities',     price: 48.00, stock: 0,   unit: 'Kilograms', reorderLevel: 15 },
    { id: 7,  name: 'Tide Powder',        category: 'Household Essentials',price: 15.00, stock: 200, unit: 'Packs',     reorderLevel: 50 },
    { id: 8,  name: 'Dishwashing Liquid', category: 'Household Essentials',price: 25.00, stock: 90,  unit: 'Pieces',    reorderLevel: 20 },
    { id: 9,  name: 'Cooking Oil 1L',     category: 'Dry Commodities',     price: 85.00, stock: 8,   unit: 'Bottles',   reorderLevel: 15 },
    { id: 10, name: 'Soy Sauce 250ml',    category: 'Condiments',          price: 18.00, stock: 55,  unit: 'Bottles',   reorderLevel: 15 },
    { id: 11, name: 'Vinegar 250ml',      category: 'Condiments',          price: 15.00, stock: 40,  unit: 'Bottles',   reorderLevel: 15 },
    { id: 12, name: 'Instant Noodles',    category: 'Dry Commodities',     price: 12.00, stock: 300, unit: 'Packs',     reorderLevel: 50 },
];

function getInventory() {
    const stored = localStorage.getItem('sharonstore_inventory');
    if (!stored) {
        localStorage.setItem('sharonstore_inventory', JSON.stringify(DEFAULT_INVENTORY));
        return DEFAULT_INVENTORY;
    }
    return JSON.parse(stored);
}

function saveInventory(inv) {
    localStorage.setItem('sharonstore_inventory', JSON.stringify(inv));
}

// ======================== TRANSACTIONS ========================
const DEFAULT_TRANSACTIONS = [];
const today = new Date();
for (let i = 29; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(d.getDate() - i);
    const count = Math.floor(Math.random() * 8) + 1;
    for (let j = 0; j < count; j++) {
        const total = Math.round((Math.random() * 400 + 50) * 100) / 100;
        DEFAULT_TRANSACTIONS.push({
            id: DEFAULT_TRANSACTIONS.length + 1,
            date: d.toISOString(),
            total,
            items: [{ name: 'Mixed Items', qty: Math.floor(Math.random() * 5) + 1, price: total }]
        });
    }
}

function getTransactions() {
    const stored = localStorage.getItem('sharonstore_transactions');
    if (!stored) {
        localStorage.setItem('sharonstore_transactions', JSON.stringify(DEFAULT_TRANSACTIONS));
        return DEFAULT_TRANSACTIONS;
    }
    const parsed = JSON.parse(stored);
    if (parsed.length < 10) {
        const merged = [...DEFAULT_TRANSACTIONS, ...parsed.filter(t => t.id > DEFAULT_TRANSACTIONS.length)];
        localStorage.setItem('sharonstore_transactions', JSON.stringify(merged));
        return merged;
    }
    return parsed;
}

function saveTransactions(txs) {
    localStorage.setItem('sharonstore_transactions', JSON.stringify(txs));
}

function addTransaction(tx) {
    const txs = getTransactions();
    tx.id   = txs.length + 1;
    tx.date = new Date().toISOString();
    txs.push(tx);
    saveTransactions(txs);
    return tx;
}

// ======================== TOAST ========================
function showToast(msg, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const icons = { success: '✅', error: '❌', info: 'ℹ️', warn: '⚠️' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span><span>${msg}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ======================== MODAL HELPERS ========================
function openModal(id)  { document.getElementById(id).classList.add('open');    }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
