<?php if (isset($certificati)): ?>
    <div class="mt-5">
        <h3 class="text-center text-white display-4 mb-5">Professional Courses Timeline</h3>

        <div class="container position-relative pl-4 border-left border-primary">
            <?php foreach ($certificati as $index => $certificato) : ?>
                <div class="position-relative mb-5 pl-4">

                    <!-- Timeline indicator -->
                    <div class="position-absolute bg-primary rounded-circle border border-white" 
                         style="width: 16px; height: 16px; top: 5px; left: -9px;"></div>

                    <!-- Content box -->
                    <div class="card shadow border-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="mb-0"><?= $certificato->title ?></h5>
                                <span class="ml-auto badge badge-primary"><?= htmlspecialchars($certificato->certified) ?></span>
                            </div>

                            <p class="text-muted mb-1"><?= $certificato->ente ?></p>

                            <!-- Toggle button -->
                            <button class="btn btn-link p-0 text-primary" 
                                    onclick="toggleOverview(<?= $certificato->id ?>)">
                                Leggi di più
                            </button>

                            <!-- Overview -->
                            <div class="collapse mt-2" id="overview-<?= $certificato->id ?>">
                                <p class="text-muted small"><?= $certificato->overview ?></p>
                            </div>

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

    <!-- Toggle Script -->
    <script>
        function toggleOverview(id) {
            const el = document.getElementById('overview-' + id);
            if (el.classList.contains('show')) {
                $(el).collapse('hide');
            } else {
                $(el).collapse('show');
            }
        }
    </script>
<?php endif; ?>
