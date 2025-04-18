<div class="fade-in-section mt-5 bg-white m-5 rounded-xl">
    <h3 class="text-4xl font-weight-bold mb-4 text-center text-dark">Cloud Projects</h3>

    <div class="container bg-white m-5">
        <div class="row">
            <?php foreach ($projects as $project) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-light rounded-lg">
                        <a href="#">
                            <img class="card-img-top rounded-t-lg" src="<?= $project->img ?>" alt="<?= $project->title ?>" />
                        </a>
                        <div class="card-body">
                            <a href="#">
                                <h5 class="card-title text-2xl font-weight-bold text-dark"><?= $project->title ?></h5>
                            </a>
                            <a href="<?= $project->link ?>"
                               class="btn btn-primary btn-sm d-flex align-items-center">
                                View Project
                                <svg class="ml-2 w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                                </svg>
                                <i class="fa fa-github fa-x2" aria-hidden="true"></i>
                                </a>
                            <?php if (isset($project->website)): ?>
                            <a href="<?= $project->website ?>"
                               class="btn btn-primary btn-sm d-flex align-items-center">
                                View Project
                                <svg class="ml-2 w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                                </svg>
                            </a>
                            <?php endif ?>


                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


