<?php
$urlSkill = isset($skill->id) ? "/admin/skill/{$skill->id}" : '/admin/skill';
?>


<div class="card">
    <div class="card-header" id="headingSkill">
        <h5 class="mb-0">
            <button class="btn btn-light w-100 text-left px-4" type="button" data-toggle="collapse" data-target="#collapseSkill" aria-expanded="false" aria-controls="collapseSkill">
                <h2 class="">Skills Form</h2>
            </button>
        </h5>
    </div>
    <div id="collapseSkill" class="collapse <?= isset($skill->id) ? 'show' : '' ?>" aria-labelledby="headingSkill" data-parent="#accordion">
        <div class="card-body shadow p-3 border border-dark m-2 rounded">
            <form method="POST" action="<?= $urlSkill ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title"  name="title" value="<?= $skill->title ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="subtitle">Description</label>
                    <textarea class="form-control editor" id="subtitle" name="description" rows="3"><?=  $skill->description ?? '' ?></textarea>

                </div>
                <button type="submit" class="btn btn-primary mt-2"><?php echo isset($skill)? 'Edit':'Update' ?></button>
            </form>
        </div>
    </div>
</div>
