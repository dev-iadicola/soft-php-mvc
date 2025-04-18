<section class="container mt-5">
    <div class="bg-white rounded">
    <h2 class="text-center display-4 mb-4">Contattami</h2>
    </div>

    <form action="/contatti" method="post" class="bg-dark p-4 p-md-5 rounded shadow-lg text-light">
        <div class="form-group">
            <label for="nome">Nome e Cognome <span class="text-danger">*</span></label>
            <input type="text" id="nome" name="nome" class="form-control" placeholder="Mario" maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" class="form-control" placeholder="mybest@mail.it" maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="typologie">Chi sei? <span class="text-danger">*</span></label>
            <select name="typologie" id="typologie" class="form-control" required>
                <option disabled selected value="">Seleziona</option>
                <option value="Privato">Privato</option>
                <option value="Azienda">Azienda</option>
                <option value="Developer">Developer</option>
            </select>
        </div>

        <div class="form-group">
            <label for="messaggio">Messaggio <span class="text-danger">*</span></label>
            <textarea id="messaggio" name="messaggio" class="form-control" rows="5" minlength="20" required></textarea>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="flexCheckDefault" required>
            <label class="form-check-label" for="flexCheckDefault">
                Accetta <a href="/laws" class="text-info">Termini e Condizioni</a> <span class="text-danger">*</span>
            </label>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow">Invia</button>
        </div>
    </form>
</section>
