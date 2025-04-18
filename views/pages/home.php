<article class="fade-in-section mt-5 py-5">
    <div class="container">
        <div class="text-center">
            <?php foreach ($profiles as $profile) : ?>
                <div class="bg-gradient-to-br from-teal-400 to-teal-600 text-white p-5 rounded-lg shadow-lg mb-4">
                    <!-- Intestazione -->
                    <h1 class="display-4 font-weight-bold mb-2"><?php echo $profile->name; ?></h1>
                    <h2 class="h4 font-weight-normal mb-4"><?php echo $profile->tagline; ?></h2>

                    <!-- Messaggio di Benvenuto -->
                    <?php echo $profile->welcome_message; ?>

                    <!-- Decorazione -->
                    <div class="position-absolute top-0 right-0 w-25 h-25 bg-white opacity-10 rounded-circle"></div>
                    <div class="position-absolute bottom-0 left-0 w-25 h-25 bg-white opacity-10 rounded-circle"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <hr class="my-5 border-teal-300">
    </div>
</article>

@include('pages.progetti')

<div class="bg-white rounded-lg shadow-lg">
    <div class="fade-in-section mt-5 py-5">
        <h1 class="text-dark text-4xl font-weight-bold mb-5 text-center">Scopri le mie Skills</h1>
        <p class="text-center text-lg text-muted mb-4">
            Sono appassionato di tecnologia e sempre aggiornato sulle ultime tendenze. Ecco le competenze che posso mettere al servizio del tuo progetto!
        </p>
        <div class="container">
            <div class="row">
                <?php foreach ($skills as $skill) : ?>
                    <article class="col-md-4 mb-4">
                        <div class="bg-dark text-white p-4 rounded-lg shadow-md text-center">
                            <h2 class="h4 font-weight-bold mb-2"><?= $skill->title; ?></h2>
                            <p class="text-muted"><?= $skill->description; ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<article class="fade-in-section mt-5 py-5">
    <div class="container py-5">
        <?php foreach ($articles as $article) : ?>
            <article class="row mb-5">
                <!-- Immagine dell'articolo -->
                <?php if (!empty($article->img)) : ?>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <img src="<?php echo $article->img; ?>" class="img-fluid rounded-lg shadow-md" alt="Immagine dell'articolo">
                    </div>
                <?php endif; ?>
                <!-- Contenuto testuale -->
                <div class="col-md-8">
                    <h1 class="h3 font-weight-bold text-dark mb-2"><?= $article->title; ?></h1>
                    <?php if (!empty($article->subtitle)) : ?>
                        <h2 class="h5 text-muted mb-3"><?= $article->subtitle; ?></h2>
                    <?php endif; ?>
                    <p class="text-muted mb-4"><?= $article->overview; ?></p>
                    <?php if (!empty($article->link)) : ?>
                        <a href="<?= $article->link; ?>" class="text-primary font-weight-bold">
                            Leggi di più
                        </a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</article>

<!-- Scroll to top button -->
<a class="fa fa-arrow-up btn-arrow" id="btn-arrow" href="#top" aria-hidden="true"></a>
