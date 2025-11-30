<?php
if ( !empty(flashMessage('error'))): ?>
    <div class="alert alert-danger text-center mt-5 mx-5 fs-3">
        <?= flashMessage('error') ?> 
    </div>
<?php endif; ?>

<?php
if ( !empty(flashMessage('success'))): ?>
    <div class="alert alert-success text-center mt-5 mx-5 fs-3">
        <?= flashMessage('success') ?>
    </div>
<?php endif; ?>

<?php
if (!empty(flashMessage('warning'))): ?>
    <div class="alert alert-warning text-center mt-5 mx-5 fs-3">
        <?= flashMessage('warning') ?>
    </div>
<?php endif; ?>