<?php
$urlArticle = isset($article->id) ? "/admin/home/{$article->id}" : '/admin/home';
?>
<div class="card">
    <div class="card-header" id="headingArticle">
        <h5 class="mb-0">
            <button class="btn btn-light w-100 text-left px-4" type="button" data-toggle="collapse" data-target="#collapseArticle" aria-expanded="true" aria-controls="collapseArticle">
                <h2 class="my-4">Articles Form</h2>
            </button>
        </h5>
    </div>
    <div id="collapseArticle" class="collapse <?= isset($article->id) ? 'show' : '' ?>" aria-labelledby="headingArticle" data-parent="#accordion">
        <div class="card-body shadow p-3 border border-dark m-2 rounded">
            <form method="POST" action="<?= $urlArticle ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= isset($article->title) ? $article->title : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="subtitle">Subtitle</label>
                    <input type="text" class="form-control" id="subtitle" name="subtitle" value="<?= isset($article->subtitle) ? $article->subtitle : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="overview">Description</label>
                    <textarea class="form-control" id="overview" name="overview" rows="3"><?= isset($article->overview) ? $article->overview : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="link">Link</label>
                    <input type="url" class="form-control" id="link" name="link" value="<?= isset($article->link) ? $article->link : '' ?>">
                </div>
                <div class="form-group">
                    <label for="img">Img</label>
                    <input type="file" accept="image/*" name="img" class="form-control-file" id="myfile">
                </div>
                <div class="mt-3 d-flex flex-row">
                    <?php if (isset($article->img)) : ?>
                        <div class="text-center">
                            <label>Immagine Originale</label><br><br>
                            <img src="<?= $article->img ?>" id="original" class="img-thumbnail" alt="Immagine Originale">
                        </div>
                    <?php endif ?>
                    <div class="text-center mr-5 mb-5">
                        <label>Immagine Selezionata</label><br><br>
                        <img src="" id="output" class="img-thumbnail" alt="Immagine selezionata">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Article</button>
            </form>
        </div>
    </div>
</div>
