<!-- Admin Panel -->
<div class="container admin-panel">
    <h1 class="my-4">Project Management <?= isset($project->title) ? $project->title : '' ?></h1>

    <!-- Form to Add Project -->
    <div class="mb-4">
        <h3>Add New Project Form GitHub</h3>
        <form method="POST" action="<?= isset($project->id) ? "/admin/progetti-edit/$project->id" : "/admin/progetti" ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" value="<?= isset($project->title) ? $project->title : '' ?>" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="overview">Overview</label>
                <textarea class="form-control" id="overview" name="overview" rows="3"><?= isset($project->overview) ? $project->overview : '' ?></textarea>
            </div>
            <div class="form-group">
                <label for="link">Link</label>
                <input type="url" class="form-control" value="<?= isset($project->link) ? $project->link : '' ?>" id="link" name="link" required>
            </div>
            <div class="form-group">
                <label for="img">Img</label>
                <input type="file" accept="image/*" name="img" class="form-control" id="myfile" required>
                <div class="mt-3 d-flex flex-row">
                    <?php if (isset($project->img)) : ?>
                        <div class="text-center">
                            <label>Immagine Originale</label><br><br>
                            <img src="<?= $project->img ?>" id="original" class="img-thumbnail" alt="Immagine Originale">
                        </div>
                    <?php endif ?>

                    <div class="text-center mr-5 mb-5">
                        <label>Immagine Selezionata</label><br><br>
                        <img src="" id="output" class="img-thumbnail" alt="Immagine selezionata">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <hr>
    <div>
        <h3>Existing Projects</h3>
        <div class="container">
            <div class="row">
                <?php foreach ($projects as $project) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm rounded">
                            <!-- Immagine del Progetto -->
                            <img src="<?= $project->img ?>" class="card-img-top img-fluid fix-object" alt="<?= $project->title ?>">

                            <!-- Card -->
                            <div class="card-body">
                                <h5 class="card-title"><?= $project->title ?></h5>
                                <p class="card-text"><?= $project->overview ?></p>
                                <a href="<?= $project->link ?>" target="_blank" class="btn btn-primary">View Project</a>

                                <div class="mt-3 d-flex justify-content-between">
                                    <form action="/admin/project-delete/<?= $project->id ?>" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you want to eliminate <?= $project->title ?>?')" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <?= $project->id ?>
                                    <a href="/admin/progetti-edit/<?= $project->id ?>" class="btn btn-warning btn-sm">Edit</a>
                                </div>
                            </div>
                            <!-- End Card -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function anteprimaFile(evt) {
        var file = evt.target.files;
        var f = file[0];
        if (!f.type.match('image.*')) {
            alert("Attenzione: il file selezionato deve essere un'immagine");
            return false;
        }

        var reader = new FileReader();

        reader.onload = function(e) {
            var output = document.getElementById('output');
            output.src = e.target.result;
            output.style.display = 'block'; // Assicurati che l'immagine sia visibile
        };

        reader.readAsDataURL(f);
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('myfile').addEventListener('change', anteprimaFile, false);
    });
</script>
