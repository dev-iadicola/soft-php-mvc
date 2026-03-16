<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <?php $url = isset($item->id) ? "/admin/footer-links-update/{$item->id}" : '/admin/footer-links'; ?>
            <form method="POST" action="<?= $url ?>" class="shadow-sm p-4 bg-light rounded border">
                @csrf
                <?php if (isset($item->id)) : ?>
                    @patch
                <?php endif; ?>

                <h1 class="h3 mb-3"><?= isset($item->id) ? 'Modifica link footer' : 'Nuovo link footer' ?></h1>

                <div class="mb-3">
                    <label for="title" class="form-label">Titolo</label>
                    <input type="text" class="form-control" id="title" name="title" maxlength="50" value="<?= $item->title ?? '' ?>" required>
                </div>

                <div class="mb-4">
                    <label for="link" class="form-label">Link</label>
                    <input type="text" class="form-control" id="link" name="link" maxlength="255" value="<?= $item->link ?? '' ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <?= isset($item->id) ? 'Salva modifiche' : 'Aggiungi link' ?>
                </button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="shadow-sm p-4 bg-white rounded border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Link dinamici del footer</h2>
                    <span class="badge text-bg-dark"><?= count($links) ?> elementi</span>
                </div>

                <?php if ($links === []) : ?>
                    <p class="text-muted mb-0">Nessun link configurato.</p>
                <?php else : ?>
                    <div class="list-group" id="sortable-list" data-entity="link_footer">
                        <?php foreach ($links as $link) : ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center gap-3" data-id="<?= $link->id ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="drag-handle text-muted" style="cursor: grab;"><i class="fa fa-bars"></i></span>
                                    <div>
                                        <strong><?= $link->title ?></strong>
                                        <div class="small text-muted"><?= $link->link ?></div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="/admin/footer-links-edit/<?= $link->id ?>" class="btn btn-outline-primary btn-sm">Modifica</a>
                                    <form action="/admin/footer-links-delete/<?= $link->id ?>" method="POST" onsubmit="return confirm('Eliminare <?= $link->title ?>?');">
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
