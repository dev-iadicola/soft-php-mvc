<?php
if (isset($FLASH['error']) && !empty($FLASH['error'])): ?>
    <div class="alert alert-danger text-center mt-5 mx-5 fs-3">
        <?= htmlspecialchars($FLASH['error']) ?>
    </div>
<?php endif; ?>

<?php
if (isset($FLASH['success']) && !empty($FLASH['success'])): ?>
    <div class="alert alert-success text-center mt-5 mx-5 fs-3">
        <?= htmlspecialchars($FLASH['success']) ?>
    </div>
<?php endif; ?>