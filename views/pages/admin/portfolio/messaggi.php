<div class="container m-5">
    <h2 class="my-4">Messaggi Ricevuti
        <small class="bg-primary rounded-circle p-1 px-3 fs-6 text-white">
            <?= count($contatti) ?>
        </small>
    </h2>

    <?php if (isset($contatto)): ?>
        <div class="list-group-item list-group-item-action my-5 rounded-2 border border-primary" id="message-opened">
            <div class="d-flex flex-row mt-2">
                <button class="btn btn-warning me-5" onclick="hideMessage()">Close X</button>
                <form action="/admin/contatti-delete/<?= $contatto->id ?>" method="POST">
                   @delete
                    <button class="btn btn-danger" onclick="return confirm('Sicuro di voler eliminare il messaggio di <?= $contatto->nome .' '.$contatto->typologie ?>?')">Delete X</button>
                </form>
            </div>
            <div class="py-5">
                <h5 class="mb-1">Mittente: <?= $contatto->nome ?> - <?= $contatto->typologie ?></h5>
                <h6 class="mb-1">Indirizzo email: <a href="mailto:<?= $contatto->email ?>"><?= $contatto->email ?></a></h6>
                <p class="mb-2 overflow-auto"><?= $contatto->messaggio ?></p>
                <small>Data: <?= date('d/m/Y - H:i:s', strtotime($contatto->created_at)) ?></small>
            </div>
        </div>
    <?php endif ?>

    <div class="list-group" style="max-height: 700px; overflow-y: auto;">
        <?php foreach ($contatti as $contatto): ?>
            <div class="list-group-item list-group-item-action my-2 py-4 rounded-2">
                <h5 class="mb-1">Mittente: <?= $contatto->nome ?> - <?= $contatto->typologie ?></h5>
                <h6 class="mb-1">Indirizzo email: <a href="mailto:<?= $contatto->email ?>"><?= $contatto->email ?></a></h6>
                <p class="mb-2 overflow-auto"><?= substr($contatto->messaggio, 0, 300) ?></p>
                <small>Data: <?= date('d/m/Y - H:i:s', strtotime($contatto->created_at)) ?></small>
                <p>
                    <a href="/admin/contatti/<?= $contatto->id ?>" class="btn btn-primary">Apri</a>
                </p>
            </div>
        <?php endforeach ?>
    </div>
</div>

<script>
    function hideMessage() {
        $('#message-opened').hide('slow');
    }
</script>
