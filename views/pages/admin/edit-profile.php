<div class="container admin-panel mt-5 ms-5">
    <h1 class="my-4">Edit Profile</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="/admin/edit-profile">
                <div class="form-group">
                    <label for="email">Email account</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <button type="submit" class="btn btn-primary mt-4">Salva modifiche</button>
            </form>
        </div>
    </div>
</div>
