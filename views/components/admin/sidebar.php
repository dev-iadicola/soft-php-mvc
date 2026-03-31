<?php $page = request()->uri(); ?>

<!-- Brand -->
<a href="/admin/dashboard" class="sidebar-brand">
    <span class="sidebar-brand__icon"><i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i></span>
    <span class="sidebar-brand__text">Soft MVC</span>
</a>

<!-- Navigation -->
<nav>
    <ul class="sidebar-nav">
        <!-- Main -->
        <li class="sidebar-section-title">Principale</li>

        <li class="sidebar-nav-item">
            <a href="/admin/dashboard" class="sidebar-nav-link<?= $page === '/admin/dashboard' ? ' active' : '' ?>">
                <i data-lucide="home" style="width:18px;height:18px;"></i>
                <span class="sidebar-nav-link__text">Dashboard</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="/admin/visitors" class="sidebar-nav-link<?= $page === '/admin/visitors' ? ' active' : '' ?>">
                <i data-lucide="bar-chart-3" style="width:18px;height:18px;"></i>
                <span class="sidebar-nav-link__text">Visitatori</span>
            </a>
        </li>

        <!-- Portfolio -->
        <li class="sidebar-section-title">Gestione Contenuti</li>

        <li class="sidebar-nav-item">
            <button class="sidebar-submenu-toggle<?= _sidebarGroupActive($page, ['/admin/home', '/admin/contatti', '/admin/contact-hero', '/admin/project', '/admin/corsi']) ? ' active' : '' ?>"
                    data-target="submenu-portfolio"
                    aria-expanded="<?= _sidebarGroupActive($page, ['/admin/home', '/admin/contatti', '/admin/contact-hero', '/admin/project', '/admin/corsi']) ? 'true' : 'false' ?>">
                <i data-lucide="briefcase" style="width:18px;height:18px;"></i>
                <span>Portfolio</span>
                <span class="sidebar-submenu-chevron"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i></span>
            </button>
            <ul id="submenu-portfolio" class="sidebar-submenu<?= _sidebarGroupActive($page, ['/admin/home', '/admin/contatti', '/admin/contact-hero', '/admin/project', '/admin/corsi']) ? ' open' : '' ?>">
                <li><a href="/admin/home" class="sidebar-submenu-link<?= $page === '/admin/home' ? ' active' : '' ?>">Home Page</a></li>
                <li><a href="/admin/contatti" class="sidebar-submenu-link<?= $page === '/admin/contatti' || str_starts_with($page, '/admin/contatti/') ? ' active' : '' ?>">Messaggi</a></li>
                <li><a href="/admin/contact-hero" class="sidebar-submenu-link<?= $page === '/admin/contact-hero' || str_starts_with($page, '/admin/contact-hero-') ? ' active' : '' ?>">Hero Contatti</a></li>
                <li><a href="/admin/project" class="sidebar-submenu-link<?= $page === '/admin/project' || str_starts_with($page, '/admin/project-') ? ' active' : '' ?>">Progetti</a></li>
                <li><a href="/admin/corsi" class="sidebar-submenu-link<?= $page === '/admin/corsi' || str_starts_with($page, '/admin/corso-') ? ' active' : '' ?>">Corsi</a></li>
            </ul>
        </li>

        <li class="sidebar-nav-item">
            <button class="sidebar-submenu-toggle<?= _sidebarGroupActive($page, ['/admin/technology', '/admin/partner', '/admin/footer-links', '/admin/email-templates']) ? ' active' : '' ?>"
                    data-target="submenu-config"
                    aria-expanded="<?= _sidebarGroupActive($page, ['/admin/technology', '/admin/partner', '/admin/footer-links', '/admin/email-templates']) ? 'true' : 'false' ?>">
                <i data-lucide="settings-2" style="width:18px;height:18px;"></i>
                <span>Configurazione</span>
                <span class="sidebar-submenu-chevron"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i></span>
            </button>
            <ul id="submenu-config" class="sidebar-submenu<?= _sidebarGroupActive($page, ['/admin/technology', '/admin/partner', '/admin/footer-links', '/admin/email-templates']) ? ' open' : '' ?>">
                <li><a href="/admin/technology" class="sidebar-submenu-link<?= $page === '/admin/technology' || str_starts_with($page, '/admin/technology-') ? ' active' : '' ?>">Stack Tecnologico</a></li>
                <li><a href="/admin/partner" class="sidebar-submenu-link<?= $page === '/admin/partner' || str_starts_with($page, '/admin/partner-') ? ' active' : '' ?>">Partner</a></li>
                <li><a href="/admin/footer-links" class="sidebar-submenu-link<?= $page === '/admin/footer-links' || str_starts_with($page, '/admin/footer-links-') ? ' active' : '' ?>">Footer Links</a></li>
                <li><a href="/admin/email-templates" class="sidebar-submenu-link<?= $page === '/admin/email-templates' || str_starts_with($page, '/admin/email-templates') ? ' active' : '' ?>">Template Email</a></li>
            </ul>
        </li>

        <li class="sidebar-nav-item">
            <a href="/admin/laws" class="sidebar-nav-link<?= $page === '/admin/laws' ? ' active' : '' ?>">
                <i data-lucide="scale" style="width:18px;height:18px;"></i>
                <span class="sidebar-nav-link__text">Policy & Laws</span>
            </a>
        </li>

        <!-- System -->
        <li class="sidebar-section-title">Sistema</li>

        <li class="sidebar-nav-item">
            <button class="sidebar-submenu-toggle<?= _sidebarGroupActive($page, ['/admin/settings', '/admin/security', '/admin/sessions', '/admin/password', '/admin/edit-profile', '/admin/logs']) ? ' active' : '' ?>"
                    data-target="submenu-user"
                    aria-expanded="<?= _sidebarGroupActive($page, ['/admin/settings', '/admin/security', '/admin/sessions', '/admin/password', '/admin/edit-profile', '/admin/logs']) ? 'true' : 'false' ?>">
                <i data-lucide="user-cog" style="width:18px;height:18px;"></i>
                <span>Account</span>
                <span class="sidebar-submenu-chevron"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i></span>
            </button>
            <ul id="submenu-user" class="sidebar-submenu<?= _sidebarGroupActive($page, ['/admin/settings', '/admin/security', '/admin/sessions', '/admin/password', '/admin/edit-profile', '/admin/logs']) ? ' open' : '' ?>">
                <li><a href="/admin/settings" class="sidebar-submenu-link<?= $page === '/admin/settings' ? ' active' : '' ?>">Impostazioni</a></li>
                <li><a href="/admin/security" class="sidebar-submenu-link<?= $page === '/admin/security' ? ' active' : '' ?>">Sicurezza 2FA</a></li>
                <li><a href="/admin/sessions" class="sidebar-submenu-link<?= $page === '/admin/sessions' ? ' active' : '' ?>">Sessioni Attive</a></li>
                <li><a href="/admin/password" class="sidebar-submenu-link<?= $page === '/admin/password' ? ' active' : '' ?>">Cambia Password</a></li>
                <li><a href="/admin/edit-profile" class="sidebar-submenu-link<?= $page === '/admin/edit-profile' ? ' active' : '' ?>">Modifica Profilo</a></li>
                <li><a href="/admin/logs" class="sidebar-submenu-link<?= $page === '/admin/logs' ? ' active' : '' ?>">Logs</a></li>
            </ul>
        </li>

        <li class="sidebar-nav-item">
            <a href="/admin/terminal" class="sidebar-nav-link<?= $page === '/admin/terminal' ? ' active' : '' ?>">
                <i data-lucide="terminal" style="width:18px;height:18px;"></i>
                <span class="sidebar-nav-link__text">Terminal</span>
            </a>
        </li>
    </ul>
</nav>

<!-- Footer: Logout -->
<div class="sidebar-footer">
    <a href="/" target="_blank" class="sidebar-nav-link" style="margin-bottom:4px;">
        <i data-lucide="external-link" style="width:18px;height:18px;"></i>
        <span class="sidebar-nav-link__text">Visita il sito</span>
    </a>
    <form action="/logout" method="POST" style="margin:0;">
        @csrf
        <button type="submit" class="sidebar-logout-btn">
            <i data-lucide="log-out" style="width:18px;height:18px;"></i>
            <span>Logout</span>
        </button>
    </form>
</div>

<script>
// Sidebar submenu toggle
document.querySelectorAll('.sidebar-submenu-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var targetId = this.getAttribute('data-target');
        var submenu = document.getElementById(targetId);
        var isOpen = submenu.classList.contains('open');

        if (isOpen) {
            submenu.classList.remove('open');
            this.setAttribute('aria-expanded', 'false');
        } else {
            submenu.classList.add('open');
            this.setAttribute('aria-expanded', 'true');
        }
    });
});
</script>

<?php
// Helper function for sidebar group active state
function _sidebarGroupActive(string $currentPage, array $prefixes): bool {
    foreach ($prefixes as $prefix) {
        if ($currentPage === $prefix || str_starts_with($currentPage, $prefix . '-') || str_starts_with($currentPage, $prefix . '/')) {
            return true;
        }
    }
    return false;
}
?>
