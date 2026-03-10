<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?= csrf_token() ?>">


    <title>Software Developer - Luigi Iadicola</title>
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.png" />


    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- Custom styles -->
    <link rel="stylesheet" href="/assets/style.css" />
    <link rel="stylesheet" href="/assets/cards.css" />
    <link rel="stylesheet" href="/assets/effect.css" />
    <link rel="stylesheet" href="/assets/colors.css" />
    <script src="/assets/lib.js" defer></script>
</head>

<body class="animated-gradient">
    <main style="min-height: 100vh;">
        <header>
            <div id="menu-icon">
                <span id="barra-1"></span>
                <span id="barra-2"></span>
                <span id="barra-3"></span>
            </div>
            <div id="menu" class="chiuso">

                <nav>
                    <ul>
                        <li><a href="/" class="{{ isActivePage('home', $page) }}">Home</a></li>
                        <li><a href="/contatti" class="{{ isActivePage('contatti', $page) }}">Contatti</a></li>
                        <li><a href="/portfolio" class="{{ isActivePage('portfolio', $page) }}">Portfolio</a></li>
                        <li><a href="/progetti" class="{{ isActivePage('progetti', $page) }}">Progetti</a></li>
                        <li><a href="/certificati" class="{{ isActivePage('corsi', $page) }}">Certificati</a></li>
                        <li><a target="_blank" href="https://github.com/dev-iadicola/">GitHub <i class="fa fa-github text-white" aria-hidden="true"></i></a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <!-- messages -->
        @include('session.messages')

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 min-h-screen border-solid border-2 border-gray-200 rounded-xl p-10 m-10">
            <<page>>
        </div>
    </main>

    @include('components.footer')
    @include('components.popup-cookie')

    <!-- jQuery + Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>

</body>

</html>