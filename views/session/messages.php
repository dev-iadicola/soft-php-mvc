<?php


if (!is_null(session()->get('error'))): ?>
    <div class="alert alert-danger text-center mt-5 mx-5 fs-3">
        {{flashMessage('error')}}
    </div>
<?php endif; ?>

<?php
if (!is_null(session()->get('success'))): ?>
    <div class="alert alert-success text-center mt-5 mx-5 fs-3">
        {{ flashMessage('success') }}
    </div>
<?php endif; ?>

<?php
if (!is_null(session()->get('warning'))): ?>
    <div class="alert alert-warning text-center mt-5 mx-5 fs-3">
        {{ flashMessage('warning') }}
    </div>
<?php endif; ?>