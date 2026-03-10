<style>
    .arrow-icon {
        display: inline-block;
        opacity: 1;
        transform: translateX(0);
        transition:
            opacity 0.3s ease,
            transform 0.3s ease,
            width 0.3s ease,
            margin 0.3s ease;
        width: auto;
        margin-right: 8px;
    }

    .arrow-icon.hidden-arrow {
        opacity: 0;
        transform: translateX(-10px);
        width: 0;
        margin-right: 0;
        overflow: hidden;
    }
</style>

    <div class="fade-in-section mt-5">
        <div class="bg-white rounded">
            <h3 class="text-center py-4 mb-5">PROJECTS</h3>
        </div>

        <div class="my-5">
            <div class="row">
                <?php foreach ($projects as $project) { ?>

                    <div class="py-2 col-12 col-md-4 col-sm-6">
                        <div class="card shadow border-primary h-100">

                            <a href="/progetti/<?php echo urlencode($project->title) ?>" class="image-wrapper">
                                <img class="card-img-top object-fit-contain border rounded"

                                    src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>"
                                    alt="<?= strtoupper($project->title) ?>" />

                                <!-- EFFETTO VISUALIZZA PROGETTO -->
                                <div class="overlay-text">Apri</div>
                            </a>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-2xl font-weight-bold text-dark"><?= strtoupper($project->title) ?></h5>

                                <p class="p-2 mx-3 border border-white shadow rounded-lg bg-dark text-white flex-grow-1">
                                    {{{$project->overview }}}
                                </p>

                                <div class="d-flex flex-wrap justify-content-around gap-3 py-2">
                                    <?php if (!is_null($project->link)) { ?>
                                        <a onmouseover="showArrow('arrow-<?= $project->id ?>-code')"
                                            onmouseleave="hideArrow('arrow-<?= $project->id ?>-code')"
                                            href="<?= $project->link ?>"
                                            target="_blank" rel="noopener noreferrer"
                                            class="btn bg-dark btn-sm d-flex align-items-center gap-2 p-0 px-3 text-dark hover-text-white">

                                            <i class="fa fa-arrow-right arrow-icon hidden-arrow" id="arrow-<?= $project->id ?>-code" aria-hidden="true"></i>

                                            <p class="border rounded px-2 text-white mt-3">
                                                <i class="fa fa-github fa-x2" aria-hidden="true"></i>
                                                Code
                                            </p>
                                        </a>
                                    <?php } ?>
                                    <?php if (!is_null($project->website)) { ?>
                                        <a onmouseover="showArrow('arrow-<?= $project->id ?>-web')"
                                            onmouseleave="hideArrow('arrow-<?= $project->id ?>-web')"
                                            href="<?= $project->website ?>"
                                            target="_blank" rel="noopener noreferrer"
                                            class="btn btn-primary btn-sm d-flex align-items-center gap-2 p-0 px-3 text-primary hover-text-white">
                                            <i class="fa fa-arrow-right arrow-icon hidden-arrow" id="arrow-<?= $project->id ?>-web" aria-hidden="true"></i>

                                            <p class="border rounded px-2 text-white mt-3">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                WebSite
                                            </p>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
    </div>


<script>
    function showArrow(id) {
        const arrow = document.getElementById(id);
        if (arrow) {
            arrow.classList.remove('hidden-arrow');
        }
    }

    function hideArrow(id) {
        const arrow = document.getElementById(id);
        if (arrow) {
            arrow.classList.add('hidden-arrow');
        }
    }
</script>
