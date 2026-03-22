<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <?php $url = isset($item->id) ? "/admin/contact-hero-update/{$item->id}" : '/admin/contact-hero'; ?>
            <form method="POST" action="<?= $url ?>" class="shadow-sm p-4 bg-light rounded border">
                @csrf
                <?php if (isset($item->id)) : ?>
                    @patch
                <?php endif; ?>

                <h1 class="h3 mb-3"><?= isset($item->id) ? 'Modifica hero contatti' : 'Nuovo hero contatti' ?></h1>

                <div class="mb-3">
                    <label for="badge" class="form-label">Badge</label>
                    <input type="text" class="form-control" id="badge" name="badge" maxlength="100" value="<?= $item->badge ?? '' ?>" required>
                </div>

                <div class="mb-3">
                    <label for="title_html" class="form-label">Titolo HTML</label>
                    <textarea class="form-control" id="title_html" name="title_html" rows="3" required><?= $item->title_html ?? '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="description_html" class="form-label">Descrizione HTML</label>
                    <textarea class="form-control" id="description_html" name="description_html" rows="5" required><?= $item->description_html ?? '' ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="primary_stat_value" class="form-label">Stat 1 valore</label>
                        <input type="text" class="form-control" id="primary_stat_value" name="primary_stat_value" maxlength="50" value="<?= $item->primary_stat_value ?? '' ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="primary_stat_label" class="form-label">Stat 1 etichetta</label>
                        <input type="text" class="form-control" id="primary_stat_label" name="primary_stat_label" maxlength="100" value="<?= $item->primary_stat_label ?? '' ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="secondary_stat_value" class="form-label">Stat 2 valore</label>
                        <input type="text" class="form-control" id="secondary_stat_value" name="secondary_stat_value" maxlength="50" value="<?= $item->secondary_stat_value ?? '' ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="secondary_stat_label" class="form-label">Stat 2 etichetta</label>
                        <input type="text" class="form-control" id="secondary_stat_label" name="secondary_stat_label" maxlength="100" value="<?= $item->secondary_stat_label ?? '' ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="technology_stat_label" class="form-label">Etichetta tecnologie</label>
                    <input type="text" class="form-control" id="technology_stat_label" name="technology_stat_label" maxlength="100" value="<?= $item->technology_stat_label ?? '' ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <?= isset($item->id) ? 'Salva modifiche' : 'Aggiungi hero' ?>
                </button>
            </form>
        </div>

        <div class="col-lg-7">
            <div class="shadow-sm p-4 bg-white rounded border">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Hero contatti</h2>
                    <span class="badge text-bg-dark"><?= count($heroes) ?> elementi</span>
                </div>

                <?php if ($heroes === []) : ?>
                    <p class="text-muted mb-0">Nessun hero configurato.</p>
                <?php else : ?>
                    <div class="list-group">
                        <?php foreach ($heroes as $hero) : ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <strong><?= $hero->badge ?></strong>
                                    <div class="small text-muted mt-1"><?= strip_tags($hero->title_html) ?></div>
                                    <div class="small text-muted"><?= strip_tags($hero->description_html) ?></div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="/admin/contact-hero-edit/<?= $hero->id ?>" class="btn btn-outline-primary btn-sm">Modifica</a>
                                    <form action="/admin/contact-hero-delete/<?= $hero->id ?>" method="POST" onsubmit="return confirm('Eliminare questo hero contatti?');">
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
