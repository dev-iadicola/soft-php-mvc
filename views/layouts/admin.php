<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <title>Soft MVC — Admin</title>
    <link rel="icon" type="image/x-icon" href="<?= assets('img/favicon.png') ?>">

    <!-- Bootstrap CSS -->
    <link href="<?= assets("vendor/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet">
    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <!-- Quill -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <!-- Devicon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/devicon.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?= assets('admin.css') ?>?v=<?= time() ?>">
</head>

<body>
    <?php
    try {
        $currentUser = \App\Services\AuthService::user();
        $userName = $currentUser->email ?? 'Admin';
        $userInitial = strtoupper(substr($userName, 0, 1));
    } catch (\Throwable) {
        $userName = 'Admin';
        $userInitial = 'A';
    }
    $currentPage = request()->uri();
    $pageTitle = 'Dashboard';
    $pageTitles = [
        '/admin/dashboard' => 'Dashboard',
        '/admin/home' => 'Gestione Home',
        '/admin/contatti' => 'Messaggi',
        '/admin/project' => 'Progetti',
        '/admin/corsi' => 'Corsi',
        '/admin/technology' => 'Stack Tecnologico',
        '/admin/partner' => 'Partner',
        '/admin/footer-links' => 'Footer Links',
        '/admin/email-templates' => 'Template Email',
        '/admin/contact-hero' => 'Hero Contatti',
        '/admin/visitors' => 'Visitatori',
        '/admin/settings' => 'Impostazioni',
        '/admin/security' => 'Sicurezza 2FA',
        '/admin/sessions' => 'Sessioni Attive',
        '/admin/password' => 'Cambia Password',
        '/admin/edit-profile' => 'Modifica Profilo',
        '/admin/logs' => 'Logs',
        '/admin/laws' => 'Policy & Laws',
        '/admin/terminal' => 'Terminal',
    ];
    foreach ($pageTitles as $path => $title) {
        if ($currentPage === $path || str_starts_with($currentPage, $path . '-') || str_starts_with($currentPage, $path . '/')) {
            $pageTitle = $title;
            break;
        }
    }
    ?>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside id="admin-sidebar" class="admin-sidebar">
            @include('components.admin.sidebar')
        </aside>

        <!-- Backdrop (mobile) -->
        <div id="sidebar-backdrop" class="sidebar-backdrop"></div>

        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button id="topbar-toggle" class="topbar-toggle" aria-label="Toggle sidebar">
                    <i data-lucide="menu" style="width:20px;height:20px;"></i>
                </button>
                <nav class="topbar-breadcrumb">
                    <a href="/admin/dashboard">Admin</a>
                    <span class="topbar-breadcrumb__separator"><i data-lucide="chevron-right" style="width:12px;height:12px;"></i></span>
                    <span class="topbar-breadcrumb__current"><?= htmlspecialchars($pageTitle) ?></span>
                </nav>
            </div>
            <div class="topbar-right">
                <!-- Notifications -->
                @include('components.admin.notification-bell')

                <!-- User dropdown -->
                <div class="topbar-user">
                    <button id="user-dropdown-toggle" class="topbar-user-btn">
                        <span class="topbar-user-avatar"><?= $userInitial ?></span>
                        <span><?= htmlspecialchars(explode('@', $userName)[0]) ?></span>
                        <i data-lucide="chevron-down" style="width:14px;height:14px;opacity:0.5;"></i>
                    </button>
                    <div id="user-dropdown" class="topbar-user-dropdown">
                        <a href="/admin/edit-profile"><i data-lucide="user" style="width:15px;height:15px;"></i> Profilo</a>
                        <a href="/admin/settings"><i data-lucide="settings" style="width:15px;height:15px;"></i> Impostazioni</a>
                        <a href="/admin/security"><i data-lucide="shield" style="width:15px;height:15px;"></i> Sicurezza</a>
                        <div class="topbar-user-dropdown__divider"></div>
                        <form action="/logout" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="text-danger"><i data-lucide="log-out" style="width:15px;height:15px;"></i> Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-content">
                @include('session.messages')
                <<page>>
            </div>

            <!-- Admin Footer -->
            <footer class="admin-footer">
                &copy; <?= date('Y') ?> Soft MVC &mdash;
                <a href="/" target="_blank">Visita il sito</a>
            </footer>
        </div>
    </div>

    <!-- JS: jQuery, Popper, Bootstrap -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>

    <!-- Quill, Sortable, CKEditor -->
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script src="<?= assets('vendor/ckeditor/js/execute.js')?>"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Init Lucide icons
        lucide.createIcons();

        // === Sidebar toggle (mobile) ===
        (function() {
            var sidebar = document.getElementById('admin-sidebar');
            var backdrop = document.getElementById('sidebar-backdrop');
            var toggleBtn = document.getElementById('topbar-toggle');

            function openSidebar() {
                sidebar.classList.add('mobile-open');
                backdrop.classList.add('visible');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('mobile-open');
                backdrop.classList.remove('visible');
                document.body.style.overflow = '';
            }

            toggleBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('mobile-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            backdrop.addEventListener('click', closeSidebar);
        })();

        // === User dropdown ===
        (function() {
            var btn = document.getElementById('user-dropdown-toggle');
            var dropdown = document.getElementById('user-dropdown');

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('open');
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });
        })();

        // === Toast system ===
        window.adminToast = function(message, type) {
            type = type || 'success';
            var container = document.getElementById('admin-toast');
            if (!container) {
                container = document.createElement('div');
                container.id = 'admin-toast';
                container.className = 'admin-toast';
                document.body.appendChild(container);
            }
            var toast = document.createElement('div');
            toast.className = 'admin-toast__item admin-toast__item--' + type;
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(function() {
                toast.classList.add('fade-out');
                setTimeout(function() { toast.remove(); }, 300);
            }, 3000);
        };

        // === Sortable ===
        function initSortable(elementId) {
            var el = document.getElementById(elementId);
            if (!el) return;

            var entity = el.getAttribute('data-entity');
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function () {
                    var items = el.querySelectorAll('[data-id]');
                    var order = [];
                    items.forEach(function (item) {
                        order.push(parseInt(item.getAttribute('data-id')));
                    });

                    var formData = new URLSearchParams();
                    formData.append('_method', 'PATCH');
                    formData.append('_token', csrfToken);
                    formData.append('entity', entity);
                    order.forEach(function (id) { formData.append('order[]', id); });

                    fetch('/admin/sort-order', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: formData.toString()
                    })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            adminToast('Ordine aggiornato');
                        }
                    })
                    .catch(function () {
                        adminToast('Errore durante il salvataggio dell\'ordine.', 'error');
                    });
                }
            });
        }

        function toggleActive(entity, id, buttonEl) {
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var formData = new URLSearchParams();
            formData.append('_method', 'PATCH');
            formData.append('_token', csrfToken);
            formData.append('entity', entity);
            formData.append('id', id);

            fetch('/admin/toggle-active', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    var item = buttonEl.closest('[data-id]');
                    if (data.is_active) {
                        buttonEl.textContent = 'Attivo';
                        buttonEl.className = 'btn btn-success btn-sm toggle-active-btn';
                        if (item) item.style.opacity = '1';
                    } else {
                        buttonEl.textContent = 'Archiviato';
                        buttonEl.className = 'btn btn-secondary btn-sm toggle-active-btn';
                        if (item) item.style.opacity = '0.5';
                    }
                    adminToast(data.is_active ? 'Attivato' : 'Archiviato');
                }
            })
            .catch(function () {
                adminToast('Errore durante il cambio di stato.', 'error');
            });
        }
    </script>
</body>

</html>
