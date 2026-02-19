<?php if (isset($certificati)): ?>
    <div class="fade-in-section mt-5">
        <div class="bg-white rounded">
            <h3 class="text-center py-4 mb-5">PROFESSIONAL COURSES</h3>
        </div>

        <div class="my-5">
            <div class="row">
                <?php foreach ($certificati as $index => $certificato) : ?>
                    <div class="py-2 col-12 col-md-4 col-sm-6">

                        <!-- Content box -->
                        <div class="card shadow border-primary h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0"><?= strtoupper($certificato->title) ?></h5>
                                    <span class="ms-auto badge bg-primary"><?= $certificato->certified ?></span>
                                </div>

                                <p class="text-muted mb-1"><?= $certificato->ente ?></p>

                                <div class="mt-auto">
                                    <a href="<?= $certificato->link ?>"
                                        class="btn btn-sm btn-outline-primary mt-3"
                                        target="_blank">
                                        <i class="fa fa-external-link me-1"></i> View Certificate
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php endif; ?>
