<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NAHRIM Dashboard</title>
  <link rel="stylesheet" href="css/style.css?v=30">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script defer src="js/index.js?v=30"></script>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div role="banner" class="actionbar">
    <div class="imageBox" style="display: inline-flex;">
      <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
    </div>
    <form action="#" method="POST" role="form">
      <label for="Range">Select Range:</label>
      <select name="Range" id="Range">
        <option value="Range1">&lt;1 day</option>
        <option value="Range2">&lt;1 week</option>
        <option value="Range3">&lt;2 weeks</option>
        <option value="Range4">&lt;4 weeks</option>
      </select>
    </form>
  </div>
  <div class="page-content">
    <div class="grid">
      <div class="NAHRIM" id="sourcePanel">
        <h2 id="sourcePanelTitle">COMBINED PANEL</h2>
      </div>
      <div class="Sources">
        <form action="#" method="POST">
          <div class="source-item">
            <input type="checkbox" id="NAHRIM" name="Source[]" value="NAHRIM">
            <label for="NAHRIM">NAHRIM</label>
          </div>
          <div class="source-item">
            <input type="checkbox" id="OpenMeteo" name="Source[]" value="OpenMeteo">
            <label for="OpenMeteo">OpenMeteo</label>
          </div>
          <div class="source-item">
            <input type="checkbox" id="Google" name="Source[]" value="Google">
            <label for="Google">Google</label>
          </div>
        </form>
      </div>
      <div style="margin: 0 auto; display: block; text-align: center;">
        <label><input type="checkbox" id="show-pins" checked> Show Hospital Pins</label>
        <button id="refresh" style="margin-left:10px;">Refresh Data</button>
      </div>
      <div class="Map" id="map" style="height: 600px; width: 100%;"></div>
    </div>
    <div class="box-row">
      <div class="box">
        <h2>NAHRIM</h2>
        <div class="chart-controls">
          <label for="kedahLocation">Kedah:</label>
          <select id="kedahLocation"></select>
          <label for="selangorLocation">Selangor:</label>
          <select id="selangorLocation"></select>
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