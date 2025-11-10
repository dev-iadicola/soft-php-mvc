<!-- Articles List View -->
<?php if (isset($articles)) : ?>
    <div class="mb-3">
        <h3 onclick="toggleFunction('#articles-cards')" style="cursor: pointer; font-weight: bold;">Articles</h3>
        <div class="row mt-3" id="articles-cards">
            <?php foreach ($articles as $article) : ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="p-3 border border-light bg-light rounded">
                        <img src="<?= $article->img ?: assets('img/default.jpg') ?>"
                             class="img-fluid object-fit-contain border rounded w-100 mb-2"
                             style="height: 150px; object-fit: cover;">
                        <h5>{{$article->title}} ?></h5>
                        <p class="text-muted">{{$article->subtitle}}</p>
                        <small><strong>Creato il:</strong> {{$article->created_at}} </small>
                        <div class="mt-2 actions">
                            <a href="/admin/article/create" class="btn btn-outline-success btn-sm">Create</a>
                            <button class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editArticleModal-<?= $article->id ?>">Edit</button>
                           
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="editArticleModal-<?= $article->id ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form action="/admin/article/update/<?= $article->id ?>" method="POST" enctype="multipart/form-data">
                                @csrf
                                @patch

                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">Modifica Articolo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Titolo</label>
                                        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($article->title) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Sottotitolo</label>
                                        <input type="text" class="form-control" name="subtitle" value="<?= htmlspecialchars($article->subtitle) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Immagine</label>
                                        <input type="file" class="form-control" name="img">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                                    <button type="submit" class="btn btn-primary">Salva modifiche</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>
