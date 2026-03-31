<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <title>Persona MVC - Admin</title>
    <link rel="icon" type="image/x-icon" href="<?= assets('img/favicon.png') ?>">

    <!-- Bootstrap CSS -->
    <link href="<?= assets("vendor/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet">
    <!-- Font Awesome Icon CDN -->
    <link rel="stylesheet" href="<?= assets('vendor/fontawesome/css/font-awesome.min.css') ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= assets('admin.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <!-- Devicon - Technology icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/devicon.min.css">
</head>



<body>
    <main style="min-height: 100vh;">
        <!-- Pulsante per aprire e chiudere la sidebar -->
        <button id="toggle-sidebar" class="btn btn-primary">☰ Open Sidebar</button>

        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-dark text-white p-3 flex-shrink-0">
            @include('components.admin.sidebar')
        </div>

        <div class="d-flex flex-row">
            <section class="container justify-content-center m-auto foo">
                @include('session.messages')
                <<page>>
            </section>
        </div>

    </main>


    @include('components.footer')

    <!-- JavaScript per gestire la visibilità della sidebar -->
    <script>
        document.getElementById('toggle-sidebar').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const isOpen = sidebar.classList.contains('open');
            if (isOpen) {
                sidebar.classList.remove('open');
                this.innerHTML = '☰ Open Sidebar'; // Modifica il testo del pulsante
            } else {
                sidebar.classList.add('open');
                this.innerHTML = '× Close Sidebar'; // Modifica il testo del pulsante
            }
        });
    </script>

    <!-- chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
    <script src="<?= assets('vendor/ckeditor/js/execute.js')?>"></script>

    <script>
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
                            var toast = document.createElement('div');
                            toast.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
                            toast.style.zIndex = '9999';
                            toast.textContent = 'Ordine aggiornato';
                            document.body.appendChild(toast);
                            setTimeout(function () { toast.remove(); }, 2000);
                        }
                    })
                    .catch(function () {
                        alert('Errore durante il salvataggio dell\'ordine.');
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
                }
            })
            .catch(function () {
                alert('Errore durante il cambio di stato.');
            });
        }
    </script>

    <!-- Editor init moved to assets/vendor/ckeditor/js/execute.js -->

    <!-- A friendly reminder to run on a server, remove this during the integration. -->
    <script>
        window.onload = function() {
            if (window.location.protocol === "file:") {
                alert("This sample requires an HTTP server. Please serve this file with a web server.");
            }
        };
    </script>




</body>

</html>
