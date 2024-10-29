<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Persona MVC - Admin</title>
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Font Awesome Icon CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.0/ckeditor5.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/admin.css">
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
                {{page}}
            </section>
        </div>

    </main>


    @include('components.admin.footer')

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

<!-- editor di test -->

<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.0/ckeditor5.css">


<script type="importmap">
            {
                "imports": {
                    "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.0/ckeditor5.js",
                    "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.0/"
                }
            }
        </script>
        <script type="module">
            import {
                ClassicEditor,
                Essentials,
                Paragraph,
                Bold,
                Italic,
                Font
            } from 'ckeditor5';

            document.querySelectorAll('.editor').forEach((element) => {
        ClassicEditor
            .create(element, {
                plugins: [Essentials, Paragraph, Bold, Italic, Font],
                toolbar: [
                    'undo', 'redo', '|', 'bold', 'italic', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
                ]
            })
            .then(editor => {
                // Puoi salvare ogni editor creato in un array per riferimento futuro
                window.editors = window.editors || [];
                window.editors.push(editor);
            })
            .catch(error => {
                console.error(error);
            });
    });
        </script>
        <!-- A friendly reminder to run on a server, remove this during the integration. -->
        <script>
		        window.onload = function() {
		            if ( window.location.protocol === "file:" ) {
		                alert( "This sample requires an HTTP server. Please serve this file with a web server." );
		            }
		        };
		</script>

</body>

</html>