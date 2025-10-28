<?php if (isset($certificati)): ?>
    <div class="fade-in-section mt-5">
        <div class="bg-white rounded">
            <h3 class="text-center py-4 mb-5">PROFESSIONAL CURSES</h3>
        </div>

        <div class="container  my-5">
            <div class="row">
                <?php foreach ($certificati as $index => $certificato) : ?>
                    <div class="position-relative py-2 pl-4 col-4">

                        <!-- Content box -->
                        <div class="card shadow border-primary">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0"><?= strtoupper($certificato->title) ?></h5>
                                    <span class="ml-auto badge badge-primary"><?= $certificato->certified ?></span>
                                </div>

                                <p class="text-muted mb-1"><?= $certificato->ente ?></p>

                                <a href="<?= $certificato->link ?>"
                                    class="btn btn-sm btn-outline-primary mt-3"
                                    target="_blank">
                                    <i class="fa fa-external-link mr-1"></i> View Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php endif; ?>