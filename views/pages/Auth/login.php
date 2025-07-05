<form action="/login" method="POST" class="vh-100 d-flex align-items-center justify-content-center bg-dark rounded-lg">
  <div class="card bg-secondary text-white shadow-lg" style="max-width: 28rem; width: 100%;">
    <div class="card-body p-4">
      <h2 class="card-title text-center text-uppercase fw-bold mb-3">Login</h2>
      <p class="text-center text-muted mb-4">Please enter your login and password!</p>

      <!-- Messaggio di errore o avviso -->
      {{message}}

      <div class="mb-3">
        <label for="email" class="form-label small fw-medium">Email</label>
        <input
          type="email"
          class="form-control bg-dark border-secondary text-white"
          id="email"
          name="email"
          placeholder="Enter your email"
          required
        >
      </div>

      <div class="mb-4">
        <label for="password" class="form-label small fw-medium">Password</label>
        <input
          type="password"
          class="form-control bg-dark border-secondary text-white"
          id="password"
          name="password"
          placeholder="Enter your password"
          required
        > 
       <p onclick="seePassword()">See</p>
      </div>

      <div class="d-flex flex-column text-center mb-4">
        <a href="/forgot" class="text-muted small mb-1">Forgot password?</a>
        <a href="/sign-up" class="text-muted small">Sei il primo? Registrati</a>
      </div>

      <button type="submit" class="btn btn-primary w-100 fw-semibold">
        Login
      </button>
    </div>
  </div>
</form>
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
}
</script>