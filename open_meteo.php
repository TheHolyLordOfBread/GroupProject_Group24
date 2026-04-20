<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Hospital Weather Map</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f0f0f0; margin: 0; }
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
  <?php include 'navbar.php'; include 'BottomBar.php';?>
  <h1>Open Meteo Weather Map</h1>
  <div style="margin: 0 auto; display: block; text-align: center;">
    <label><input type="checkbox" id="show-pins" checked> Show Hospital Pins</label>
    <button id="refresh" style="margin-left:10px;">Refresh Data</button>
  </div>
  <div id="map"></div>
  <div id="legend" style="z-index: 10;">
    <h4>Rainfall (mm) Today</h4>
    <div style="display: flex; flex-direction: column;">
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #0000ff;"></div><span>0 - 15</span> 
      </div> 
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #0088ff;"></div><span>15-30</span> 
      </div> 
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #00ffff;"></div><span>30-45</span> 
      </div> 
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #00ff88;"></div><span>45-60</span> 
      </div> 
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #00ff00;"></div><span>60-75</span> 
      </div> 
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #88ff00;"></div><span>75-90</span> 
      </div> 
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #ffff00;"></div><span>90-120</span> 
      </div> 
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #ff8800;"></div><span>120-150</span> 
      </div>       
      <div style="display: flex; align-items: center; margin-bottom: 5px;"> 
        <div style="width: 20px; height: 20px; background-color: #ff0000;"></div><span>150+</span> 
      </div> 
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
  <script>
    var map = L.map('map').setView([4.2105, 101.9758], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap | Data: Open-Meteo'
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
      fetch('live_rainfall.json?v=' + Date.now()) 
       .then(res => res.json())
       .then(data => {
          const maxRainfall = 150;
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
              0.1: '#0088ff',
              0.2: '#00ffff',
              0.3: '#00ff88',
              0.4: '#00ff00',
              0.5: '#88ff00',
              0.6: '#ffff00',
              0.8: '#ff8800', 
              1.0: '#ff0000', 
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