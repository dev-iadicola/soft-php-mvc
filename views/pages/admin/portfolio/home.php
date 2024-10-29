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

    <div class="row justify-content-center">
        <!-- Colonna per i moduli -->
        <div id="accordion" class="col-lg-7 col-md-12 mb-4 mb-md-0">
            <!-- Forms -->
            @include('components.admin.article-form')
            @include('components.admin.profile-form')
            @include('components.admin.skill-form')
        </div>

        <!-- Colonna per le visualizzazioni -->
        <div class="admin-panel col-lg-6 col-md-12">
            <div class="p-5">
                <?php if (isset($articles)) : ?>
                    <!-- Articles List View -->
                    <div class="card mb-4">
                        <div class="card-header" onclick="toggleFunction('#articles-cards')">
                            <h3>Articles</h3>
                        </div>
                        <div class="row mt-4" id="articles-cards">
                            <?php foreach ($articles as $article) : ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card p-2">
                                        <?php if (!empty($article->img)) : ?>
                                            <img src="<?= $article->img ?>" class="card-img-top img-fluid" alt="Image for <?= $article->title ?>" style="height: 120px; object-fit: cover;">
                                        <?php endif ?>
                                        <div class="card-body p-2">
                                            <h6 class="card-title fs-6"><?= $article->title ?></h6>
                                            <h6 class="card-subtitle mb-1 text-muted fs-7"><?= $article->subtitle ?></h6>
                                            <p class="card-text fs-7 mb-1">
                                                <strong>Link:</strong>
                                                <?php if (!empty($article->link)) : ?>
                                                    <a href="<?= $article->link ?>" target="_blank" class="btn btn-primary btn-sm">Open Link</a>
                                                <?php endif ?>
                                            </p>
                                            <p class="card-text fs-7 mb-1">
                                                <strong>Date:</strong> <?= $article->created_at ?>
                                            </p>
                                            <div class="d-flex justify-content-between">
                                                <form action="/admin/home-delete/<?= $article->id ?>" method="POST">
                                                   @delete
                                                    <button type="submit" onclick="return confirm('Are you sure you want to eliminate <?= $article->title ?>?')" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                                <a href="/admin/home/<?= $article->id ?>" class="btn btn-warning btn-sm">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endif ?>

                <!-- Profiles List View -->
                <?php if (isset($profiles)) : ?>
                    <div class="card mb-4">
                        <div class="card-header" onclick="toggleFunction('#profiles-cards')">
                            <h3>Profiles</h3>
                        </div>
                        <div class="row mt-4" id="profiles-cards">
                            <?php foreach ($profiles as $profile) : ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card p-2">
                                        <div class="card-body p-2">
                                            <h6 class="card-title fs-6"><?= $profile->name ?></h6>
                                            <h6 class="card-subtitle mb-1 text-muted fs-7"><?= $profile->tagline ?></h6>
                                            <p class="card-text fs-7 mb-1"><?= $profile->welcome_message ?></p>
                                            <p class="card-text fs-7 mb-1"><strong>Selected:</strong> <?= $profile->selected ? 'Yes' : 'No' ?></p>
                                            <div class="d-flex justify-content-between">
                                                <form action="/admin/profile-delete/<?= $profile->id ?>" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to eliminate <?= $profile->name ?>?')" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                                <a href="/admin/profile/<?= $profile->id ?>" class="btn btn-warning btn-sm">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endif ?>

                <?php if (isset($skills)) : ?>
                    <div class="card mb-4">
                        <div class="card-header" onclick="toggleFunction('#skill-cards')">
                            <h3>Skills</h3>
                        </div>
                        <div class="row mt-4" id="skill-cards">
                            <?php foreach ($skills as $skill) : ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card p-2">
                                        <div class="card-body p-2">
                                            <h6 class="card-title fs-6"><?= $skill->title ?></h6>
                                            <h6 class="card-subtitle mb-1 text-muted fs-7"><?= substr($skill->description, 0, 20) ?></h6>
                                            <div class="d-flex justify-content-between mt-2">
                                                <form action="/admin/skill-delete/<?= $skill->id ?>" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to eliminate <?= $skill->title ?>?')" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                                <a href="/admin/skill/<?= $skill->id ?>" class="btn btn-warning btn-sm">Edit</a>
                                            </div>
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
