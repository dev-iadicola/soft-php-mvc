<a href="/" target="_blank" class="d-flex align-items-center mb-3 mt-5 mb-md-0 me-md-auto text-white text-decoration-none">
    <svg class="bi me-2" width="16" height="70">
        <use xlink:href="#bootstrap"></use>
    </svg>
    <span class="fs-4 mt-5">Visita il tuo sito</span>
</a>
<hr>

<?php $page  = request()->uri() ?>
<ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item my-2">
        <a href="{{route('admin.dashboard')}}" class="nav-link text-white {{ isActivePage("{{{route('admin.dashboard')}}}",
         request()->uri()) }} aria-current="page">
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
                    <a class="dropdown-item  text-dark {{ isActivePage(route('admin.dashboard'), $page) }}"
                        href=" {{{ route('admin.home') }}}  ">Gestione Home Page</a>
                    
                        <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/contatti', $page) ?>"
                        href="/admin/contatti">Messaggi</a>
                   
                       <a class="dropdown-item  text-dark<?php echo isActivePage(route('admin.projects'), $page) ?>"
                        href="{{route('admin.projects')}}">Progetti</a>
                    
                        <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/corsi', $page) ?>"
                        href="/admin/corsi">Corsi Professionali</a>
                    
                        <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/cv', $page) ?>"
                        href="/admin/cv">Curriculum</a>
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
                    <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/laws', $page) ?>" href="/admin/laws">Policy and Laws</a>
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
                    <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/settings', $page) ?>" href="/admin/settings">Impostazioni</a>
                    <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/password', $page) ?>" href="/admin/password">Cambia Password</a>
                    <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/edit', $page) ?>" href="/admin/edit">Edit Profile</a>
                    <a class="dropdown-item  text-dark<?php echo isActivePage('/admin/logs', $page) ?>" href="/admin/logs">Logs</a>
                </div>
            </div>
        </div>
    </li>
    <li class="nav-item my-5">
        <form action="POST" method="{{route('logout')}}" class="text-white text-decoration-none no-underline" aria-current="page">
            <svg class="bi me-2" width="16" height="16">
                <use xlink:href="#logout"></use>
            </svg>
            <strong>Log-out</strong><span></span> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
            </svg>
        </form>
    </li>
</ul>
<hr>




<script>
    // JavaScript per mantenere aperto il collapse se la pagina è attiva
    document.addEventListener('DOMContentLoaded', function() {
        var currentPath = '<?php echo $page; ?>';
        var collapseIds = ['collapsePortfolio', 'collapseLaws', 'collapseUser'];

        collapseIds.forEach(function(id) {
            var button = document.querySelector('[data-target="#' + id + '"]');
            var collapse = document.getElementById(id);

            // Check if the collapse should be open based on the current page
            if (button && collapse) {
                var links = collapse.querySelectorAll('a');
                links.forEach(function(link) {
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