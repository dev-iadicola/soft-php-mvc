<form action="/forgot" method="POST">
  <div class="container py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="w-100" style="max-width: 500px;">
      <div class="card bg-dark text-white shadow">
        <div class="card-body text-center">
          <h2 class="card-title text-uppercase mb-3">Reset Password</h2>
          <p class="text-secondary mb-3">Inserisci la tua Email</p>

          <div class="mb-3 text-start">
            <label for="email" class="form-label">Email</label>
            <input 
              type="email" 
              name="email" 
              id="email" 
              class="form-control bg-dark text-white border-secondary"
              required
            >
          </div>

          <p class="text-sm mb-4">
            <a href="/login" class="text-secondary text-decoration-none">Hai la password? Accedi</a>
          </p>

          <button type="submit" class="btn btn-light w-100 fw-bold">Reset Password</button>
        </div>
      </div>
    </div>
  </div>
</form>
<script>
