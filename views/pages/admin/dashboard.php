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
    
    <div class="col-lg-3 col-md-3 col-sm-12 pr-0 mb-3">
      <div class="card  ">
        <div class="card-header"><i class="fa fa-bar-chart"></i> Visite</div>
        <div class="">
          <canvas id="myLineChart"></canvas>

          <script>
            // Dati del grafico
            const data = {
              labels: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug'], // Etichette per l'asse X
              datasets: [{
                label: 'Vendite Mensili', // Etichetta del dataset
                data: [30, 45, 60, 70, 50, 80, 90], // Dati da tracciare
                borderColor: 'rgba(75, 192, 192, 1)', // Colore del bordo della linea
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Colore di riempimento sotto la linea
                fill: true, // Riempi sotto la linea
                tension: 0.1 // Arrotondamento della curva della linea
              }]
            };

            // Configurazione del grafico
            const config = {
              type: 'line', // Tipo di grafico
              data: data,
              options: {
                responsive: true, // Rende il grafico responsive
                plugins: {
                  legend: {
                    position: 'top', // Posizione della legenda
                  },
                  tooltip: {
                    callbacks: {
                      label: function(tooltipItem) {
                        return `Vendite: ${tooltipItem.raw} units`;
                      }
                    }
                  }
                },
                scales: {
                  x: {
                    title: {
                      display: true,
                      text: 'Mese'
                    }
                  },
                  y: {
                    title: {
                      display: true,
                      text: 'Vendite'
                    }
                  }
                }
              }
            };

            // Creazione del grafico
            const ctxVisit = document.getElementById('myLineChart').getContext('2d');
            new Chart(ctxVisit, config);
          </script>


        </div>

      </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 pr-0 mb-3">
      <div class="card text-white bg-warning">
        <div class="card-header"><i class="fa fa-user-plus"></i> Message</div>
        <div class="card-body">
          <h3 class="card-title">44</h3>
        </div>
        <a class="card-footer text-right text-white" href="#">
          More info <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 pr-0 mb-3">
      <div class="card text-white bg-danger">
        <div class="card-header"><i class="fa fa-pie-chart"></i> Unique Visitor</div>
        <div class="card-body">
          <h3 class="card-title">65</h3>
        </div>
        <a class="card-footer text-right text-white" href="#">
          More info <i class="fa fa-arrow-circle-right"></i>
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

            <?php foreach ($message as $msg) : ?>
             
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>