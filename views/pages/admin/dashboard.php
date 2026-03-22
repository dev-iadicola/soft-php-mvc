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
      <div class="card text-white bg-warning">
        <div class="card-header"><i class="fa fa-commenting"></i> Messaggi</div>
        <div class="card-body">
          <h3 class="card-title"><?= count($messages) ?></h3>
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
          Messages <i class="fa fa-commenting" aria-hidden="true"></i>

        </div>
        <div class="card-body d-flex justify-content-around">
          <div class="list-group" style="max-height: 700px; overflow-y: auto;">

          <div class="list-group" style="max-height: 300px; overflow-y: auto;">

            <?php foreach ($messages as $msg) : ?>
             
              <div class="list-group-item list-group-item-action my-2 rounded-2 ">
                <h5 class="mb-1">Mittente: <?= $msg->nome ?> - <?= $msg->typologie ?></h5>
                <h6 class="mb-1">Indirizzo email: <a href="mailto:<?= $msg->email ?>"><?= $msg->email ?></a></h6>
                <p class="mb-2 overflow-auto"><?=substr( $msg->messaggio,0,300) ?></p>
                <small>Data: <?php echo date('d/m/Y - H:i:s', strtotime($msg->created_at)) ?></small>
              </div>
            <?php endforeach ?>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card-collapsible card">
        <div class="card-header">
          Table <i class="fa fa-caret-down caret"></i>
        </div>
        <div class="card-body">
          <table class="table">
            <thead class="thead bg-primary text-white">
              <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
              </tr>
              <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
              </tr>
              <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 pr-0 mb-3">
      <div class="card-collapsible card">
        <div class="card-header">
          Quick Form <i class="fa fa-caret-down caret"></i>
        </div>
        <div class="card-body">
          <form>

            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Assignee's email">
              <div class="input-group-append">
                <span class="input-group-text">@example.com</span>
              </div>
            </div>

            <div class="form-group">
              <input type="text" class="form-control" placeholder="Ticket title">
            </div>

            <div class="form-group">
              <textarea class="form-control" placeholder="Ticket description" cols="30" rows="5"></textarea>
            </div>

            <div class="form-group row">
              <div class="col-sm-10">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-send"></i>
                  Submit Ticket
                </button>
              </div>
            </div>

          </form>
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