
 


    <a href="/" target="_blank" class="d-flex align-items-center mb-3 mt-5 mb-md-0 me-md-auto text-white text-decoration-none">
        <svg class="bi me-2" width="16" height="70">
            <use xlink:href="#bootstrap"></use>
        </svg>
        <span class="fs-4 mt-5">Visita il tuo portflio</span>
    </a>
    <hr>
    <?php
    function isActive($menuItem, $currentPage)
    {
        return strtolower($menuItem) === strtolower($currentPage) ? 'active' : '';
    }
    $page = $this->mvc->request->getRequestPath();

    ?>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item my-2">
            <a href="/admin/dashboard" class="nav-link text-white <?php echo isActive('/admin/dashboard', $page) ?>" aria-current="page">
                <svg class="bi me-2" width="16" height="16">
                    <use xlink:href="#home"></use>
                </svg>
                Dashboard
            </a>
        </li>
        <li class="nav-item my-2">
            <div>
                <button class="btn btn-light w-100 text-left px-4" type="button" data-toggle="collapse" data-target="#collapsePortfolio" aria-expanded="false" aria-controls="collapsePortfolio">
                    Gestione Portfolio
                </button>

                <div class="collapse" id="collapsePortfolio">
                    <div class="card card-body mt-2">
                        <a class="dropdown-item <?php echo isActive('/admin/home', $page) ?>" href="/admin/home">Gestione Home Page</a>
                        <a class="dropdown-item <?php echo isActive('/admin/contatti', $page) ?>" href="/admin/contatti">Messaggi</a>
                        <a class="dropdown-item <?php echo isActive('/admin/portfolio', $page) ?>" href="/admin/portfolio">Portfolio</a>
                        <a class="dropdown-item <?php echo isActive('/admin/progetti', $page) ?>" href="/admin/progetti">Progetti</a>
                        <a class="dropdown-item <?php echo isActive('/admin/corsi', $page) ?>" href="/admin/corsi">Corsi Professionali</a>
                        <a class="dropdown-item <?php echo isActive('/admin/cv', $page) ?>" href="/admin/cv">Curriculum</a>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item my-2">
            <div>
                <button class="btn btn-light w-100 text-left px-4" type="button" data-toggle="collapse" data-target="#collapseLaws" aria-expanded="false" aria-controls="collapseLaws">
                    Laws
                </button>

                <div class="collapse" id="collapseLaws">
                    <div class="card card-body mt-2">
                        <a class="dropdown-item <?php echo isActive('/admin/laws', $page) ?>" href="/admin/laws">Policy and Laws</a>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item my-2">
            <div>
                <button class="btn btn-light w-100 text-left px-4" type="button" data-toggle="collapse" data-target="#collapseUser" aria-expanded="false" aria-controls="collapseUser">
                    User
                </button>

                <div class="collapse" id="collapseUser">
                    <div class="card card-body mt-2">
                        <a class="dropdown-item <?php echo isActive('/admin/settings', $page) ?>" href="/admin/settings">Impostazioni</a>
                        <a class="dropdown-item <?php echo isActive('/admin/password', $page) ?>" href="/admin/password">Cambia Password</a>
                        <a class="dropdown-item <?php echo isActive('/admin/edit', $page) ?>" href="/admin/edit">Edit Profile</a>
                        <a class="dropdown-item <?php echo isActive('/admin/logs', $page) ?>" href="/admin/logs">Logs</a>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item my-5">
            <a href="/admin/logout" class="text-white text-decoration-none no-underline" aria-current="page">
                <svg class="bi me-2" width="16" height="16">
                    <use xlink:href="#logout"></use>
                </svg>
                <strong>Log-out</strong> <i class="ml-3 fa fa-sign-out" aria-hidden="true"></i>
            </a>
        </li>
    </ul>
    <hr>




<script>
    // JavaScript per mantenere aperto il collapse se la pagina è attiva
    document.addEventListener('DOMContentLoaded', function () {
        var currentPath = '<?php echo $page; ?>';
        var collapseIds = ['collapsePortfolio', 'collapseLaws', 'collapseUser'];

        collapseIds.forEach(function (id) {
            var button = document.querySelector('[data-target="#' + id + '"]');
            var collapse = document.getElementById(id);

            // Check if the collapse should be open based on the current page
            if (button && collapse) {
                var links = collapse.querySelectorAll('a');
                links.forEach(function (link) {
                    if (link.href.includes(currentPath)) {
                        $(collapse).collapse('show');
                    }
                });
            }
        });
    });
</script>
</body>
</html>
