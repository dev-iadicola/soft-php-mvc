<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-terminal"></i>
                        <strong>Terminal</strong>
                    </div>
                    <span class="badge bg-success">php soft</span>
                </div>

                <div class="card-body p-0">

                    <!-- Output area -->
                    <div class="bg-dark text-white p-3 font-monospace small" style="min-height: 300px; max-height: 500px; overflow-y: auto; white-space: pre-wrap;" id="terminal-output">
                        <?php if (!empty($error)) { ?>
<span class="text-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></span>
                        <?php } elseif (isset($output) && $output !== '') { ?>
<span class="text-success">$ php soft <?= htmlspecialchars($input ?? '', ENT_QUOTES) ?></span>
<?= htmlspecialchars($output, ENT_QUOTES) ?>
                        <?php } else { ?>
<span class="text-muted">Digita un comando per iniziare...</span>
                        <?php } ?>
                    </div>

                    <!-- Input area -->
                    <form method="POST" action="/admin/terminal" id="terminal-form">
                        @csrf
                        <div class="input-group border-top">
                            <span class="input-group-text bg-dark text-success font-monospace border-0 rounded-0">$</span>
                            <input
                                type="text"
                                name="command"
                                class="form-control bg-dark text-white font-monospace border-0 rounded-0 shadow-none"
                                placeholder="php soft migrate"
                                value="<?= htmlspecialchars($input ?? '', ENT_QUOTES) ?>"
                                autocomplete="off"
                                autofocus
                                list="commands-list"
                            />
                            <button type="submit" class="btn btn-success rounded-0 px-4">
                                <i class="fa fa-play"></i>
                            </button>
                        </div>

                        <datalist id="commands-list">
                            <?php foreach ($commands as $cmd) { ?>
                                <option value="php soft <?= $cmd ?>">
                            <?php } ?>
                        </datalist>
                    </form>
                </div>

                <div class="card-footer bg-light d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted small me-2">Comandi:</span>
                    <?php foreach ($commands as $cmd) { ?>
                        <button type="button" class="btn btn-outline-dark btn-sm font-monospace cmd-btn" data-cmd="php soft <?= $cmd ?>"><?= $cmd ?></button>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.cmd-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = document.querySelector('input[name="command"]');
            input.value = this.dataset.cmd;
            input.focus();
        });
    });

    var output = document.getElementById('terminal-output');
    if (output) output.scrollTop = output.scrollHeight;
</script>
