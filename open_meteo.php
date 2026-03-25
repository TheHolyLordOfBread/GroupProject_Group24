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
</head>
<body>
    <h1>Hospital Live Weather Map</h1>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([4.2105, 101.9758], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        fetch('map_data.json')
            .then(response => response.json())
            .then(data => {
                data.forEach(hospital => {
                    var marker = L.marker([hospital.LATITUDE, hospital.LONGITUDE]).addTo(map);
                    marker.bindPopup(`
                        <b>${hospital.NAME}</b><br>
                        ${hospital.TYPE_OF_HOSPITAL} Hospital<br>
                        State: ${hospital.STATE}<br>
                        Rainfall: ${hospital.rainfall_in} inches<br>
                        Condition Code: ${hospital.condition_code}
                    `);
                });
            })
            .catch(error => console.log('Error fetching map data:', error));
    </script>
</body>
</html>