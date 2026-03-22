<section class="container justify-content-center m-auto">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fa fa-line-chart"></i> Statistiche Visitatori</h1>
        <a href="/admin/dashboard" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-header"><i class="fa fa-eye"></i> Visite Totali</div>
                <div class="card-body">
                    <h3 class="card-title mb-0"><?= number_format($totalVisits) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card text-white bg-success">
                <div class="card-header"><i class="fa fa-users"></i> Visitatori Unici</div>
                <div class="card-body">
                    <h3 class="card-title mb-0"><?= number_format($uniqueVisitors) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card text-white bg-info">
                <div class="card-header"><i class="fa fa-calendar-check-o"></i> Visite Oggi</div>
                <div class="card-body">
                    <h3 class="card-title mb-0"><?= number_format($todayVisits) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-header"><i class="fa fa-user"></i> Unici Oggi</div>
                <div class="card-body">
                    <h3 class="card-title mb-0"><?= number_format($todayUnique) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafico Visite Giornaliere -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-area-chart"></i> Visite Giornaliere (ultimi 30 giorni)
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafici Browser e Dispositivi -->
    <div class="row mb-4">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-globe"></i> Browser
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="browserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-mobile"></i> Dispositivi
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafico Visite Mensili -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-bar-chart"></i> Visite Mensili (ultimi 12 mesi)
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagine piu visitate e visite recenti -->
    <div class="row mb-4">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-file-text-o"></i> Pagine piu visitate
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Pagina</th>
                                <th class="text-right">Visite</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topPages as $page) : ?>
                                <tr>
                                    <td><code><?= htmlspecialchars((string) ($page['url'] ?? $page->url ?? '')) ?></code></td>
                                    <td class="text-right"><strong><?= number_format((int) ($page['count'] ?? $page->count ?? 0)) ?></strong></td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (empty($topPages)) : ?>
                                <tr><td colspan="2" class="text-center text-muted">Nessun dato</td></tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-clock-o"></i> Visite Recenti
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>IP</th>
                                <th>Pagina</th>
                                <th>Dispositivo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentVisits as $visit) : ?>
                                <?php
                                    $ip = is_array($visit) ? ($visit['ip_address'] ?? '') : ($visit->ip_address ?? '');
                                    $url = is_array($visit) ? ($visit['url'] ?? '') : ($visit->url ?? '');
                                    $ua = is_array($visit) ? ($visit['user_agent'] ?? '') : ($visit->user_agent ?? '');
                                    $date = is_array($visit) ? ($visit['created_at'] ?? '') : ($visit->created_at ?? '');
                                    $uaLower = strtolower((string) $ua);
                                    $deviceIcon = str_contains($uaLower, 'mobile') || str_contains($uaLower, 'iphone') || str_contains($uaLower, 'android')
                                        ? 'fa-mobile' : 'fa-desktop';
                                ?>
                                <tr>
                                    <td><small><?= htmlspecialchars(substr((string) $ip, 0, 15)) ?></small></td>
                                    <td><small><code><?= htmlspecialchars(substr((string) $url, 0, 30)) ?></code></small></td>
                                    <td><i class="fa <?= $deviceIcon ?>"></i></td>
                                    <td><small><?= $date ? date('d/m H:i', strtotime((string) $date)) : '-' ?></small></td>
                                </tr>
                            <?php endforeach ?>
                            <?php if (empty($recentVisits)) : ?>
                                <tr><td colspan="4" class="text-center text-muted">Nessuna visita registrata</td></tr>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartColors = {
        blue: 'rgba(54, 162, 235, 1)',
        blueBg: 'rgba(54, 162, 235, 0.2)',
        green: 'rgba(75, 192, 192, 1)',
        greenBg: 'rgba(75, 192, 192, 0.2)',
        palette: [
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)'
        ]
    };

    // --- Grafico visite giornaliere ---
    var dailyData = <?= json_encode($dailyVisits) ?>;
    var dailyLabels = dailyData.map(function(d) {
        var parts = (d.date || '').split('-');
        return parts.length >= 3 ? parts[2] + '/' + parts[1] : d.date;
    });
    var dailyCounts = dailyData.map(function(d) { return parseInt(d.count) || 0; });

    new Chart(document.getElementById('dailyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Visite',
                data: dailyCounts,
                borderColor: chartColors.blue,
                backgroundColor: chartColors.blueBg,
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // --- Grafico browser (doughnut) ---
    var browserData = <?= json_encode($topBrowsers) ?>;
    var browserLabels = browserData.map(function(b) { return b.browser || 'Altro'; });
    var browserCounts = browserData.map(function(b) { return parseInt(b.count) || 0; });

    new Chart(document.getElementById('browserChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: browserLabels,
            datasets: [{
                data: browserCounts,
                backgroundColor: chartColors.palette.slice(0, browserLabels.length)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } }
            }
        }
    });

    // --- Grafico dispositivi (pie) ---
    var deviceData = <?= json_encode($topDevices) ?>;
    var deviceLabels = deviceData.map(function(d) { return d.device || 'Altro'; });
    var deviceCounts = deviceData.map(function(d) { return parseInt(d.count) || 0; });

    new Chart(document.getElementById('deviceChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: deviceLabels,
            datasets: [{
                data: deviceCounts,
                backgroundColor: chartColors.palette.slice(0, deviceLabels.length)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } }
            }
        }
    });

    // --- Grafico visite mensili (bar) ---
    var monthlyData = <?= json_encode($monthlyVisits) ?>;
    var monthlyLabels = monthlyData.map(function(m) {
        var parts = (m.month || '').split('-');
        var monthNames = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
        return parts.length >= 2 ? monthNames[parseInt(parts[1]) - 1] + ' ' + parts[0] : m.month;
    });
    var monthlyCounts = monthlyData.map(function(m) { return parseInt(m.count) || 0; });

    new Chart(document.getElementById('monthlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Visite',
                data: monthlyCounts,
                backgroundColor: chartColors.greenBg,
                borderColor: chartColors.green,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
});
</script>
