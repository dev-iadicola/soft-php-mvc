<style>
  .border-left-bold {
    border-left: 4px solid #ffc107 !important;
    background-color: rgba(255, 193, 7, 0.05);
  }
</style>

<section class="container justify-content-center m-auto">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa fa-tachometer"></i> Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-primary">Share</button>
        <button class="btn btn-sm btn-primary">Export</button>
      </div>
      <button class="btn btn-sm btn-primary dropdown-toggle">
        <i class="fa fa-calendar"></i>
        This week
      </button>
    </div>
  </div>

  <div class="row">

    <div class="col-lg-3 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card">
        <div class="card-header"><i class="fa fa-bar-chart"></i> Visite (7 giorni)</div>
        <div class="card-body p-2">
          <canvas id="myLineChart" height="150"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card text-white bg-primary">
        <div class="card-header"><i class="fa fa-eye"></i> Visite Totali</div>
        <div class="card-body">
          <h3 class="card-title"><?= number_format($totalVisits) ?></h3>
        </div>
        <a class="card-footer text-right text-white" href="/admin/visitors">
          Dettagli <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card text-white <?= $unreadCount > 0 ? 'bg-danger' : 'bg-warning' ?>">
        <div class="card-header"><i class="fa fa-commenting"></i> Messaggi non letti</div>
        <div class="card-body">
          <h3 class="card-title"><?= $unreadCount ?></h3>
        </div>
        <a class="card-footer text-right text-white" href="/admin/contatti">
          Dettagli <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card text-white bg-danger">
        <div class="card-header"><i class="fa fa-users"></i> Visitatori Unici</div>
        <div class="card-body">
          <h3 class="card-title"><?= number_format($uniqueVisitors) ?></h3>
        </div>
        <a class="card-footer text-right text-white" href="/admin/visitors">
          Dettagli <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card text-white bg-success">
        <div class="card-header"><i class="fa fa-folder-open"></i> Progetti Attivi</div>
        <div class="card-body">
          <h3 class="card-title"><?= $totalProjects ?></h3>
        </div>
        <a class="card-footer text-right text-white" href="/admin/project">
          Gestisci <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card text-white bg-info">
        <div class="card-header"><i class="fa fa-pencil"></i> Articoli Attivi</div>
        <div class="card-body">
          <h3 class="card-title"><?= $totalArticles ?></h3>
        </div>
        <a class="card-footer text-right text-white" href="/admin/home">
          Gestisci <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card-collapsible card">
        <div class="card-header">
          Doughnut Chart <i class="fa fa-caret-down caret"></i>
        </div>
        <div class="card-body d-flex justify-content-around">
          <canvas id="myChart"></canvas>
        </div>
      </div>
    </div>

    
    <div class="col-lg-6 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card-collapsible card">
        <div class="card-header">
          Messaggi <i class="fa fa-commenting" aria-hidden="true"></i>
          <?php if ($unreadCount > 0) : ?>
            <span class="badge badge-danger ml-1"><?= $unreadCount ?> non letti</span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <div class="list-group" style="max-height: 400px; overflow-y: auto;">
            <?php foreach ($messages as $msg) : ?>
              <a href="/admin/contatti/<?= $msg->id ?>" class="list-group-item list-group-item-action my-1 rounded-2 <?= !$msg->is_read ? 'border-left-bold' : '' ?>">
                <div class="d-flex justify-content-between align-items-center">
                  <h6 class="mb-1" style="<?= !$msg->is_read ? 'font-weight:bold;' : '' ?>">
                    <?= htmlspecialchars($msg->nome) ?> — <?= htmlspecialchars($msg->typologie ?? '') ?>
                  </h6>
                  <?php if (!$msg->is_read) : ?>
                    <span class="badge badge-warning">Nuovo</span>
                  <?php endif; ?>
                </div>
                <p class="mb-1 text-muted small"><?= htmlspecialchars(substr($msg->messaggio, 0, 120)) ?>...</p>
                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($msg->created_at)) ?></small>
              </a>
            <?php endforeach ?>
          </div>
        </div>
      </div>
    </div>
  </div>


</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mini grafico visite settimanali
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
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: true,
        tension: 0.3,
        pointRadius: 2
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { display: true, ticks: { font: { size: 10 } } },
        y: { beginAtZero: true, ticks: { precision: 0 } }
      }
    }
  });

  // Doughnut chart
  var ctxDoughnut = document.getElementById('myChart');
  if (ctxDoughnut) {
    new Chart(ctxDoughnut, {
      type: 'doughnut',
      data: {
        labels: ['Visite Oggi', 'Totali'],
        datasets: [{
          data: [<?= $todayVisits ?>, <?= max($totalVisits - $todayVisits, 0) ?>],
          backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(199, 199, 199, 0.3)']
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
      }
    });
  }
});
</script>