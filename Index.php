﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAHRIM Dashboard</title>

    <link rel="stylesheet" href="css/style.css?v=70">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script defer src="js/index.js?v=2000000"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="actionbar" role="banner">
    <div class="imageBox" style="display: inline-flex;">
        <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
    </div>

    <form action="#" method="POST">
        <label for="Source">Source:</label>
        <select name="Source" id="Source">
            <option value="NAHRIM" selected>NAHRIM</option>
            <option value="OpenMeteo">OpenMeteo</option>
            <option value="Google">Google</option>
        </select>
    </form>

    <form action="#" method="POST">
        <label for="Range">Select Range:</label>
        <select name="Range" id="Range">
            <option value="3">3 days</option>
            <option value="7">7 days</option>
<option value="14" selected>14 days</option>
        </select>
    </form>
</div>

<div class="page-content">
    <div class="grid">
        <div class="NAHRIM" id="sourcePanel">
            <h2 id="sourcePanelTitle">NAHRIM PANEL</h2>
        </div>

        <div class="Sources">
            <form action="#" method="POST">
                <div class="source-item">
                    <input type="checkbox" id="NAHRIM" checked>
                    <label for="NAHRIM">NAHRIM</label>
                </div>

                <div class="source-item">
                    <input type="checkbox" id="OpenMeteo">
                    <label for="OpenMeteo">OpenMeteo</label>
                </div>

                <div class="source-item">
                    <input type="checkbox" id="Google">
                    <label for="Google">Google</label>
                </div>
            </form>

            <div style="margin-top: 15px; text-align: left;">
                <label style="font-size: 16px;">
                    <input type="checkbox" id="show-pins" checked> Show Hospital Pins
                </label>
                <button id="refresh" type="button" style="margin-left: 10px; padding: 6px 10px; cursor: pointer;">
                    Refresh Data
                </button>
            </div>
        </div>

        <div class="Map" id="map" style="height: 350px; width: 100%;"></div>
    </div>

    <div class="box-row">
        <div class="box">
            <h2 id="chartBoxTitle">NAHRIM</h2>

            <div class="chart-controls">
                <label for="Scenario" id="scenarioLabel">Scenario:</label>
                <select id="Scenario">
                    <option value="Ave_RCP2.6">RCP 2.6</option>
                    <option value="Ave_RCP4.5" selected>RCP 4.5</option>
                    <option value="Ave_RCP6.0">RCP 6.0</option>
                    <option value="Ave_RCP8.5">RCP 8.5</option>
                </select>
            </div>

            <div class="chart-container">
                <canvas id="combinedRainfallChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include 'BottomBar.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map');

    if (!mapElement || typeof L === 'undefined') return;

    const map = L.map('map').setView([4.2105, 101.9758], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap | Data: Open-Meteo'
    }).addTo(map);

    let hospitalMarkers = [];
    const showPinsCheckbox = document.getElementById('show-pins');
    const refreshBtn = document.getElementById('refresh');

    fetch('map_data.json?v=70')
        .then(res => res.json())
        .then(data => {
            data.forEach(h => {
                const popupContent = `<b>${h.NAME}</b><br>Type: ${h.TYPE_OF_HOSPITAL}<br>Address: ${h.ADDRESS}`;
                const marker = L.marker([h.LATITUDE, h.LONGITUDE]).bindPopup(popupContent);
                hospitalMarkers.push(marker);

                if (showPinsCheckbox && showPinsCheckbox.checked) {
                    marker.addTo(map);
                }
            });
        })
        .catch(err => {
            console.error('Error loading map_data.json:', err);
        });

    if (showPinsCheckbox) {
        showPinsCheckbox.addEventListener('change', (e) => {
            if (e.target.checked) {
                hospitalMarkers.forEach(marker => marker.addTo(map));
            } else {
                hospitalMarkers.forEach(marker => map.removeLayer(marker));
            }
        });
    }

    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            location.reload();
        });
    }
});
</script>

</body>
</html>