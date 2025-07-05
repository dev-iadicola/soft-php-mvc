<style>
    .table img {
        max-width: 100px;
        height: auto;
        border-radius: 5px;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .table .actions {
        display: flex;
        gap: 10px;
    }
</style>
<!-- Admin Home Panel -->
<div class="container mt-5 admin-panel">

    <div class="row justify-content-center d-flex flex-row">
        <!-- Colonna per i moduli -->
        <div id="accordion" class="col-lg-12 col-md-12 mb-4 mb-md-0">
            <!-- Forms -->
            @include('components.admin.article-form')
            @include('components.admin.profile-form')
            @include('components.admin.skill-form')
        </div>

        <!-- Colonna per le visualizzazioni -->
        <div class="admin-panel col-lg-12 col-md-12">
    <div class="p-4">
        <?php if (isset($articles)) : ?>
            <!-- Articles List View -->
            <div class="mb-3">
                <h3 onclick="toggleFunction('#articles-cards')" style="cursor: pointer; font-weight: bold;">Articles</h3>
                <div class="row mt-3" id="articles-cards">
                    <?php foreach ($articles as $article) : ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="p-3 border border-light bg-light rounded">
                                <?php if (empty($article->img)) : ?>
                                    <img src="<?= $article->img ?>" class="img-fluid object-fit-contain border rounded w-100 h-100" alt="Image for <?= $article->title ?>" style="height: 100px; object-fit: cover;">
                                <?php endif ?>
                                <h5><?= $article->title ?></h5>
                                <p class="text-muted"><?= $article->subtitle ?></p>
                                <p><small><strong>Date:</strong> <?= $article->created_at ?></small></p>
                               
                                <div class="mt-2">
                                    <a href="/admin/home/<?= $article->id ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form action="/admin/home-delete/<?= $article->id ?>" method="POST" style="display: inline;">
                                        @delete
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete <?= $article->title ?>?')" class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>

        <!-- Profiles List View -->
        <?php if (isset($profiles)) : ?>
            <div class="mb-3">
                <h3 onclick="toggleFunction('#profiles-cards')" style="cursor: pointer; font-weight: bold;">Profiles</h3>
                <div class="row mt-3" id="profiles-cards">
                    <?php foreach ($profiles as $profile) : ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="p-3 border border-light bg-light rounded">
                                <h5><?= $profile->name ?></h5>
                                <p class="text-muted"><?= $profile->tagline ?></p>
                                <p><small><?= $profile->welcome_message ?></small></p>
                                <p><small><strong>Selected:</strong> <?= $profile->selected ? 'Yes' : 'No' ?></small></p>
                                <div class="mt-2">
                                    <a href="/admin/profile/<?= $profile->id ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form action="/admin/profile-delete/<?= $profile->id ?>" method="POST" style="display: inline;">
                                        @delete
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete <?= $profile->name ?>?')" class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>

        <!-- Skills List View -->
        <?php if (isset($skills)) : ?>
            <div class="mb-3">
                <h3 onclick="toggleFunction('#skill-cards')" style="cursor: pointer; font-weight: bold;">Skills</h3>
                <div class="row mt-3" id="skill-cards">
                    <?php foreach ($skills as $skill) : ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="p-3 border border-light bg-light rounded">
                                <h5><?= $skill->title ?></h5>
                                <p class="text-muted"><?= substr($skill->description, 0, 20) ?>...</p>
                                <div class="mt-2">
                                    <a href="/admin/skill/<?= $skill->id ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                    <form action="/admin/skill-delete/<?= $skill->id ?>" method="POST" style="display: inline;">
                                        @delete
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete <?= $skill->title ?>?')" class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

    </div>
</div>

<script>
    function toggleFunction(elem) {
        $(elem).slideToggle();
    }
</script>

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
