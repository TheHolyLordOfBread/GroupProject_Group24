<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphs</title>

    <link rel="stylesheet" href="css/style.css?v=12">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-content">

    <div class="nahrim-banner">
        <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
        <div class="banner-line"></div>
    </div>

    <h2 class="graphs-title">Rainfall Data by Specific Location</h2>

    <div class="graphs-grid rainfall-grid">

        <div class="graph-card">
            <h3>Kedah Rainfall</h3>

            <div class="graph-controls">
                <select id="kedahLocation"></select>
                <select id="kedahRcp">
                    <option value="Ave_RCP2.6">RCP 2.6</option>
                    <option value="Ave_RCP4.5" selected>RCP 4.5</option>
                    <option value="Ave_RCP6.0">RCP 6.0</option>
                    <option value="Ave_RCP8.5">RCP 8.5</option>
                </select>
            </div>

            <canvas id="rfKedahChart"></canvas>
        </div>

        <div class="graph-card">
            <h3>Selangor / Wilayah Rainfall</h3>

            <div class="graph-controls">
                <select id="slgrLocation"></select>
                <select id="slgrRcp">
                    <option value="Ave_RCP2.6">RCP 2.6</option>
                    <option value="Ave_RCP4.5" selected>RCP 4.5</option>
                    <option value="Ave_RCP6.0">RCP 6.0</option>
                    <option value="Ave_RCP8.5">RCP 8.5</option>
                </select>
            </div>

            <canvas id="rfSlgrChart"></canvas>
        </div>

    </div>

</div>

<script>
let kedahRows = [];
let slgrRows = [];
let kedahChart = null;
let slgrChart = null;

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}

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
    select.innerHTML = '';

    locations.forEach(loc => {
        const option = document.createElement('option');
        option.value = `${loc.lat},${loc.lon}`;
        option.textContent = `${loc.lat}, ${loc.lon}`;
        select.appendChild(option);
    });
}

function getLocationSeries(rows, lat, lon, rcpColumn) {
    const filtered = rows.filter(row =>
        String(row.Latitude) === String(lat) &&
        String(row.Longitude) === String(lon)
    );

    const labels = [];
    const values = [];

    filtered.forEach(row => {
        const value = parseFloat(row[rcpColumn]);

        if (!isNaN(value)) {
            labels.push(formatDate(row.Year, row.Month, row.Day));
            values.push(value);
        }
    });

    return { labels, values };
}

function buildChart(chartRef, canvasId, labels, values, labelText) {
    const ctx = document.getElementById(canvasId).getContext('2d');

    if (chartRef) {
        chartRef.destroy();
    }

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: labelText,
                data: values,
                borderWidth: 2,
                tension: 0.2,
                fill: false,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxTicksLimit: 8
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function updateKedahChart() {
    const locValue = document.getElementById('kedahLocation').value;
    const rcpValue = document.getElementById('kedahRcp').value;

    const [lat, lon] = locValue.split(',');
    const series = getLocationSeries(kedahRows, lat, lon, rcpValue);

    kedahChart = buildChart(
        kedahChart,
        'rfKedahChart',
        series.labels,
        series.values,
        `Kedah Rainfall (${rcpValue.replace('Ave_', '').replace('RCP', 'RCP ')})`
    );
}

function updateSlgrChart() {
    const locValue = document.getElementById('slgrLocation').value;
    const rcpValue = document.getElementById('slgrRcp').value;

    const [lat, lon] = locValue.split(',');
    const series = getLocationSeries(slgrRows, lat, lon, rcpValue);

    slgrChart = buildChart(
        slgrChart,
        'rfSlgrChart',
        series.labels,
        series.values,
        `Selangor Rainfall (${rcpValue.replace('Ave_', '').replace('RCP', 'RCP ')})`
    );
}

function loadCsv(filePath, callback) {
    Papa.parse(filePath, {
        download: true,
        header: true,
        skipEmptyLines: true,
        complete: function(results) {
            callback(results.data);
        }
    });
}

loadCsv('data/RF_Kedah_Mac_May_2026_AveRCP.csv', function(data) {
    kedahRows = data;
    const locations = getUniqueLocations(kedahRows);
    populateLocationDropdown('kedahLocation', locations);
    updateKedahChart();

    document.getElementById('kedahLocation').addEventListener('change', updateKedahChart);
    document.getElementById('kedahRcp').addEventListener('change', updateKedahChart);
});

loadCsv('data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv', function(data) {
    slgrRows = data;
    const locations = getUniqueLocations(slgrRows);
    populateLocationDropdown('slgrLocation', locations);
    updateSlgrChart();

    document.getElementById('slgrLocation').addEventListener('change', updateSlgrChart);
    document.getElementById('slgrRcp').addEventListener('change', updateSlgrChart);
});
</script>

</body>
</html>