<?php if (!is_null(session()->get('error'))): ?>
    <div class="admin-alert admin-alert--error">
        <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
        <span>{{{flashMessage('error')}}}</span>
    </div>
<?php endif; ?>

<?php if (!is_null(session()->get('success'))): ?>
    <div class="admin-alert admin-alert--success">
        <i data-lucide="check-circle-2" style="width:18px;height:18px;flex-shrink:0;"></i>
        <span>{{{ flashMessage('success') }}}</span>
    </div>
<?php endif; ?>

<?php if (!is_null(session()->get('warning'))): ?>
    <div class="admin-alert admin-alert--warning">
        <i data-lucide="alert-triangle" style="width:18px;height:18px;flex-shrink:0;"></i>
        <span>{{{flashMessage('warning')}}}</span>
    </div>
<?php endif; ?>

<script>
// Auto-dismiss flash messages as toasts
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.admin-alert').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(function() { el.remove(); }, 300);
        }, 5000);
    });
});
</script>
