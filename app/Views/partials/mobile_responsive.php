<?php
/** Mobile-only overrides — desktop layout unchanged at 901px and above. */
$mobileLayout = $mobileLayout ?? 'sidebar';
?>
<style>
.mobile-topbar,
.sidebar-backdrop {
    display: none;
}

@media (max-width: 900px) {
    .mobile-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 56px;
        padding: 0 12px;
        background: linear-gradient(135deg, rgba(82,0,24,.96), rgba(138,0,40,.92));
        color: #fff;
        z-index: 300;
        box-shadow: 0 4px 20px rgba(82,0,24,.25);
        backdrop-filter: blur(12px);
    }
    .mobile-menu-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: rgba(255,255,255,.15);
        border-radius: 12px;
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        -webkit-tap-highlight-color: transparent;
    }
    .mobile-topbar-title {
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.05em;
    }

    body.app-shell {
        display: block !important;
        padding-top: 56px;
        overflow-x: hidden;
    }
    body.app-shell .app-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: min(280px, 88vw);
        height: 100vh;
        height: 100dvh;
        transform: translateX(-105%);
        transition: transform 0.28s ease;
        z-index: 250;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    body.app-shell.sidebar-open .app-sidebar {
        transform: translateX(0);
    }
    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 10, 12, 0.55);
        z-index: 200;
        backdrop-filter: blur(2px);
    }
    body.app-shell.sidebar-open .sidebar-backdrop {
        display: block;
    }

    body.app-shell .app-main {
        margin-left: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 1rem !important;
        min-height: calc(100dvh - 56px);
        box-sizing: border-box;
    }

    .glass-card.p-8,
    .glass-card.p-10 {
        padding: 1.25rem !important;
    }
    .glass-card.flex.justify-between,
    .glass-card > .flex.justify-between.items-center,
    .glass-card.p-8 > .flex.justify-between.items-center,
    .app-main .glass-card .flex.justify-between.items-center {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.75rem;
    }

    .grid.grid-cols-2.gap-3,
    .grid.grid-cols-2.gap-4 {
        grid-template-columns: 1fr !important;
    }
    #programTypeToggle {
        flex-direction: column;
    }
    #programTypeToggle button {
        width: 100%;
        justify-content: center;
    }

    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
  table {
        min-width: 560px;
    }

    .event-poster {
        height: 160px;
    }

    .modal-overlay .glass-card,
    .modal-overlay.open .glass-card {
        max-width: calc(100vw - 2rem) !important;
        width: calc(100vw - 2rem) !important;
        margin: 1rem !important;
        padding: 1.25rem !important;
    }

    .eventraz-watermark {
        bottom: 0.5rem !important;
        right: 0.5rem !important;
        font-size: 9px !important;
    }

    body.login-page {
        padding: 1rem;
        align-items: flex-start;
        padding-top: 1.5rem;
    }
    body.login-page .card {
        padding: 1.5rem 1.25rem 1.25rem;
        border-radius: 22px;
    }
    body.login-page .role-tabs {
        gap: 16px;
    }

    body.mobile-default nav.fixed {
        padding: 0.75rem 1rem !important;
    }
    body.mobile-default .px-8 {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    body.mobile-default section.pt-32 {
        padding-top: 5.5rem !important;
        padding-bottom: 3rem !important;
    }
    body.mobile-default .text-4xl {
        font-size: 1.75rem !important;
        line-height: 1.2 !important;
    }
    body.mobile-default .mobile-hide-label {
        display: none;
    }

    #subProgramRow.visible {
        max-height: 220px !important;
    }

    .swal2-popup {
        width: calc(100vw - 2rem) !important;
        max-width: 420px !important;
        margin: 0.5rem !important;
    }
}

@media (min-width: 901px) {
    .mobile-topbar,
    .sidebar-backdrop {
        display: none !important;
    }
}
</style>

<?php if ($mobileLayout === 'sidebar'): ?>
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()" aria-hidden="true"></div>
<header class="mobile-topbar" aria-label="Navigasi utama">
    <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" onclick="toggleMobileSidebar()" aria-label="Buka menu" aria-expanded="false">
        <i class="fa-solid fa-bars"></i>
    </button>
    <span class="mobile-topbar-title">EventraZ</span>
    <span style="width:40px" aria-hidden="true"></span>
</header>
<script>
(function () {
    function toggleMobileSidebar() {
        var open = document.body.classList.toggle('sidebar-open');
        var btn = document.getElementById('mobileMenuBtn');
        if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    function closeMobileSidebar() {
        document.body.classList.remove('sidebar-open');
        var btn = document.getElementById('mobileMenuBtn');
        if (btn) btn.setAttribute('aria-expanded', 'false');
    }
    window.toggleMobileSidebar = toggleMobileSidebar;
    window.closeMobileSidebar = closeMobileSidebar;

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMobileSidebar();
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) closeMobileSidebar();
    });

    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.querySelector('.app-sidebar');
        if (!sidebar) return;
        sidebar.querySelectorAll('button, a').forEach(function (el) {
            el.addEventListener('click', function () {
                if (window.innerWidth <= 900) setTimeout(closeMobileSidebar, 150);
            });
        });
    });
})();
</script>
<?php endif; ?>
