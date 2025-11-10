<!-- Bootstrap 5 -->

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h3 class="text-center mb-4">Crea Contenuto</h3>

            <!-- Selettore -->
            <div class="btn-group w-100 mb-4" role="group">
                <button type="button" class="btn btn-primary active" data-type="article">Articolo</button>
                <button type="button" class="btn btn-outline-primary" data-type="skill">Skill</button>
                <button type="button" class="btn btn-outline-primary" data-type="profile">Profilo</button>
            </div>

            <!-- ===== FORM ARTICLE ===== -->
            <form action="/admin/article-store" method="POST" id="form-article" class="content-form">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Titolo</label>
                    <input type="text" class="form-control" name="title" placeholder="Titolo dell'articolo" required>
                </div>

                <div class="text-center  border-dark">
                    <img height="300"  src="..." class="rounded mx-auto"  id="img-preview" alt="...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Sottotitolo</label>
                    <input type="text" class="form-control" name="subtitle" placeholder="Sottotitolo">
                </div>

                <div class="mb-3">
                    <label class="form-label">Contenuto</label>
                    <textarea class="form-control" name="content" rows="4" placeholder="Scrivi qui il contenuto..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Immagine</label>
                    <input type="file" id="article-img" class="form-control" name="img" accept="image/*">    
                </div>


                <button type="submit" class="btn btn-success w-100">Salva Articolo</button>
            </form>

            <!-- ===== FORM SKILL ===== -->
            <form action="/admin/skill-store" method="POST" id="form-skill" class="content-form d-none">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Titolo Skill</label>
                    <input type="text" class="form-control" name="title" placeholder="Titolo della skill" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrizione</label>
                    <textarea class="form-control" name="description" rows="4" placeholder="Descrizione breve della skill"></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">Salva Skill</button>
            </form>

            <!-- ===== FORM PROFILE ===== -->
            <form action="/admin/profile-store" method="POST" id="form-profile" class="content-form d-none">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" class="form-control" name="name" placeholder="Nome completo" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tagline</label>
                    <input type="text" class="form-control" name="tagline" placeholder="Es. Full Stack Developer">
                </div>

                <div class="mb-3">
                    <label class="form-label">Messaggio di benvenuto</label>
                    <textarea class="form-control" name="welcome_message" rows="3" placeholder="Scrivi un messaggio di benvenuto..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profilo selezionato?</label>
                    <select class="form-select" name="selected">
                        <option value="0">No</option>
                        <option value="1">Sì</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">Salva Profilo</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('[data-type]');
        const forms = document.querySelectorAll('.content-form');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                // aggiorna stato bottoni
                buttons.forEach(b => b.classList.remove('btn-primary', 'active'));
                buttons.forEach(b => b.classList.add('btn-outline-primary'));
                btn.classList.add('btn-primary', 'active');
                btn.classList.remove('btn-outline-primary');

                // mostra form selezionato
                forms.forEach(f => f.classList.add('d-none'));
                document.getElementById(`form-${btn.dataset.type}`).classList.remove('d-none');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('article-img');
        const preview = document.getElementById('img-preview');

        input.addEventListener('change', e => {
            const file = e.target.files[0];
            if (!file) {
                preview.style.display = 'none';
                preview.src = '';
                return;
            }

            // Verifica che sia un’immagine
            if (!file.type.startsWith('image/')) {
                alert('Seleziona un file immagine valido.');
                input.value = ''; // reset input
                preview.style.display = 'none';
                return;
            }

            // Leggi e mostra l’immagine
            const reader = new FileReader();
            reader.onload = ev => {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    });
</script>