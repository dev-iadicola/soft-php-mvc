<!-- Admin Panel -->
<div class="container my-5">
    <h1 class="mb-4 display-6 text-center">Course Management</h1>

    <!-- Course Form -->
    <?php $url = isset($element->id) ? "/admin/corso-update/{$element->id}" : '/admin/corsi'; ?>
    <form method="POST" action="<?= $url ?>" class="shadow-lg p-4 bg-light rounded">
        <h3 class="mb-3"><?php (isset($element->title)) ? "Modifica ". $element->title : 'Add new' ?>
        </h3>

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title"
                placeholder="Title of certificate" value="<?= $element->title ?? '' ?>" required>
        </div>

        <div class="mb-3">
            <label for="ente" class="form-label">Institution</label>
            <input type="text" class="form-control" id="ente" name="ente"
                placeholder="Institution that issued the certificate" value="<?= $element->ente ?? '' ?>" required>
        </div>

        <div class="mb-3">
            <label for="overview" class="form-label">Overview</label>
            <textarea class="form-control editor" id="overview" name="overview" rows="3" placeholder="Course overview"><?= $element->overview ?? '' ?></textarea>
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Link</label>
            <input type="url" class="form-control" id="link" name="link"
                placeholder="https://example.com" value="<?= $element->link ?? '' ?>" required>
        </div>

        <div class="mb-4">
            <label for="date" class="form-label">Date Certified</label>
            <input type="number" class="form-control" id="date" name="certified" min="1900" max="2099" step="1"
                value="<?= $element->certified ?? date('Y') ?>" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Save Course</button>
    </form>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (dopo jQuery se usato) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Existing Certifications -->
    <div class="mt-5">
        <h3 class="mb-3 text-center">Existing Certifications</h3>
        <div class="accordion" id="certificationsAccordion">
            <?php foreach ($corsi as $corso) : ?>
                <div class="accordion-item mb-2">
                    <!-- Header -->
                    <h2 class="accordion-header" id="heading<?= $corso->id ?>">
                        <button class="accordion-button collapsed bg-primary text-white" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse<?= $corso->id ?>"
                            aria-expanded="false" aria-controls="collapse<?= $corso->id ?>">
                            <?= $corso->title ?> - <?= $corso->ente?>
                        </button>
                    </h2>

                    <!-- Body with Details -->
                    <div id="collapse<?= $corso->id ?>" class="accordion-collapse collapse"
                        aria-labelledby="heading<?= $corso->id ?>" data-bs-parent="#certificationsAccordion">
                        <div class="accordion-body">
                            <p class="text-muted">
                                <strong>Overview:</strong> <?= $corso->overview ?>
                            </p>
                            <p>
                                <strong>Certified Date:</strong> <?= $corso->certified ?>
                            </p>
                            <?php if ($corso->link) : ?>
                                <a href="<?= $corso->link ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    Open Link
                                </a>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mt-3">
                                <form action="/admin/corso-delete/<?= $corso->id ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete <?= $corso->title ?>?')">
                                    @delete
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <a href="/admin/corso-edit/<?= $corso->id ?>" class="btn btn-warning btn-sm">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


</div>