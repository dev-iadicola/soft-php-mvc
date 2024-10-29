<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Software Developer - Luigi Iadicola</title>
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.png">

    <!-- Bootstrap CSS -->
    <!--
         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous" />
-->

<!-- Tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">

    <!-- font awesome icon cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <link rel="stylesheet" href="/assets/style.css">

    <script src="/assets/lib.js" defer></script>
    <link rel="stylesheet" href="/assets/cards.css">



</head>



<body class="animated-gradient">
    <!-- Il tuo contenuto qui -->

    <!-- Il tuo contenuto qui -->


    <main style="min-height: 100vh;">
        <header>
            <div id="menu-icon" class="">
                <span id="barra-1"></span>
                <span id="barra-2"></span>
                <span id="barra-3"></span>
            </div>
            <div id="menu" class="chiuso">
                <?php
                function isActivePage($menuItem, $currentPage)
                {
                    
                 
                    return strtolower($menuItem) == strtolower($currentPage) ? 'active' : '';
                    
                }
                ?>
                <nav>
                    <ul>
                        <li><a href="/" class="<?= isActivePage('home', $page) ?>">Home</a></li>
                        <li><a href="/contatti" class="<?= isActivePage('contatti', $page) ?>">Contatti</a></li>
                        <li><a href="/portfolio" class="<?= isActivePage('portfolio', $page) ?>">Portfolio</a></li>
                        <li><a href="/progetti" class="<?= isActivePage('progetti', $page) ?>">Progetti</a></li>
                        <li><a href="/certificati"  class="<?= isActivePage('corsi', $page) ?>">Certificati</a></li>
                         
                        <li><a target="_blank" href="https://github.com/AndroLuix/" class=>GitHub <i style="color: white;" class="fa fa-github" aria-hidden="true"></i></a> </li>
                    </ul>
                </nav>
               
                <!-- Altri contenuti della pagina -->
            </div>
        </header>

        <!-- messages -->
        @include('session.messages')

<div
        class="container mx-auto px-4 sm:px-6 lg:px-8 min-h-screen border-solid border-2 border-gray-200 rounded-xl p-10 m-10">            {{page}}
        </div>
    </main>

    @include('components.footer')
    @include('components.popup-cookie')
</body>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

</html>