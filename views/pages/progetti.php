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
        /* o quello che preferisci */
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
        <div class="container my-5">
            <div class="row">
                <?php foreach ($projects as $project) : ?>

                    <div class="py-2 pl-4 col-12 col-md-4 col-sm-6 ">
                        <div class="card shadow border-primary">

                            <a href="/progetti/<?php echo $project->id ?>" class="image-wrapper">
                                <img class="card-img-top rounded-t-lg object-fit-contain border rounded"

                                    src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>"
                                    alt="<?= strtoupper($project->title) ?>" />

                                <!-- EFFETTO VISUALIZZA PROGETTO -->
                                <div class="overlay-text">Apri</div>

                            </a>


                            <h5 class="card-title text-2xl font-weight-bold text-dark"><?= strtoupper($project->title) ?></h5>


                            <div class="cad-body d-flex flex-wrap justify-content-around gap-3 py-2 ">
                                <?php if (!isset($project->link)): ?>
                                    <a onmouseover="showArrow('<?= $project->link ?>')"
                                        onmouseleave="hideArrow('<?= $project->link ?>')"
                                        href="<?= $project->link ?>"
                                        target="_blank" rel="noopener noreferrer"
                                        class="btn bg-dark btn-sm d-flex align-items-center  
                                            d-flex flex-row gap-3 p-0 px-3  text-dark hover-text-white ">

                                        <i class="fa fa-arrow-right  arrow-icon" id="<?= $project->link ?>" aria-hidden="true"></i>


                                        <p class="border rounded  px-2 text-white mt-3">
                                            <i class="fa fa-github fa-x2" aria-hidden="true"></i>
                                            Code
                                        </p>

                                    </a>
                                <?php endif ?>
                                <?php if (urlExist($project->website)): ?>
                                    <a onmouseover="showArrow('<?= $project->website ?>')"
                                        onmouseleave="hideArrow('<?= $project->website ?>')"
                                        href="<?= $project->website ?>"
                                        target="_blank" rel="noopener noreferrer"
                                        class="btn btn-primary  btn-sm d-flex align-items-center  
                                            d-flex flex-row gap-3 p-0 px-3  text-primary hover-text-white">
                                        <i class="fa fa-arrow-right arrow-icon " id="<?= $project->website ?>" aria-hidden="true"></i>


                                        <p class="border rounded px-2 text-white mt-3">
                                            <i class="fa fa-eye" id="$project->website" aria-hidden="true"> </i>
                                            WebSite
                                        </p>
                                    </a>
                                <?php endif ?>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>
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