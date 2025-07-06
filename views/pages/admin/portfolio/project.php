<div class="container admin-panel">
    <h1 class="my-4 text-center">Gestione Progetti <?= isset($project->title) ? "- {$project->title}" : '' ?></h1>

    <?php $title = isset($project->title) ? "Modifica $project->title" : "Aggiungi Nuovo Progetto da GitHub"  ?>
    <!-- Form per Aggiungere un Nuovo Progetto -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0"><?= $title ?></h3>
        </div>
        <div class="card-body">
            <form method="POST" onsubmit="verification(event)" action="<?= isset($project->id) ? "/admin/progetti-edit/{$project->id}" : "/admin/progetti" ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titolo</label>
                    <input type="text" class="form-control" value="<?= $project->title ?? '' ?>" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="overview">Descrizione</label>
                    <textarea class="form-control editor" id="editor" name="overview" rows="3"><?= $project->overview ?? '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="link">GitHub Repo</label>
                    <input type="url" class="form-control" value="<?= $project->link ?? '' ?>" id="link" name="link" >
                </div>

                 <div class="form-group">
                    <label for="link">Website</label>
                    <input type="url" class="form-control" value="<?= $project->website ?? '' ?>" id="website" name="website" >
                </div>




                <div class="form-group">
                    <label for="img">Inserisci Nuova Immagine</label>
                    <input
                        <?= isset($project?->img) ? 'required' : '' ?>
                        type="file" accept="image/*" name="img" class="form-control" id="myfile">
                    <div class="row mt-3">
                        <?php if ( isset($project) && !is_null($project?->img) && is_string($project->img) ) : ?>
                            <div class="col-md-6 text-center">
                                <label>Immagine Corrente</label>
                                <img src="<?= $project->img ?>" id="original" class="object-fit-contain border rounded w-100 h-100" alt="Immagine Corrente">
                            </div>
                        <?php endif ?>
                        <div class="col-md-6 text-center">
                            <label>Nuova Immagine</label>
                            <img src="" id="output" class="object-fit-contain border rounded w-100 h-100" alt="Anteprima Immagine" style="display: none;">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-5 w-100">Salva Progetto</button>
            </form>
        </div>
    </div>

    <hr>

    <!-- Sezione Progetti Esistenti -->
    <h3 class="text-center my-4">Progetti Esistenti</h3>
    <div class="container">
        <div class="row">
            <?php if(isset($projects) && count($projects) > 0 ): ?>
            <?php foreach ($projects as $p) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <!-- Immagine del Progetto -->
                        <img src="<?= $p->img ?>" class="object-fit-contain border rounded w-100 h-100" alt="<?= $p->title ?>">

                        <!-- Dettagli del Progetto -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary"><?= $p->title ?></h5>
                            <p class="card-text text-muted"><?= $p->overview ?></p>
                            <a href="<?= $p->link ?>" target="_blank"
                                class="btn btn-outline-primary mt-auto">Vedi Progetto <?= $p->website == 1 ? 'In Cloud' : 'Su GitHub' ?></a>


                            <div class="mt-3 d-flex justify-content-between">
                                <form action="/admin/project-delete/<?= $p->id ?>" method="POST" class="d-inline">
                                    @delete
                                    <button type="submit" onclick="return confirm('Sei sicuro di voler eliminare <?= $p->title ?>?')" class="btn btn-danger btn-sm">Elimina</button>
                                </form>
                                <a href="/admin/progetti-edit/<?= $p->id ?>" class="btn btn-warning btn-sm">Modifica</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Anteprima immagine selezionata
    function anteprimaFile(evt) {
        const file = evt.target.files[0];
        if (file && file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const output = document.getElementById('output');
                output.src = e.target.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            alert("Seleziona un'immagine valida.");
        }
    }


    const verification = (event) => {
        event.preventDefault();
        form = event.target;
        const link = form.querySelector('#link').value.trim();
        const website = form.querySelector('#website').value.trim();
        console.log(link, website);
        if ( link === website) {
            alert('I link devono esser diversi tra loro.'); 
            return false;
        }else{
            form.submit();
            return true;
        }
    };

</script>