/**
 * StockManager — app.js
 * Interactions JavaScript globales (Vanilla JS, aucune dépendance)
 */

'use strict';

/* ── Auto-dismiss flash toast ──────────────────────────── */
(function () {
    const toast = document.getElementById('flashMsg');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity .4s';
            toast.style.opacity    = '0';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }
})();

/* ── Confirm delete (data-confirm attribute) ───────────── */
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-confirm]');
    if (btn) {
        const msg = btn.dataset.confirm || 'Confirmer cette action ?';
        if (!confirm(msg)) e.preventDefault();
    }
});

/* ── Sidebar overlay close on mobile ──────────────────── */
document.addEventListener('click', function (e) {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('sidebarToggle');
    if (!sidebar || !toggle) return;
    if (sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) &&
        !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

/* ── Table row clickable (data-href) ───────────────────── */
document.querySelectorAll('tr[data-href]').forEach(function (row) {
    row.style.cursor = 'pointer';
    row.addEventListener('click', function (e) {
        if (!e.target.closest('a, button, form')) {
            window.location.href = row.dataset.href;
        }
    });
});

/* ── Form: prevent double submission ───────────────────── */
document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function () {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '.6';
        }
    });
});

/* ── Live search debounce helper ───────────────────────── */
function debounce(fn, delay) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/* ── Auto-submit filter forms on select change ─────────── */
document.querySelectorAll('.filters-bar select').forEach(function (sel) {
    sel.addEventListener('change', function () {
        this.closest('form')?.submit();
    });
});

/* ── Number formatting in stat cards ──────────────────── */
document.querySelectorAll('.stat-value[data-format="number"]').forEach(function (el) {
    const n = parseInt(el.textContent.replace(/\s/g, ''), 10);
    if (!isNaN(n)) el.textContent = n.toLocaleString('fr-FR');
});

/* ── Keyboard shortcut: Escape closes sidebar ──────────── */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('sidebar')?.classList.remove('open');
    }
});
