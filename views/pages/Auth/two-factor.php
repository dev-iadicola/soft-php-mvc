<form action="/two-factor" method="POST" class="vh-100 d-flex align-items-center justify-content-center bg-dark rounded-lg">
  <div class="card bg-secondary text-white shadow-lg" style="max-width: 28rem; width: 100%;">
    <div class="card-body p-4">
      <h2 class="card-title text-center text-uppercase fw-bold mb-3">Verifica 2FA</h2>
      <p class="text-center text-muted mb-4">Inserisci il codice a 6 cifre generato dalla tua app di autenticazione.</p>
      @csrf

      <div class="mb-4">
        <label for="code" class="form-label small fw-medium">Codice TOTP</label>
        <input
          type="text"
          class="form-control bg-dark border-secondary text-white"
          id="code"
          name="code"
          inputmode="numeric"
          pattern="[0-9]{6}"
          maxlength="6"
          placeholder="123456"
          required>
      </div>

      <button type="submit" class="btn btn-primary w-100 fw-semibold">
        Conferma accesso
      </button>
    </div>
  </div>
</form>
