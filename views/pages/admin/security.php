<div class="container admin-panel mt-5 ms-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Sicurezza account</h1>
            <p class="text-muted mb-0">Gestisci l'autenticazione a due fattori del tuo account admin.</p>
        </div>
        <span class="badge badge-<?= $user->two_factor_enabled ? 'success' : 'secondary' ?> p-2">
            <?= $user->two_factor_enabled ? '2FA attiva' : '2FA non attiva' ?>
        </span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-3">Email account</h2>
            <p class="mb-0"><strong><?= htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') ?></strong></p>
        </div>
    </div>

    <?php if ($user->two_factor_enabled): ?>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h2 class="h5 mb-3">Autenticazione a due fattori attiva</h2>
                <p class="text-muted">Il login richiederà password e codice TOTP generato dalla tua app di autenticazione.</p>

                <form method="POST" action="/admin/security/two-factor/disable" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Disattivare la 2FA per questo account?')">
                        Disattiva 2FA
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="row mt-4">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-3">1. Scansiona il QR code</h2>
                        <p class="text-muted">Apri Google Authenticator, 1Password, Authy o un'app compatibile TOTP e scansiona questo QR.</p>

                        <div
                            id="two-factor-qr"
                            data-uri="<?= htmlspecialchars($provisioningUri ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            class="border rounded bg-white p-3 d-inline-flex align-items-center justify-content-center"
                            style="min-width: 240px; min-height: 240px;"></div>

                        <p class="small text-muted mt-3 mb-1">Secret manuale</p>
                        <code class="d-inline-block p-2 bg-light text-dark rounded"><?= htmlspecialchars($setupSecret ?? '', ENT_QUOTES, 'UTF-8') ?></code>

                        <p class="small text-muted mt-3 mb-0" style="word-break: break-all;">
                            URI provisioning: <?= htmlspecialchars($provisioningUri ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-3">2. Conferma il codice</h2>
                        <p class="text-muted">Inserisci un codice a 6 cifre generato dalla tua app per attivare la 2FA.</p>

                        <form method="POST" action="/admin/security/two-factor/enable">
                            @csrf
                            <div class="form-group">
                                <label for="code">Codice TOTP</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="code"
                                    name="code"
                                    inputmode="numeric"
                                    pattern="[0-9]{6}"
                                    maxlength="6"
                                    placeholder="123456"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4">Abilita 2FA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.4/build/qrcode.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var container = document.getElementById('two-factor-qr');

            if (!container) {
                return;
            }

            var uri = container.getAttribute('data-uri');

            if (!uri || typeof QRCode === 'undefined' || typeof QRCode.toString !== 'function') {
                return;
            }

            QRCode.toString(uri, { type: 'svg', width: 220, margin: 1 }, function (error, svg) {
                if (!error && svg) {
                    container.innerHTML = svg;
                }
            });
        });
        </script>
    <?php endif; ?>
</div>
