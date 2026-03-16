<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <?php $url = isset($partner->id) ? "/admin/partner-update/{$partner->id}" : '/admin/partner'; ?>
            <form method="POST" action="<?= $url ?>" class="shadow-sm p-4 bg-light rounded border">
                <?php if (isset($partner->id)) : ?>
                    @patch
                <?php endif; ?>

                <h1 class="h3 mb-3"><?= isset($partner->id) ? 'Modifica partner' : 'Nuovo partner' ?></h1>
                <p class="text-muted small mb-4">Gestisci collaborazioni, aziende clienti e link pubblici associati ai progetti.</p>

                <div class="mb-3">
                    <label for="name" class="form-label">Nome partner</label>
                    <input type="text" class="form-control" id="name" name="name" maxlength="30" value="<?= $partner->name ?? '' ?>" required>
                </div>

                <div class="mb-4">
                    <label for="website" class="form-label">Sito web</label>
                    <input type="url" class="form-control" id="website" name="website" value="<?= $partner->website ?? '' ?>" placeholder="https://example.com">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <?= isset($partner->id) ? 'Salva modifiche' : 'Aggiungi partner' ?>
                </button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="shadow-sm p-4 bg-white rounded border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Partner configurati</h2>
                    <span class="badge text-bg-dark"><?= count($partners) ?> elementi</span>
                </div>

                <?php if ($partners === []) : ?>
                    <p class="text-muted mb-0">Nessun partner disponibile.</p>
                <?php else : ?>
                    <div class="list-group">
                        <?php foreach ($partners as $item) : ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <strong><?= $item->name ?></strong>
                                    <?php if (!empty($item->website)) : ?>
                                        <div class="small"><a href="<?= $item->website ?>" target="_blank" rel="noopener noreferrer"><?= $item->website ?></a></div>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="/admin/partner-edit/<?= $item->id ?>" class="btn btn-outline-primary btn-sm">Modifica</a>
                                    <form action="/admin/partner-delete/<?= $item->id ?>" method="POST" onsubmit="return confirm('Eliminare <?= $item->name ?>?');">
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
