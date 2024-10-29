<!-- Admin Panel -->
<div class="container admin-panel">
    <h1 class="my-4">Laws Management</h1>

    <!-- Form to Add Project -->
    <div class="mb-4">
        <h3>Add Law</h3>
        <div class="mb-4">

            <?php
            $url = isset($law->id) ? "/admin/law-edit/{$law->id}" : '/admin/laws';
            ?>

            <form method="POST" action="<?= $url ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= isset($law->title) ? htmlspecialchars($law->title) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="link">Text Law</label>
                    <textarea class="form-control" id="overview" name="testo" rows="3" required><?= isset($law->testo) ? $law->testo : '' ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        </div>

        <hr>

        <h3>Laws</h3>
        <div class="container">
            <div class="row">
                <?php foreach ($laws as $law) : ?>
                    <div class="col-md-12 mb-4">
                        <div class="card shadow rounded" style="max-width: 1000px; margin: auto;">

                            <!-- Card Header with options -->
                            <div class="card-header d-flex justify-content-between">
                                <form action="/admin/law-delete/<?= htmlspecialchars($law->id, ENT_QUOTES, 'UTF-8') ?>" method="POST">
                                    @delete
                                    <button type="submit" onclick="return confirm('Are you sure you want to eliminate <?= htmlspecialchars($law->title, ENT_QUOTES, 'UTF-8') ?>?')" class="btn btn-danger">Delete</button>
                                </form>
                                <a href="/admin/law-edit/<?= htmlspecialchars($law->id, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-warning">Edit</a>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($law->title, ENT_QUOTES, 'UTF-8') ?></h5>
                                <p><?= $law->testo ?></p>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
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