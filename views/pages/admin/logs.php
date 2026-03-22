
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 200px; width: 100%; }
        .map { display: none; height: 200px; width: 100%; }
        .map-visible { display: block; }
        .map-container { position: relative; }
    </style>
</head>
<body>

<div class="container align-middle">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Logs</h2>
        <div>
            <a href="<?= route('admin.logs.export') . '&' . http_build_query($filters) ?>" class="btn btn-success btn-sm">
                <i class="fa fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= route('admin.logs') ?>" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label"><strong>Da</strong></label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($filters['date_from']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><strong>A</strong></label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($filters['date_to']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><strong>User ID</strong></label>
                    <input type="number" name="user_id" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($filters['user_id']) ?>" placeholder="ID utente">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><strong>Dispositivo</strong></label>
                    <select name="device" class="form-control form-control-sm">
                        <option value="">Tutti</option>
                        <?php foreach ($devices as $device): ?>
                            <option value="<?= htmlspecialchars($device) ?>"
                                <?= $filters['device'] === $device ? 'selected' : '' ?>>
                                <?= htmlspecialchars(mb_substr($device, 0, 60)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> Filtra
                    </button>
                    <a href="<?= route('admin.logs') ?>" class="btn btn-secondary btn-sm">
                        <i class="fa fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete old logs -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="<?= route('admin.logs.deleteOld') ?>"
                  onsubmit="return confirm('Sei sicuro di voler eliminare i log precedenti a questa data?');">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label"><strong>Elimina log precedenti al</strong></label>
                        <input type="date" name="delete_before" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i> Elimina log vecchi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results info -->
    <p class="text-muted">
        Totale: <strong><?= $paginator->totalItems() ?></strong> risultati
        <?php if ($paginator->hasPages()): ?>
            &mdash; Pagina <?= $paginator->currentPage() ?> di <?= $paginator->totalPages() ?>
        <?php endif; ?>
    </p>

    <!-- Log cards -->
    <div class="card-container mt-3">
        <?php if (empty($paginator->items())): ?>
            <div class="alert alert-info">Nessun log trovato.</div>
        <?php else: ?>
            <?php foreach ($paginator->items() as $log): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($log['device']) ?></h5>
                        <p class="card-text"><strong>IP:</strong> <?= htmlspecialchars($log['indirizzo']) ?></p>
                        <p class="card-text"><strong>User ID:</strong> <?= htmlspecialchars((string) $log['user_id']) ?></p>
                        <p class="card-text"><strong>Last Log:</strong> <?= htmlspecialchars($log['last_log']) ?></p>
                        <p class="card-text"><strong>Number of Access:</strong> <?= (int) $log['login_count'] ?></p>
                        <p class="card-text location" data-ip="<?= htmlspecialchars($log['indirizzo']) ?>">
                            Loading...
                        </p>
                        <button class="btn btn-secondary btn-sm" onclick="showMap('<?= htmlspecialchars($log['indirizzo']) ?>')">Show Map</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($paginator->hasPages()): ?>
        <nav aria-label="Pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- Previous -->
                <li class="page-item <?= !$paginator->hasPreviousPage() ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $paginator->url($paginator->previousPage()) ?>">
                        &laquo; Prec
                    </a>
                </li>

                <?php foreach ($paginator->pageRange() as $p): ?>
                    <?php if ($p === null): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php else: ?>
                        <li class="page-item <?= $p === $paginator->currentPage() ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $paginator->url($p) ?>"><?= $p ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Next -->
                <li class="page-item <?= !$paginator->hasNextPage() ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $paginator->url($paginator->nextPage()) ?>">
                        Succ &raquo;
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        function isPrivateIp(ip) {
            return /^(127\.|10\.|172\.(1[6-9]|2\d|3[01])\.|192\.168\.|::1|0\.0\.0\.0|localhost)/.test(ip);
        }

        function showMap(ip) {
            var $cell = $(`.location[data-ip="${ip}"]`);

            if (isPrivateIp(ip)) {
                $cell.html('<span class="text-warning">IP locale/privato — geolocalizzazione non disponibile</span>');
                return;
            }

            $cell.html('<i class="fa fa-spinner fa-spin"></i> Caricamento...');

            $.ajax({
                url: `https://ipinfo.io/${ip}/json`,
                method: 'GET',
                dataType: 'json',
                timeout: 8000,
                success: function(response) {
                    if (response.bogon) {
                        $cell.html('<span class="text-warning">IP riservato — geolocalizzazione non disponibile</span>');
                        return;
                    }
                    if (!response.loc) {
                        var msg = response.error ? response.error.message : 'Posizione non disponibile per questo IP';
                        $cell.html('<span class="text-danger">' + msg + '</span>');
                        return;
                    }
                    var parts = response.loc.split(',');
                    var lat = parseFloat(parts[0]);
                    var lon = parseFloat(parts[1]);
                    var location = response.city ? `${response.city}, ${response.region}, ${response.country}` : 'Unknown';
                    var mapId = ip.replace(/\./g, '_');

                    var mapHtml = `<div id="${mapId}" class="map map-visible"></div>`;
                    $cell.addClass('map-container').html(location + mapHtml);

                    var map = L.map(mapId).setView([lat, lon], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19
                    }).addTo(map);
                    L.marker([lat, lon]).addTo(map);
                },
                error: function(xhr) {
                    var msg = 'Errore API';
                    if (xhr.status === 429) {
                        msg = 'Troppe richieste — limite API raggiunto (riprova tra poco)';
                    } else if (xhr.status === 403) {
                        msg = 'Accesso negato — potrebbe servire un token API per ipinfo.io';
                    } else if (xhr.statusText === 'timeout') {
                        msg = 'Timeout — il servizio non ha risposto';
                    }
                    $cell.html('<span class="text-danger">' + msg + '</span>');
                }
            });
        }
    </script>

