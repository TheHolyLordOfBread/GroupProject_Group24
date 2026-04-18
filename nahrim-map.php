<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hospital Weather Map</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f0f0f0; margin: 0; padding: 20px; }
    h1 { text-align: center; color: #333; }
    #map { height: 600px; width: 80%; margin: 20px auto; }
  </style>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css" />
  <link rel="stylesheet" href="css/style.css?v=1" />
</head>
<body>
  <?php include 'navbar.php'; ?>
  <h1>Nahrim Weather Map</h1>
  <h2>Rainfall Data for Malaysia</h2>
  <select id="rcp-select" style="margin: 0 auto; display: block;">
    <option value="Ave_RCP2.6">RCP 2.6</option>
    <option value="Ave_RCP4.5">RCP 4.5</option>
    <option value="Ave_RCP6.0">RCP 6.0</option>
    <option value="Ave_RCP8.5">RCP 8.5</option>
  </select>
  <script>
       window.onload = function()
        {
          rcpselect = document.getElementById("rcp-select");
          RCP = rcpselect.value;
        }



        function selectRedirect() {
          rcpselect = document.getElementById("redirectDropdown");
          RCP = rcpselect.value;
          
        }
  </script>

  <div style="margin: 0 auto; display: block; text-align: center;">
    <label><input type="checkbox" id="show-pins" checked> Show Hospital Pins</label>
  </div>
  <div id="map"></div>
  <div id="legend" style="position: absolute; top: 100px; right: 20px; background-color: #fff; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: Arial, sans-serif;">
    <h4>Rainfall (mm) Legend</h4>
    <div style="display: flex; flex-direction: column;">
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #0000ff;"></div>
        <span>0 - 10</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #00ffff;"></div>
        <span>10 - 20</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #00ff00;"></div>
        <span>20 - 30</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #ffff00;"></div>
        <span>30 - 40</span>
      </div>
      <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <div style="width: 20px; height: 20px; background-color: #ff0000;"></div>
        <span>40+</span>
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
      `;
      const marker = L.marker([h.LATITUDE, h.LONGITUDE])
        .bindPopup(popupContent);
      hospitalMarkers.push(marker);
      if (showPinsCheckbox.checked) marker.addTo(map);
    });
  });



    
    function updateHeatmap(RCP) {
      var rcp = RCP;
      fetch('nahrim_data.json?v=' + Date.now()) 
       .then(res => res.json())
       .then(data => {
          const maxRainfall = 50
          const heatData = data.map(point => [
            point.LATITUDE,
            point.LONGITUDE,
            switch(rcp)
            {
              case "Ave_RCP2.6": point.Ave_RCP26 / maxRainfall; break;
              case "Ave_RCP4.5": point.Ave_RCP45 / maxRainfall; break;
              case "Ave_RCP6.0": point.Ave_RCP60 / maxRainfall; break;              
              case "Ave_RCP8.5": point.Ave_RCP85 / maxRainfall; break;
              default; break;
            }
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

    updateHeatmap(RCP);

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