<div class="bg-white rounded-lg shadow-lg">

    <div class="fade-in-section mt-5 bg-white m-5 rounded-xl">
        <h3 class="text-4xl font-weight-bold mb-4 text-center text-dark py-3 shadow w-50 rounded">Projects</h3>

        <div class="container bg-white my-5">
            <div class="row">
                <?php foreach ($projects as $project) : ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-light rounded-lg">

                            <a href="<?php isset($project->website) ? $project->website : $project->link ?>">
                                <img class="card-img-top rounded-t-lg object-fit-contain border rounded"

                                    src="<?= validateImagePath($project->img, assets('img/no-img.svg')) ?>" alt="<?= $project->title ?>" />
                            </a>
                            <div class="">

                                <h5 class="card-title text-2xl font-weight-bold text-dark"><?= $project->title ?></h5>


                                <div class="cad-body shadow d-flex flex-column gap-3 ms-2">
                                    <?php if (isset($project->link)): ?>
                                        <a href="<?= $project->link ?>"
                                            class="btn bg-dark btn-sm d-flex align-items-center w-75 d-flex flex-row gap-3 p-0 px-3 text-dark hover-text-white ">
                                            Code
                                            <i class="fa fa-arrow-right" aria-hidden="true"></i>


                                            <p class="border rounded py-1 px-2 text-white mt-3">
                                                <i class="fa fa-github fa-x2" aria-hidden="true"></i>
                                                <?= $project->link ?>
                                            </p>

                                        </a>
                                    <?php endif ?>
                                    <?php if (isset($project->website)): ?>
                                        <a href="<?= $project->website ?>"
                                            class="btn btn-primary   btn-sm d-flex align-items-center w-75 d-flex flex-row gap-3 p-0 px-3 text-primary hover-text-white">
                                            Web Site
                                            <i class="fa fa-arrow-right" aria-hidden="true"></i>


                                            <p class="border rounded py-1 px-2 text-white mt-3">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                <?= $project->website ?>
                                            </p>
                                        </a>
                                    <?php endif ?>
                                </div>


                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>