<!-- Admin Panel -->
<div class="container admin-panel">
    <h1 class="my-4">Portfolio Management</h1>

    <!-- Form to Add portfolio -->
    <div class="mb-4">
        <h3>Nuova Esperienza</h3>
        <?php
            $url = isset($pfolio->id) ? "/admin/portfolio-update/{$pfolio->id}" : '/admin/portfolio';
        ?>

        <form method="POST" action="<?= $url ?>">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= isset($pfolio->title) ? $pfolio->title : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="overview">Overview</label>
                <textarea class="form-control" id="overview" name="overview" rows="3"><?= isset($pfolio->overview) ? $pfolio->overview : '' ?></textarea>
            </div>
            <div class="form-group">
                <label for="link">Link</label>
                <input type="url" class="form-control" id="link" name="link" value="<?= isset($pfolio->link) ? $pfolio->link : '' ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Portfolio Table -->
    <div>
        <h3>Existing Projects</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Overview</th>
                    <th>Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="projectsTableBody">
                <?php foreach ($portfolio as $project): ?>
                    <tr>
                        <td><?= $project->id ?></td>
                        <td><?= $project->title ?></td>
                        <td><?= $project->overview ?></td>
                        <td>
                            <?php if ($project->link !== ''): ?>
                                <a href="<?= $project->link ?>" target="_blank" class="btn btn-primary">
                                    Apri il link per <?= $project->title ?>
                                </a>
                            <?php endif ?>
                        </td>
                        <td class="col-ms-3 p-2 gap-3">
                            <form action="/admin/portfolio-delete/<?= $project->id ?>" method="POST">
                                @delete
                                <button onclick="return confirm('Are you sure you want to eliminate <?= $project->title ?>')" class="btn btn-danger">Delete</button>
                            </form>
                            <a href="/admin/portfolio-edit/<?= $project->id ?>" class="btn btn-warning my-3">Edit</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
