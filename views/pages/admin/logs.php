
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
    <h2>Logs</h2>
    <div class="card-container mt-5">
        <?php foreach($logs as $log): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= $log->device ?></h5>
                    <p class="card-text"><strong>IP:</strong> <?= $log->indirizzo ?></p>
                    <p class="card-text"><strong>Last Log:</strong> <?= $log->last_log ?></p>
                    <p class="card-text"><strong>Number of Access:</strong> <?= $log->login_count ?></p>
                    <p class="card-text location" data-ip="<?= $log->indirizzo ?>">
                        Loading...
                    </p>
                    <button class="btn btn-secondary btn-sm" onclick="showMap('<?= $log->indirizzo ?>')">Show Map</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    
    <!-- Includi i file JavaScript alla fine del documento -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {
            const apiUrl = 'https://ipinfo.io/'; // API URL per ipinfo.io

            function createMap(lat, lon, elementId) {
                const mapElement = $(`#${elementId}`);
                if (mapElement.length === 0) {
                    console.error(`Map container with id ${elementId} not found.`);
                    return;
                }
                
                const map = L.map(elementId).setView([lat, lon], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);
                L.marker([lat, lon]).addTo(map);
            }

            function getLocation(ip, callback) {
                $.ajax({
                    url: `${apiUrl}${ip}/json`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('API Response:', response); // Debug: mostra la risposta dell'API
                        if (!response.loc) {
                            callback('Error retrieving location', null, null, null, null);
                            return;
                        }
                        const [lat, lon] = response.loc.split(',');
                        const location = response.city ? `${response.city}, ${response.region}, ${response.country}` : 'Unknown';
                        const mapId = ip.replace(/\./g, '_');
                        
                        // Aggiungi una colonna per la mappa
                        callback(null, location, mapId, lat, lon);
                    },
                    error: function(xhr, status, error) {
                        console.error(`Request failed. Status: ${status}. Error: ${error}`); // Debug: mostra dettagli dell'errore
                        callback('Error retrieving location', null, null, null, null);
                    }
                });
            }

            function showMap(ip) {
                getLocation(ip, function(error, location, mapId, lat, lon) {
                    const $locationCell = $(`.location[data-ip="${ip}"]`);
                    if (error) {
                        $locationCell.html(error);
                    } else {
                        const mapHtml = `<div id="${mapId}" class="map map-visible"></div>`;
                        $locationCell.addClass('map-container').html(location + mapHtml);
                        createMap(parseFloat(lat), parseFloat(lon), mapId);
                    }
                });
            }

            // Popola le celle con i dati iniziali
            $('table tbody tr').each(function() {
                const ip = $(this).find('td').first().text().trim();
                getLocation(ip, function(error, location) {
                    if (error) {
                        $(`.location[data-ip="${ip}"]`).html(error);
                    } else {
                        $(`.location[data-ip="${ip}"]`).html(location);
                    }
                });
            });
        });

        // Funzione globale per visualizzare la mappa
        function showMap(ip) {
            $(document).ready(function() {
                $.ajax({
                    url: `https://ipinfo.io/${ip}/json`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log('API Response:', response); // Debug: mostra la risposta dell'API
                        if (!response.loc) {
                            $(`.location[data-ip="${ip}"]`).html('Error retrieving location');
                            return;
                        }
                        const [lat, lon] = response.loc.split(',');
                        const location = response.city ? `${response.city}, ${response.region}, ${response.country}` : 'Unknown';
                        const mapId = ip.replace(/\./g, '_');
                        
                        // Aggiungi una colonna per la mappa
                        const mapHtml = `<div id="${mapId}" class="map map-visible"></div>`;
                        const $locationCell = $(`.location[data-ip="${ip}"]`);
                        $locationCell.addClass('map-container').html(location + mapHtml);
                        
                        // Crea la mappa
                        const mapElement = $(`#${mapId}`);
                        if (mapElement.length === 0) {
                            console.error(`Map container with id ${mapId} not found.`);
                            return;
                        }
                        const map = L.map(mapId).setView([parseFloat(lat), parseFloat(lon)], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19
                        }).addTo(map);
                        L.marker([parseFloat(lat), parseFloat(lon)]).addTo(map);
                    },
                    error: function(xhr, status, error) {
                        console.error(`Request failed. Status: ${status}. Error: ${error}`); // Debug: mostra dettagli dell'errore
                        $(`.location[data-ip="${ip}"]`).html('Error retrieving location');
                    }
                });
            });
        }
    </script>

