<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">Dashboard</h1>
        <p class="text-muted small mb-0">Panoramica del tuo sito</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="dashboard-stats">
    <a href="/admin/visitors" class="stat-card">
        <div class="stat-card__icon stat-card__icon--primary">
            <i data-lucide="eye" style="width:20px;height:20px;"></i>
        </div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= number_format($totalVisits) ?></div>
            <div class="stat-card__label">Visite Totali</div>
        </div>
    </a>

    <a href="/admin/contatti" class="stat-card">
        <div class="stat-card__icon stat-card__icon--<?= $unreadCount > 0 ? 'danger' : 'warning' ?>">
            <i data-lucide="mail" style="width:20px;height:20px;"></i>
        </div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= $unreadCount ?></div>
            <div class="stat-card__label">Messaggi non letti</div>
        </div>
    </a>

    <a href="/admin/visitors" class="stat-card">
        <div class="stat-card__icon stat-card__icon--info">
            <i data-lucide="users" style="width:20px;height:20px;"></i>
        </div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= number_format($uniqueVisitors) ?></div>
            <div class="stat-card__label">Visitatori Unici</div>
        </div>
    </a>

    <a href="/admin/project" class="stat-card">
        <div class="stat-card__icon stat-card__icon--success">
            <i data-lucide="folder-open" style="width:20px;height:20px;"></i>
        </div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= $totalProjects ?></div>
            <div class="stat-card__label">Progetti Attivi</div>
        </div>
    </a>

    <a href="/admin/home" class="stat-card">
        <div class="stat-card__icon stat-card__icon--info">
            <i data-lucide="file-text" style="width:20px;height:20px;"></i>
        </div>
        <div class="stat-card__content">
            <div class="stat-card__value"><?= $totalArticles ?></div>
            <div class="stat-card__label">Articoli Attivi</div>
        </div>
    </a>
</div>

<!-- Charts Row -->
<div class="dashboard-grid mb-4">
    <!-- Weekly visits chart -->
    <div class="admin-card">
        <div class="admin-card__header">
            <h3><i data-lucide="trending-up" style="width:16px;height:16px;margin-right:6px;opacity:0.5;"></i> Visite (ultimi 7 giorni)</h3>
        </div>
        <div class="admin-card__body">
            <canvas id="myLineChart" height="200"></canvas>
        </div>
    </div>

    <!-- Doughnut chart -->
    <div class="admin-card">
        <div class="admin-card__header">
            <h3><i data-lucide="pie-chart" style="width:16px;height:16px;margin-right:6px;opacity:0.5;"></i> Visite oggi vs totali</h3>
        </div>
        <div class="admin-card__body d-flex justify-content-center">
            <canvas id="myChart" style="max-height:240px;"></canvas>
        </div>
    </div>
</div>

<!-- Messages -->
<div class="admin-card">
    <div class="admin-card__header">
        <h3>
            <i data-lucide="inbox" style="width:16px;height:16px;margin-right:6px;opacity:0.5;"></i>
            Messaggi recenti
            <?php if ($unreadCount > 0) : ?>
                <span class="admin-badge admin-badge--danger ml-2"><?= $unreadCount ?> non letti</span>
            <?php endif; ?>
        </h3>
        <a href="/admin/contatti" class="btn btn-sm btn-admin-secondary">Vedi tutti</a>
    </div>
    <div class="admin-card__body p-0">
        <div style="max-height: 400px; overflow-y: auto;">
            <?php if (empty($messages)) : ?>
                <div class="text-center py-4 text-muted">Nessun messaggio</div>
            <?php else : ?>
                <?php foreach ($messages as $msg) : ?>
                    <a href="/admin/contatti/<?= $msg->id ?>" class="d-flex align-items-start gap-3 px-4 py-3 text-decoration-none" style="border-bottom: 1px solid var(--admin-border-light); transition: background 0.15s;<?= !$msg->is_read ? 'background: var(--admin-warning-light);' : '' ?>" onmouseover="this.style.background='var(--admin-surface-hover)'" onmouseout="this.style.background='<?= !$msg->is_read ? 'var(--admin-warning-light)' : '' ?>'">
                        <div style="width:36px;height:36px;border-radius:50%;background:var(--admin-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:600;color:var(--admin-text-secondary);font-size:0.8rem;">
                            <?= strtoupper(substr($msg->nome ?? '?', 0, 1)) ?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-weight:<?= !$msg->is_read ? '600' : '400' ?>;color:var(--admin-text);font-size:0.82rem;">
                                    <?= htmlspecialchars($msg->nome) ?>
                                    <?php if ($msg->typologie) : ?>
                                        <span class="admin-badge admin-badge--info ml-1"><?= htmlspecialchars($msg->typologie) ?></span>
                                    <?php endif; ?>
                                </span>
                                <small style="color:var(--admin-text-muted);font-size:0.7rem;white-space:nowrap;margin-left:8px;">
                                    <?= date('d/m H:i', strtotime($msg->created_at)) ?>
                                </small>
                            </div>
                            <p style="margin:0;color:var(--admin-text-secondary);font-size:0.78rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?= htmlspecialchars(substr($msg->messaggio, 0, 120)) ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart defaults
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';

    // Weekly visits line chart
    var dailyData = <?= json_encode($dailyVisits) ?>;
    var dailyLabels = dailyData.map(function(d) {
        var parts = (d.date || '').split('-');
        return parts.length >= 3 ? parts[2] + '/' + parts[1] : d.date;
    });
    var dailyCounts = dailyData.map(function(d) { return parseInt(d.count) || 0; });

    new Chart(document.getElementById('myLineChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Visite',
                data: dailyCounts,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#4f46e5',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                }
            }
        }
    });

    // Doughnut chart
    var ctxDoughnut = document.getElementById('myChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Visite Oggi', 'Altre'],
                datasets: [{
                    data: [<?= $todayVisits ?>, <?= max($totalVisits - $todayVisits, 0) ?>],
                    backgroundColor: ['#4f46e5', '#e2e8f0'],
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 16, font: { size: 12 } }
                    }
                }
            }
        });
    }
});
</script>
