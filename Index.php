<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>

<<<<<<< Updated upstream
    <link rel="stylesheet" href="css/style.css?v=1">
=======
    <link rel="stylesheet" href="css/style.css?v=40">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://pyscript.net/releases/2026.3.1/core.css" />
    <script type="module" src="https://pyscript.net/releases/2026.3.1/core.js"></script>
    <script defer src="js/index.js?v=40"></script>
>>>>>>> Stashed changes
</head>
<body>
    <script src="https://cdn.jsdelivr.net/pyodide/v0.29.3/full/pyodide.js"></script>
   
    <script type="text/javascript">
      async function main() {
        let pyodide = await loadPyodide();
        await pyodide.loadPackage("micropip");
        const micropip = pyodide.pyimport("micropip");
        await micropip.install("sqlite3");
        await micropip.install("requests");

        await pyodide.runPython(`
        import sqlite3
        import requests
      `);
        
        try {
        const response = await fetch("data.py");
        if (!response.ok) {
          throw new Error(`Failed to load script: ${response.statusText}`);
        }
        const pythonCode = await response.text();

        // Run the Python code
        const result = await pyodide.runPythonAsync(pythonCode);

        // Display result
        document.getElementById("results").textContent = `Script executed successfully.\nResult: ${result}`;
      } catch (err) {
        document.getElementById("results").textContent = `Error: ${err.message}`;
      }
        

      }
      main();
    </script>


 
<div id="results">apple sauce</div>





<?php include 'navbar.php'; ?>

<div class="page-content">
    <h1>Index</h1>

    <div class="box-row">
        <div class="box">Hospitals</div>
        <div class="box">Rainfall</div>
        <div class="box">Map</div>
    </div>
</div>

<<<<<<< Updated upstream
=======
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

    fetch('map_data.json')
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



>>>>>>> Stashed changes
</body>
</html>