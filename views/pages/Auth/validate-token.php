<section class="container my-5 py-5">
    <form method="POST" action="/token/change-password" class="mx-auto bg-dark text-white rounded shadow p-4" style="max-width: 500px;">
        <div class="mb-4">
            <label for="password" class="form-label text-white fs-5">Inserisci la nuova password</label>


            <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input type="password" name="password" minlength="8" class="form-control bg-dark text-white border-secondary" id="password" placeholder="Password *" required>
            </div>

            <div class="mb-4">
                <label for="confirmed" class="form-label">Ripeti Password *</label>
                <input type="password" name="confirmed" minlength="8" class="form-control bg-dark text-white border-secondary" id="confirmed" placeholder="Ripeti Password *" required>
            </div>
            <button type="button" onclick="seePassword()" class="btn btn-link text-white p-0">Mostra password</button>

            <input type="hidden" name="token" value="<?= $token ?>" readonly>

            <button type="submit" class="btn btn-primary w-100">Invia</button>
        </div>
    </form>
</section>
<script>
    const seePassword = () => {
        const passwordFields = document.querySelectorAll('input[type="password"], input[data-toggled="true"]');

        passwordFields.forEach(field => {
            if (field.type === 'password') {
                // Salva name originale se non presente
                if (!field.name) {
                    field.name = 'password';
                }
                field.setAttribute('data-toggled', 'true');
                field.type = 'text';
            } else if (field.dataset.toggled === 'true') {
                field.type = 'password';
                field.removeAttribute('data-toggled');
            }
        });
    };
</script>