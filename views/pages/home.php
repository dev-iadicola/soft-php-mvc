<article class="fade-in-section mt-5 py-5">
    <div class="container">
        <div>
            <?php foreach ($profiles as $profile): ?>
                <div class=" p-5 shadow-lg mb-4 bg-dark rounded">
                    <!-- Intestazione -->
                    <div class="d-flex flex-row justify-content-center mb-5">
                        <h1 class="display-4 mb-2 fw-bold text-center dodgerblu w-100" id="name">
                            <i class="fa fa-code text-white" style="font-size:3rem" aria-hidden="true"></i>
                            <?php echo strtoupper($profile->name); ?>
                        </h1>

                    </div>
                    <!-- Messaggio di Benvenuto -->
                    <div class="d-flex flex-row justify-content-center ">
                        <h2 class="h3 mb-4 text-white" id="tagline" style="min-height: 4.5rem;">
                            <?php echo $profile->tagline; ?>
                        </h2>
                        <p class="h3 text-white" id="welcome" style="min-height: 3rem;">
                            <?php echo $profile->welcome_message; ?> 
                        </p>
                        <span id='cursor-text' class="h4 text-white fs-bold"> |</span>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
        <hr class="my-5 border-teal-300">
    </div>
</article>




@include('pages.progetti')

<div class="bg-white rounded-lg shadow-lg">
<div class="fade-in-section mt-5 bg-white m-5 rounded-xl">
    <h3 class="text-4xl font-weight-bold mb-4 text-center text-dark py-3 shadow w-50 rounded">Skills</h3>
        <p class="text-center text-lg text-muted mb-4">
            Sono appassionato di tecnologia e sempre aggiornato sulle ultime tendenze. Ecco le competenze che posso
            mettere al servizio del tuo progetto!
        </p>
        <div class="container">
            <div class="row">
                <?php foreach ($skills as $skill): ?>
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


<!-- Scroll to top button -->
<a class="fa fa-arrow-up btn-arrow" id="btn-arrow" href="#top" aria-hidden="true"></a>
<script src="<?= assets('js/typewrite.js') ?>"></script>