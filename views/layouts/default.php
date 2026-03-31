<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <?php $seo = $seo ?? []; ?>
    <?php if (!empty($seo['description'])) : ?>
        <meta name="description" content="<?= htmlspecialchars($seo['description']) ?>">
    <?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($seo['title'] ?? 'Iadicola // dev') ?>">
    <?php if (!empty($seo['description'])) : ?>
        <meta property="og:description" content="<?= htmlspecialchars($seo['description']) ?>">
    <?php endif; ?>
    <?php if (!empty($seo['image'])) : ?>
        <meta property="og:image" content="<?= htmlspecialchars($seo['image']) ?>">
    <?php endif; ?>
    <?php if (!empty($seo['url'])) : ?>
        <meta property="og:url" content="<?= htmlspecialchars($seo['url']) ?>">
        <link rel="canonical" href="<?= htmlspecialchars($seo['url']) ?>">
    <?php endif; ?>

    <title><?= htmlspecialchars($seo['title'] ?? 'Iadicola // dev') ?></title>
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.png" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <!-- Devicon - Technology icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/devicon.min.css">

    <!-- Custom styles -->
    <link rel="stylesheet" href="/assets/style.css" />
    <link rel="stylesheet" href="/assets/cards.css" />
    <link rel="stylesheet" href="/assets/effect.css" />
    <link rel="stylesheet" href="/assets/colors.css" />
    <script src="/assets/lib.js" defer></script>
</head>

<body>
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
                        <li><a href="/" class="{{ isActivePage('home', $page) }}">~/home</a></li>
                        <li><a href="/contatti" class="{{ isActivePage('contatti', $page) }}">~/contatti</a></li>
                        <li><a href="/portfolio" class="{{ isActivePage('portfolio', $page) }}">~/portfolio</a></li>
                        <li><a href="/progetti" class="{{ isActivePage('progetti', $page) }}">~/progetti</a></li>
                        <li><a href="/tech-stack" class="{{ isActivePage('tech-stack', $page) }}">~/tech-stack</a></li>
                        <li><a href="/certificati" class="{{ isActivePage('corsi', $page) }}">~/certificati</a></li>
                        <li><a target="_blank" href="https://github.com/dev-iadicola/"><i class="fa fa-github" aria-hidden="true"></i> github</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        @include('session.messages')

        <div class="container" style="max-width: 1100px; padding: 1.5rem;">
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
