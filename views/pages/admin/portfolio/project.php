<div class="container admin-panel">
    <h1 class="my-4 text-center">Gestione Progetti <?= isset($project->title) ? "- {$project->title}" : '' ?></h1>

    <!-- Form per Aggiungere un Nuovo Progetto -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Aggiungi Nuovo Progetto da GitHub</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= isset($project->id) ? "/admin/progetti-edit/{$project->id}" : "/admin/progetti" ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titolo</label>
                    <input type="text" class="form-control" value="<?= isset($project->title) ? $project->title : '' ?>" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="overview">Descrizione</label>
                    <textarea class="form-control editor" id="overview" name="overview" rows="3"><?= isset($project->overview) ? $project->overview : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="link">Link</label>
                    <input type="url" class="form-control" value="<?= isset($project->link) ? $project->link : '' ?>" id="link" name="link" required>
                </div>

               


                <div class="form-group">
                    <label for="img">Immagine</label>
                    <input
                        <?= isset($project->img) ? '' : 'required' ?>
                        type="file" accept="image/*" name="img" class="form-control" id="myfile">
                    <div class="row mt-3">
                        <?php if (isset($project->img)) : ?>
                            <div class="col-md-6 text-center">
                                <label>Immagine Corrente</label>
                                <img src="<?= $project->img ?>" id="original" class="img-thumbnail" alt="Immagine Corrente">
                            </div>
                        <?php endif ?>
                        <div class="col-md-6 text-center">
                            <label>Nuova Immagine</label>
                            <img src="" id="output" class="img-thumbnail" alt="Anteprima Immagine" style="display: none;">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-3">Salva Progetto</button>
            </form>
        </div>
    </div>

    <hr>

    <!-- Sezione Progetti Esistenti -->
    <h3 class="text-center my-4">Progetti Esistenti</h3>
    <div class="container">
        <div class="row">
            <?php foreach ($projects as $project) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <!-- Immagine del Progetto -->
                        <img src="<?= $project->img ?>" class="card-img-top img-fluid" alt="<?= $project->title ?>">

                        <!-- Dettagli del Progetto -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary"><?= $project->title ?></h5>
                            <p class="card-text text-muted"><?= $project->overview ?></p>
                            <a href="<?= $project->link ?>" target="_blank"
                                class="btn btn-outline-primary mt-auto">Vedi Progetto <?= $project->deploy == 1 ? 'In Cloud' : 'Su GitHub' ?></a>


                            <div class="mt-3 d-flex justify-content-between">
                                <form action="/admin/project-delete/<?= $project->id ?>" method="POST" class="d-inline">
                                    @delete
                                    <button type="submit" onclick="return confirm('Sei sicuro di voler eliminare <?= $project->title ?>?')" class="btn btn-danger btn-sm">Elimina</button>
                                </form>
                                <a href="/admin/progetti-edit/<?= $project->id ?>" class="btn btn-warning btn-sm">Modifica</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('myfile').addEventListener('change', anteprimaFile, false);
    });
</script>