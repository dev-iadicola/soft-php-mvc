<?php
$urlProfile = isset($profile->id) ? "/admin/profile/{$profile->id}" : '/admin/profile';
?>
<div class="card">
    <div class="card-header" id="headingSkill">
        <h5 class="mb-0">
            <button class="btn btn-light w-100 text-left px-4" type="button" data-toggle="collapse" data-target="#collapseProfile" aria-expanded="false" aria-controls="collapseProfile">
                <h2 class="my-4">Welcome Message - Profile Form</h2>
            </button>
        </h5>
    </div>
    <div id="collapseProfile" class="collapse <?= isset($profile->id) ? 'show' : '' ?>" aria-labelledby="headingSkill" data-parent="#accordion">
        <div class="card-body shadow p-3 border border-dark m-2 rounded">
            <form method="POST" action="<?= $urlProfile ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="name" value="<?= isset($profile->name) ? $profile->name : '' ?>" required>
                </div>
                <?php if (isset($profile->id)) : ?>
                    <div class="form-group form-check text-center">
                        <input type="checkbox" name="selected" <?= ($profile->selected == 1) ? 'checked' : '' ?> class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">
                            <?= ($profile->selected == 1) ? 'Deselziona Per togliere dalla' : 'Seleziona per Aggiungere nella' ?> home page
                        </label>
                    </div>
                <?php endif ?>
                <div class="form-group">
                    <label for="subtitle">Subtitle</label>
                    <input type="text" class="form-control" id="subtitle" name="tagline" value="<?= isset($profile->tagline) ? $profile->tagline : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="overview">Description</label>
                    <textarea class="form-control" id="overview" name="welcome_message" rows="3"><?= isset($profile->welcome_message) ? $profile->welcome_message : '' ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Welcome Profile Message</button>
            </form>
        </div>
    </div>
</div>
