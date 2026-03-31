<div class="container m-5">
    <h2 class="my-4">Messaggi Ricevuti
        <small class="bg-primary rounded-circle p-1 px-3 fs-6 text-white">
            <?= count($contatti) ?>
        </small>
    </h2>

    <!-- Filtro per tipologia -->
    <form method="GET" action="/admin/contatti" class="mb-4 d-flex align-items-center gap-2">
        <label for="typologie" class="form-label mb-0 mr-2"><strong>Filtra per tipologia:</strong></label>
        <select name="typologie" id="typologie" class="form-control" style="max-width: 250px;" onchange="this.form.submit()">
            <option value="">Tutte</option>
            <?php foreach ($typologies ?? [] as $typ) : ?>
                <option value="<?= htmlspecialchars($typ) ?>" <?= ($typologie ?? '') === $typ ? 'selected' : '' ?>>
                    <?= htmlspecialchars($typ) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php

            use App\Core\Helpers\Types\StrHelper;

 if (isset($contatto)): ?>
        <div class="list-group-item list-group-item-action my-5 rounded-2 border border-primary" id="message-opened">
            <div class="d-flex flex-row mt-2 gap-2">
                <button class="btn btn-warning" onclick="hideMessage()">Chiudi X</button>
                <form action="{{route('admin.contatti.delete', ['id'=>$contatto->id])}}" method="POST">
                   @csrf
                   @delete
                    <button class="btn btn-danger" onclick="return confirm('Sicuro di voler eliminare il messaggio di <?= htmlspecialchars($contatto->nome) ?>?')">Elimina</button>
                </form>
                <form action="/admin/contatti/<?= $contatto->id ?>/toggle-read" method="POST">
                    @csrf
                    <button type="submit" class="btn <?= $contatto->is_read ? 'btn-outline-secondary' : 'btn-success' ?>">
                        <?= $contatto->is_read ? 'Segna come non letto' : 'Segna come letto' ?>
                    </button>
                </form>
            </div>
            <div class="py-5">
                <h5 class="mb-1">Mittente: <?= htmlspecialchars($contatto->nome) ?> — <?= htmlspecialchars($contatto->typologie ?? '') ?></h5>
                <h6 class="mb-1">Email: <a href="mailto:<?= htmlspecialchars($contatto->email) ?>"><?= htmlspecialchars($contatto->email) ?></a></h6>
                <p class="mb-2 overflow-auto"><?= nl2br(htmlspecialchars($contatto->messaggio)) ?></p>
                <small class="text-muted">Data: <?= date('d/m/Y - H:i:s', strtotime($contatto->created_at)) ?></small>
            </div>

            <!-- Form risposta -->
            <div class="mt-3 border-top pt-3">
                <h6><i class="fa fa-reply"></i> Rispondi a <?= htmlspecialchars($contatto->nome) ?></h6>
                <form action="/admin/contatti/<?= $contatto->id ?>/reply" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <textarea name="reply_body" class="form-control" rows="4" placeholder="Scrivi la tua risposta..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Inviare la risposta a <?= htmlspecialchars($contatto->email) ?>?')">
                        <i class="fa fa-paper-plane"></i> Invia risposta
                    </button>
                </form>
            </div>
        </div>
    <?php endif ?>

    <div class="list-group" style="max-height: 700px; overflow-y: auto;">
        <?php foreach ($contatti as $msg): ?>
            <div class="list-group-item list-group-item-action my-2 py-4 rounded-2 <?= !$msg->is_read ? 'border-left: 4px solid #ffc107;' : '' ?>" style="<?= !$msg->is_read ? 'border-left: 4px solid #ffc107; background-color: rgba(255,193,7,0.05);' : '' ?>">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1" style="<?= !$msg->is_read ? 'font-weight:bold;' : '' ?>">
                            Mittente: <?= htmlspecialchars($msg->nome) ?> — <?= htmlspecialchars($msg->typologie ?? '') ?>
                            <?php if (!$msg->is_read) : ?>
                                <span class="badge badge-warning ml-2">Nuovo</span>
                            <?php endif; ?>
                        </h5>
                        <h6 class="mb-1">Email: <a href="mailto:<?= htmlspecialchars($msg->email) ?>"><?= htmlspecialchars($msg->email) ?></a></h6>
                        <p class="mb-2 overflow-auto"><?= htmlspecialchars(StrHelper::truncate($msg->messaggio, 0, 300)) ?></p>
                        <small class="text-muted">Data: <?= (new DateTime($msg->created_at))->format('d/m/Y - H:i:s') ?></small>
                    </div>
                    <div class="d-flex flex-column gap-1">
                        <a href="{{route('admin.contatti', ['id'=>$msg->id])}}" class="btn btn-primary btn-sm">Apri</a>
                        <form action="/admin/contatti/<?= $msg->id ?>/toggle-read" method="POST" class="mt-1">
                            @csrf
                            <button type="submit" class="btn btn-sm <?= $msg->is_read ? 'btn-outline-secondary' : 'btn-outline-warning' ?>">
                                <i class="fa <?= $msg->is_read ? 'fa-envelope-open' : 'fa-envelope' ?>"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>

<script>
    function hideMessage() {
        $('#message-opened').hide('slow');
    }
</script>
