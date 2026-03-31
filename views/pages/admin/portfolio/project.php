<?php 

use App\Core\Enum\HttpActionType;

?>
<div class="container admin-panel">
    <h1 class="my-4 text-center">Gestione Progetti {{$project?->title ?? 'Crea nuovo Progetto'}}></h1>

    <?php $title = isset($project->title) ? "Modifica $project->title" : "Aggiungi Nuovo Progetto da GitHub"  ?>
    <!-- Form per Aggiungere un Nuovo Progetto -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">{{$project?->title ?? "Pubblica nuovo progetto"}} </h3>
        </div>
        <div class="card-body">
            <form method="POST" 
            onsubmit="verification(event)" 
            action="{{ route('admin.project.upset', ['id' =>$project?->id ?? 0 ]) }}" 
            enctype="multipart/form-data">

                @csrf
                {{ HttpActionType::PATCH->value}}
                {{ HttpActionType::POST->value}}
                <div class="form-group">
                    <label for="title">Titolo</label>
                    <input type="text" class="form-control" value="<?= $project->title ?? '' ?>" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="overview">Overview - sottotitolo</label>
                    <textarea class="form-control editor"  id="overview" name="overview" rows="3">{{$project?->overview ?? ''}}</textarea>
                </div>

                <div class="form-group">
                    <label for="description">Descrizione</label>
                    <textarea class="form-control editor" id="description" name="description" rows="3">{{$project?->description ?? ''}} </textarea>
                </div>
                <div class="form-group">
                    <label for="link">GitHub Repo</label>
                    <input type="url" class="form-control" value="{{$project->link ?? ''}}" id="link" name="link" >
                </div>

                 <div class="form-group">
                    <label for="link">Website</label>
                    <input type="url" class="form-control" value="<?= $project->website ?? '' ?>" id="website" name="website" >
                </div>




                <div class="form-group">
                    <label for="img">Inserisci Nuova Immagine</label>
                    <input
                    type="file"
                    accept="image/*" 
                    name="img" 
                    class="form-control" 
                    id="myfile">
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
                <div class="form-group mt-3">
                    <label for="status">Stato progetto</label>
                    <select class="form-control" id="status" name="status">
                        <?php $currentStatus = $project->status ?? 'in_progress'; ?>
                        <?php foreach (\App\Core\Enum\ProjectStatus::cases() as $status) : ?>
                            <option value="<?= $status->value ?>" <?= $currentStatus === $status->value ? 'selected' : '' ?>><?= $status->label() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="started_at">Data inizio</label>
                            <input type="date" class="form-control" id="started_at" name="started_at" value="<?= $project->started_at ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ended_at">Data fine</label>
                            <input type="date" class="form-control" id="ended_at" name="ended_at" value="<?= $project->ended_at ?? '' ?>">
                            <small class="form-text text-muted">Lascia vuoto se il progetto è ancora in corso</small>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="gallery">Galleria immagini</label>
                    <input type="file" class="form-control" id="gallery" name="gallery[]" multiple accept="image/*">
                    <small class="form-text text-muted">Puoi selezionare più immagini</small>
                    <?php if (!empty($gallery)) : ?>
                        <div class="row mt-2">
                            <?php foreach ($gallery as $media) : ?>
                                <div class="col-3 mb-2 position-relative">
                                    <img src="<?= $media->path ?>" class="img-fluid rounded border" alt="Gallery">
                                    <form action="/admin/project-media/<?= $media->id ?>" method="POST" class="position-absolute" style="top: 2px; right: 17px;">
                                        @csrf
                                        @delete
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Eliminare questa immagine?')" style="padding: 0 5px; font-size: 0.7rem;">&times;</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($project->is_active ?? true) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">Visibile nel portfolio pubblico</label>
                </div>
                <button type="submit" class="btn btn-success mt-5 w-100">Salva Progetto</button>
            </form>
        </div>
    </div>

    <hr>

    <!-- Sezione Progetti Esistenti -->
    <h3 class="text-center my-4">Progetti Esistenti</h3>
    <div class="container">
        <div class="row" id="sortable-list" data-entity="project">
            <?php if(isset($projects) && count($projects) > 0 ): ?>
            <?php foreach ($projects as $p) : ?>
                <div class="col-md-4 mb-4" data-id="<?= $p->id ?>">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light d-flex align-items-center gap-2 py-1 px-2">
                            <span class="drag-handle text-muted" style="cursor: grab;"><i class="fa fa-bars"></i></span>
                            <small class="text-muted">Trascina per riordinare</small>
                        </div>
                        <!-- Immagine del Progetto -->
                        <img src="<?= $p->img ?>" class="object-fit-contain border rounded w-100 h-100" alt="<?= $p->title ?>">

                        <!-- Dettagli del Progetto -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary"><?= $p->title ?></h5>
                            <p class="card-text text-muted">{{{$p->overview }}}</p>
                            <a href="<?= $p->link ?>" target="_blank"
                                class="btn btn-outline-primary mt-auto">Vedi Progetto <?= $p->website == 1 ? 'In Cloud' : 'Su GitHub' ?></a>


                            <div class="mt-3 d-flex justify-content-between">
                                <form action="/admin/project-delete/<?= $p->id ?>" method="POST" class="d-inline">
                                    @csrf
                                    @delete
                                    <button type="submit" onclick="return confirm('Sei sicuro di voler eliminare <?= $p->title ?>?')" class="btn btn-danger btn-sm">Elimina</button>
                                </form>
                                <a href="{{route('admin.project.edit', ['id'=>$p->id])}}"<?= $p->id ?>" class="btn btn-warning btn-sm">Modifica</a>
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

    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('myfile');
        if (fileInput) {
            fileInput.addEventListener('change', anteprimaFile);
        }
    });


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
            if (typeof window.syncEditors === 'function') {
                window.syncEditors(form);
            }
            // Use form.submit() to avoid re-triggering the onsubmit handler
            form.submit();
            return true;
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        initSortable('sortable-list');
    });
</script>
