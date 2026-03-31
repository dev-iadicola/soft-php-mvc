<?php
try {
    $unreadNotifications = \App\Services\NotificationService::getUnread(10);
    $unreadCount = \App\Services\NotificationService::countUnread();
} catch (\Throwable) {
    $unreadNotifications = [];
    $unreadCount = 0;
}
?>

<div id="notification-bell" class="notification-bell">
    <button id="notification-toggle" class="notification-bell__btn" title="Notifiche">
        <i class="fa fa-bell"></i>
        <span class="notification-bell__label">Notifiche</span>
        <span id="notification-badge" class="notification-bell__badge <?= $unreadCount > 0 ? 'notification-bell__badge--visible' : '' ?>">
            <?= $unreadCount ?>
        </span>
    </button>

    <div id="notification-dropdown" class="notification-bell__dropdown">
        <div class="notification-bell__header">
            <strong>Notifiche</strong>
            <?php if ($unreadCount > 0) : ?>
                <form method="POST" action="/admin/notifications/read-all" style="display:inline;">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="notification-bell__read-all">Segna tutte come lette</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="notification-bell__list">
            <?php if ($unreadCount === 0) : ?>
                <div class="notification-bell__empty">Nessuna nuova notifica</div>
            <?php else : ?>
                <?php foreach ($unreadNotifications as $notification) : ?>
                    <form method="POST" action="/admin/notifications/<?= $notification->id ?>/read" class="notification-bell__item-form">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="notification-bell__item">
                            <span class="notification-bell__icon">
                                <?php if ($notification->type === 'new_contact') : ?>
                                    <i class="fa fa-envelope"></i>
                                <?php else : ?>
                                    <i class="fa fa-info-circle"></i>
                                <?php endif; ?>
                            </span>
                            <span class="notification-bell__content">
                                <span class="notification-bell__title"><?= htmlspecialchars($notification->title) ?></span>
                                <?php if ($notification->message) : ?>
                                    <span class="notification-bell__message"><?= htmlspecialchars($notification->message) ?></span>
                                <?php endif; ?>
                                <span class="notification-bell__time"><?= date('d/m/Y H:i', strtotime($notification->created_at)) ?></span>
                            </span>
                        </button>
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function() {
    var toggle = document.getElementById('notification-toggle');
    var dropdown = document.getElementById('notification-dropdown');

    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('notification-bell__dropdown--open');
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('notification-bell').contains(e.target)) {
            dropdown.classList.remove('notification-bell__dropdown--open');
        }
    });

    // Polling ogni 30 secondi
    setInterval(function() {
        fetch('/admin/notifications/count', { credentials: 'same-origin' })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var badge = document.getElementById('notification-badge');
                var count = parseInt(data.count) || 0;
                badge.textContent = count;
                if (count > 0) {
                    badge.classList.add('notification-bell__badge--visible');
                } else {
                    badge.classList.remove('notification-bell__badge--visible');
                }
            })
            .catch(function() {});
    }, 30000);
})();
</script>
