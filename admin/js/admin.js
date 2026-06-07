/* ============================================================
   HACK KUET — Admin Panel JavaScript
   ============================================================ */
(function () {
    'use strict';

    // ─── Theme Toggle ──────────────────────────────────────────
    function initTheme() {
        const btn = document.getElementById('themeToggle');
        const sunIcon  = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');

        function applyTheme(dark) {
            document.documentElement.classList.toggle('dark', dark);
            if (sunIcon && moonIcon) {
                sunIcon.style.display  = dark ? 'none' : '';
                moonIcon.style.display = dark ? ''     : 'none';
            }
        }

        const saved = localStorage.getItem('hack-theme');
        applyTheme(saved === 'dark');

        if (btn) {
            btn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('hack-theme', isDark ? 'dark' : 'light');
                applyTheme(isDark);
            });
        }
    }

    // ─── Sidebar Toggle ────────────────────────────────────────
    function initSidebar() {
        const sidebar  = document.getElementById('adminSidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const openBtn  = document.getElementById('menuToggle');
        const closeBtn = document.getElementById('sidebarClose');

        function openSidebar() {
            sidebar?.classList.add('open');
            overlay?.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar?.classList.remove('open');
            overlay?.classList.remove('active');
            document.body.style.overflow = '';
        }

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSidebar();
        });
    }

    // ─── Modal Helpers ─────────────────────────────────────────
    window.openModal = function (id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function (id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // Close modal on overlay click
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    // Close modal on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay[style*="flex"]').forEach(m => {
                m.style.display = 'none';
                document.body.style.overflow = '';
            });
        }
    });

    // ─── Toast Notifications ───────────────────────────────────
    window.toast = function (message, type = 'info', duration = 3500) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const t = document.createElement('div');
        t.className = `toast toast-${type}`;
        t.textContent = message;
        container.appendChild(t);

        setTimeout(() => {
            t.style.animation = 'toastOut 300ms ease forwards';
            setTimeout(() => t.remove(), 300);
        }, duration);
    };

    // ─── Table Search / Filter ─────────────────────────────────
    window.filterTable = function (tableId, query) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        if (!tbody) return;
        const term = query.toLowerCase().trim();
        tbody.querySelectorAll('tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = (!term || text.includes(term)) ? '' : 'none';
        });
    };

    // ─── Image preview helper (generic) ────────────────────────
    window.previewImage = function (inputId, previewId) {
        const input   = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;
        input.addEventListener('change', function () {
            if (this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(this.files[0]);
            }
        });
    };

    // ─── Active sidebar link highlight ─────────────────────────
    function highlightActiveNav() {
        const path = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            const href = link.getAttribute('href') || '';
            const active = href && path.endsWith(href.replace(/^\/admin/, '').split('?')[0]);
            link.classList.toggle('active', active);
        });
    }

    // ─── Auto-dismiss alerts ───────────────────────────────────
    function autoDismissAlerts() {
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 400ms';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 400);
            }, 5000);
        });
    }

    // ─── Init ──────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        initTheme();
        initSidebar();
        highlightActiveNav();
        autoDismissAlerts();
    });

})();
