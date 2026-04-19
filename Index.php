<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAHRIM Dashboard</title>

    <link rel="stylesheet" href="css/style.css?v=40">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script defer src="js/index.js?v=40"></script>
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
            <option value="7" selected>7 days</option>
            <option value="14">14 days</option>
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
        </div>

        <div class="Map">
            Map
        </div>
    </div>

    <div class="box-row">
        <div class="box">
            <h2 id="chartBoxTitle">NAHRIM</h2>

            <div class="chart-controls">
                <label for="Scenario">Scenario:</label>
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
  </div>
  <?php include 'BottomBar.php'; ?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      var map = L.map('map').setView([4.2105, 101.9758], 7);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap | Data: Open-Meteo'
      }).addTo(map);

      let hospitalMarkers = [];
      const showPinsCheckbox = document.getElementById('show-pins');
      const refreshBtn = document.getElementById('refresh');

      fetch('map_data.json')
        .then(res => res.json())
        .then(data => {
          data.forEach(h => {
            const popupContent = ` <b>${h.NAME}</b><br> Type: ${h.TYPE_OF_HOSPITAL}<br> Address: ${h.ADDRESS}<br> `;
            const marker = L.marker([h.LATITUDE, h.LONGITUDE])
              .bindPopup(popupContent);
            hospitalMarkers.push(marker);
            if (showPinsCheckbox && showPinsCheckbox.checked) marker.addTo(map);
          });
        });

      if (showPinsCheckbox) {
        showPinsCheckbox.addEventListener('change', (e) => {
          if (e.target.checked) {
            hospitalMarkers.forEach(m => m.addTo(map));
          } else {
            hospitalMarkers.forEach(m => map.removeLayer(m));
          }
        });
      }

      if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
          
        });
      }

      
      let kedahRows = [];
      let selangorRows = [];

      function loadCsv(filePath, callback) {
        Papa.parse(filePath, {
          download: true,
          header: true,
          skipEmptyLines: true,
          complete: function(results) {
            callback(results.data);
          },
          error: function(err) {
            console.error('Error loading CSV:', err);
          }
        });
      }

      loadCsv("data/RF_Kedah_Mac_May_2026_AveRCP.csv", function(data) {
        kedahRows = data;
        loadCsv("data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv", function(data2) {
          selangorRows = data2;
          
          populateLocationDropdown('kedahLocation', getUniqueLocations(kedahRows));
          populateLocationDropdown('selangorLocation', getUniqueLocations(selangorRows));
          renderCombinedRainfallChart();
        });
      });

      function getUniqueLocations(rows) {
        const map = new Map();
        rows.forEach(row => {
          const lat = row.Latitude;
          const lon = row.Longitude;
          const key = `${lat},${lon}`;
          if (!map.has(key)) {
            map.set(key, { lat, lon });
          }
        });
        return Array.from(map.values());
      }

      function populateLocationDropdown(selectId, locations) {
        const select = document.getElementById(selectId);
        select.innerHTML = "";
        locations.forEach(loc => {
          const option = document.createElement("option");
          option.value = `${loc.lat},${loc.lon}`;
          option.textContent = `${loc.lat}, ${loc.lon}`;
          select.appendChild(option);
        });
      }

      function renderCombinedRainfallChart() {
        
      }
    });
  </script>
</body>
</html>