<div class="container m-5">
    <h2 class="my-4"><i class="fa fa-envelope"></i> Template Email</h2>
    <p class="text-muted">Gestisci i template delle email automatiche. Usa i placeholder <code>{nome}</code>, <code>{email}</code>, <code>{messaggio}</code> nel corpo del template.</p>

    <?php if (isset($template)) : ?>
        <!-- Form modifica template -->
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-pencil"></i> Modifica: <?= htmlspecialchars($template->slug) ?>
            </div>
            <div class="card-body">
                <form action="/admin/email-templates/<?= $template->id ?>" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="subject"><strong>Oggetto</strong></label>
                        <input type="text" name="subject" id="subject" class="form-control" value="<?= htmlspecialchars($template->subject) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="body"><strong>Corpo (HTML)</strong></label>
                        <textarea name="body" id="body" class="form-control editor" rows="12" required><?= $template->body ?></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" <?= $template->is_active ? 'checked' : '' ?>>
                        <label for="is_active" class="form-check-label">Attivo</label>
                        <small class="form-text text-muted d-block">Se disattivato, l'email automatica non verrà inviata.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Salva</button>
                        <a href="/admin/email-templates" class="btn btn-secondary">Annulla</a>
                    </div>
                </form>
            </div>

            <!-- Preview -->
            <div class="card-footer">
                <strong>Anteprima:</strong>
                <div class="border p-3 mt-2 bg-white" style="max-height: 400px; overflow-y: auto;">
                    <?= $template->body ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lista template -->
    <div class="card">
        <div class="card-header">
            <i class="fa fa-list"></i> Template disponibili
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Slug</th>
                        <th>Oggetto</th>
                        <th>Stato</th>
                        <th>Ultima modifica</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates ?? [] as $tpl) : ?>
                        <tr>
                            <td><code><?= htmlspecialchars($tpl->slug) ?></code></td>
                            <td><?= htmlspecialchars($tpl->subject) ?></td>
                            <td>
                                <?php if ($tpl->is_active) : ?>
                                    <span class="badge badge-success">Attivo</span>
                                <?php else : ?>
                                    <span class="badge badge-secondary">Disattivo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $tpl->updated_at ? date('d/m/Y H:i', strtotime($tpl->updated_at)) : '—' ?></td>
                            <td>
                                <a href="/admin/email-templates/<?= $tpl->id ?>/edit" class="btn btn-sm btn-primary">
                                    <i class="fa fa-pencil"></i> Modifica
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($templates)) : ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Nessun template configurato. Esegui la migration e il seeder.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
