<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Hospital Weather Map</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f0f0f0; margin: 0; padding: 20px; }
    h1 { text-align: center; color: #333; }
    #map { height: 600px; width: 80%; margin: 20px auto; }
    
#legend {
  position: absolute;
  top: 100px;
  right: 20px;
  background-color: #fff;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  z-index: 1000;
}

  </style>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css" />
  <link rel="stylesheet" href="css/style.css?v=1" />
</head>
<body>
  <?php include 'navbar.php'; ?>
  <h1>Google Weather Map</h1>
  <h2>Rainfall Data for Malaysia</h2>
  <div style="margin: 0 auto; display: block; text-align: center;">
    <label><input type="checkbox" id="show-pins" checked> Show Hospital Pins</label>
    <button id="refresh" style="margin-left:10px;">Refresh Data</button>
  </div>
  <div id="map"></div>
  <div id="legend">
    <h4>Rainfall (mm) Today</h4>
    <div style="display: flex; flex-direction: column;">
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #0000ff;"></div><span>0 - 10</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #00ffff;"></div><span>10 - 20</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #00ff00;"></div><span>20 - 30</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #ffff00;"></div><span>30 - 40</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #ff0000;"></div><span>40+</span>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
  <script>
    var map = L.map('map').setView([4.2105, 101.9758], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap | Data: Google'
    }).addTo(map);

    let heatmapLayer;
    let hospitalMarkers = [];
    const showPinsCheckbox = document.getElementById('show-pins');
    const refreshBtn = document.getElementById('refresh');

    
    
fetch('map_data.json')
 .then(res => res.json())
 .then(data => {
    data.forEach(h => {
      const popupContent = `
        <b>${h.NAME}</b><br>
        Type: ${h.TYPE_OF_HOSPITAL}<br>
        Address: ${h.ADDRESS}<br>
        Rainfall: ${h.rainfall_mm} mm<br>
        Condition: ${h.condition}
      `;
      const marker = L.marker([h.LATITUDE, h.LONGITUDE])
        .bindPopup(popupContent);
      hospitalMarkers.push(marker);
      if (showPinsCheckbox.checked) marker.addTo(map);
    });
  });



    
    function updateHeatmap() {
      fetch('google_live_rainfall.json?v=' + Date.now()) 
       .then(res => res.json())
       .then(data => {
          const maxRainfall = Math.max(50,...data.map(p => p.rainfall_mm)); 
          const heatData = data.map(point => [
            point.LATITUDE,
            point.LONGITUDE,
            point.rainfall_mm / maxRainfall
          ]);

          if (heatmapLayer) map.removeLayer(heatmapLayer);
          heatmapLayer = L.heatLayer(heatData, {
            radius: 25,
            blur: 15,
            maxZoom: 10,
            gradient: {
              0.0: '#0000ff',
              0.2: '#00ffff',
              0.4: '#00ff00',
              0.6: '#ffff00',
              1.0: '#ff0000'
            }
          }).addTo(map);
          console.log(`Heatmap updated: ${data.length} points, max ${maxRainfall.toFixed(1)}mm`);
        })
       .catch(err => console.log('Error loading live_rainfall.json:', err));
    }

    updateHeatmap();

    showPinsCheckbox.addEventListener('change', (e) => {
      if (e.target.checked) {
        hospitalMarkers.forEach(m => m.addTo(map));
      } else {
        hospitalMarkers.forEach(m => map.removeLayer(m));
      }
    });

    refreshBtn.addEventListener('click', updateHeatmap);
  </script>
</body>
</html>