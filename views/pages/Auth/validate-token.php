<section class="container my-5 py-5">
    <form method="POST" action="/token/change-password" class="mx-auto bg-dark text-white rounded shadow p-4" style="max-width: 500px;">
        <div class="mb-4">
            <label for="password" class="form-label text-white fs-5">Inserisci la nuova password</label>

            <h3 class="text-danger mb-3">{{message}}</h3>

            <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input type="password" name="password" minlength="8" class="form-control bg-dark text-white border-secondary" id="password" placeholder="Password *" required>
            </div>

            <div class="mb-4">
                <label for="confirmed" class="form-label">Ripeti Password *</label>
                <input type="password" name="confirmed" minlength="8" class="form-control bg-dark text-white border-secondary" id="confirmed" placeholder="Ripeti Password *" required>
            </div>

            <input type="hidden" name="token" value="<?= $token ?>" readonly>

            <button type="submit" class="btn btn-primary w-100">Invia</button>
        </div>
    </form>
</section>
