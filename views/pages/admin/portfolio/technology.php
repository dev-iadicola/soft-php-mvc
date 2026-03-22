<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <?php $url = isset($technology->id) ? "/admin/technology-update/{$technology->id}" : '/admin/technology'; ?>
            <form method="POST" action="<?= $url ?>" class="shadow-sm p-4 bg-light rounded border">
                @csrf
                <?php if (isset($technology->id)) : ?>
                    @patch
                <?php endif; ?>

                <h1 class="h3 mb-3"><?= isset($technology->id) ? 'Modifica tecnologia' : 'Nuova tecnologia' ?></h1>
                <p class="text-muted small mb-4">Gestisci lo stack tecnologico usato nelle sezioni pubbliche e nei progetti.</p>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome tecnologia</label>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        name="name"
                        maxlength="100"
                        value="<?= $technology->name ?? '' ?>"
                        placeholder="Es. Laravel"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <?= isset($technology->id) ? 'Salva modifiche' : 'Aggiungi tecnologia' ?>
                </button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="shadow-sm p-4 bg-white rounded border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Stack attuale</h2>
                    <span class="badge text-bg-dark"><?= count($technologies) ?> elementi</span>
                </div>

                <?php if ($technologies === []) : ?>
                    <p class="text-muted mb-0">Nessuna tecnologia configurata.</p>
                <?php else : ?>
                    <div class="list-group" id="sortable-list" data-entity="technology">
                        <?php foreach ($technologies as $item) : ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center gap-3" data-id="<?= $item->id ?>" style="<?= $item->is_active ? '' : 'opacity: 0.5;' ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="drag-handle text-muted" style="cursor: grab;"><i class="fa fa-bars"></i></span>
                                    <div>
                                        <strong><?= $item->name ?></strong>
                                        <div class="text-muted small">ID #<?= $item->id ?></div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-<?= $item->is_active ? 'success' : 'secondary' ?> btn-sm toggle-active-btn" onclick="toggleActive('technology', <?= $item->id ?>, this)"><?= $item->is_active ? 'Attivo' : 'Archiviato' ?></button>
                                    <a href="/admin/technology-edit/<?= $item->id ?>" class="btn btn-outline-primary btn-sm">Modifica</a>
                                    <form action="/admin/technology-delete/<?= $item->id ?>" method="POST" onsubmit="return confirm('Eliminare <?= $item->name ?>?');">
                                        @csrf
                                        @delete
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Elimina</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded', function () { initSortable('sortable-list'); });</script>
