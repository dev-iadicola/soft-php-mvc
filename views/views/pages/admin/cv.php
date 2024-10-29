<style>
    .card {
        width: 100%;
        max-width: 1000px;
        /* Imposta una larghezza massima */
        margin: 0 auto;
        /* Centra la card orizzontalmente */
    }

    .card-body {
        padding: 20px;
        /* Aumenta il padding per un migliore layout */
    }

    iframe {
        border: none;
        /* Rimuove il bordo dell'iframe */
    }
</style>
<!-- Admin Panel -->
<div class="container admin-panel">
    <h1 class="my-4">CV Management</h1>

    <!-- Form to Add Project -->
    <div class="mb-4">
        <h3>Add Curriculum</h3>
        <div class="mb-4">

            <?php
            $url = isset($cv->id) ? "/admin/cv-update/{$cv->id}" : '/admin/cv';

            ?>

            <form method="POST" /admin/portfolio-update/{id} action="<?= $url ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" 
                    id="title" name="title" value="<?= isset($cv->title) ? $cv->title : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="link">File</label>
                    <input required type="file" class="form-control" id="link" name="img"
                     value="?>">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        </div>

        <hr>
        <div>
            <h3>CV</h3>
            <div class="container">
                <div class=" d-flex justify-content-center flex-column wrap">
                    <?php foreach ($curricula as $cv) : ?>
                        <div class="col-md-12 mb-4">
                            <div class="card shadow-sm rounded" style="min-height: 500px; width: 100%; max-width: 1000px;">
                                <!-- Card Body -->
                                <div class="card-body">
                                    <h5 class="card-title"><?= $cv->title ?></h5>
                                    <!-- Modifica le dimensioni dell'iframe -->
                                    <iframe src="<?= $cv->img ?>" title="<?= $cv->title ?>" width="100%" height="500" style="border: none;"></iframe>
                                    <a href="<?= $cv->img ?>" target="_blank" class="btn btn-primary mt-2">View CV</a>


                                    <div class="mt-3 d-flex justify-content-between">
                                        <form action="/admin/cv-delete/<?= $cv->id ?>" method="POST">
                                            @delete
                                            <button type="submit" onclick="return confirm('Are you sure you want to eliminate <?= $cv->title ?> ?')" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                        <a href="/admin/cv-edit/<?= $cv->id ?>" class="btn btn-warning btn-sm">Edit</a>
                                    </div>
                                </div>
                                <!-- End Card -->
                            </div>
                        </div>
                    <?php endforeach; ?>
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