<div class="container admin-panel mt-5 ms-5">
    <h1 class="my-4">Cambia Password</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="/admin/password">
                <div class="form-group">
                    <label for="current_password">Password attuale</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>

                <div class="form-group mt-3">
                    <label for="password">Nuova password</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                </div>

                <div class="form-group mt-3">
                    <label for="confirmed">Conferma nuova password</label>
                    <input type="password" class="form-control" id="confirmed" name="confirmed" minlength="8" required>
                </div>

                <button type="submit" class="btn btn-primary mt-4">Aggiorna password</button>
            </form>
        </div>
    </div>
</div>
