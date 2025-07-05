<!-- Admin Panel -->
<div class="container admin-panel">
    <h1 class="my-4">Portfolio Management</h1>

    <!-- Form to Add portfolio -->
    <div class="mb-4 p-4 bg-light shadow rounded">
        <h3 class="text-center mb-4">Nuova Esperienza</h3>
        <?php
        $url = isset($pfolio->id) ? "/admin/portfolio-update/{$pfolio->id}" : '/admin/portfolio';
        ?>
        <form method="POST" action="<?= $url ?>" class="p-3">
            <div class="form-group mb-3">
                <label for="title" class="form-label">Titolo</label>
                <input type="text" class="form-control shadow-sm" id="title" name="title" value="<?= $pfolio->title ?? '' ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="overview" class="form-label">Descrizione</label>
                <textarea class="form-control editor shadow-sm" id="overview" name="overview" rows="5"><?=  $pfolio->overview ?? '' ?></textarea>
            </div>
            <div class="form-group mb-4">
                <label for="link" class="form-label">Link</label>
                <input type="url" class="form-control shadow-sm" id="link" name="link" value="<?= $pfolio->link ?? '' ?>">
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary shadow-sm px-4">Salva</button>
            </div>
        </form>
    </div>


    <!-- Portfolio Table -->
    <div class="container mb-4">
        <h3 class="text-center mb-4">Progetti Esistenti</h3>
        <div class="row" id="projectsCardBody">
            <?php foreach ($portfolio as $project): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary font-weight-bold"><?= $project->title ?></h5>
                            <p class="card-text text-muted"><?= $project->overview ?></p>
                            <?php if ($project->link !== ''): ?>
                                <a href="<?= $project->link ?>" target="_blank" class="btn btn-outline-primary mt-2">
                                    Vai al progetto
                                </a>
                            <?php endif ?>
                            <div class="mt-3 d-flex justify-content-between">
                                <form action="/admin/portfolio-delete/<?= $project->id ?>" method="POST" class="d-inline">
                                    @delete
                                    <button onclick="return confirm('Vuoi davvero eliminare <?= $project->title ?>?')" class="btn btn-danger btn-sm">Elimina</button>
                                </form>
                                <a href="/admin/portfolio-edit/<?= $project->id ?>" class="btn btn-warning btn-sm">Modifica</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>