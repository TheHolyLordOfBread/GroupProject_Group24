
<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< Updated upstream
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphs</title>
    <link rel="stylesheet" href="css/style.css?v=22">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="actionbar">
    <div class="imageBox" style="display: inline-flex;">
        <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
=======
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
>>>>>>> Stashed changes
    </div>
  </div>
  <script>
    let kedahRows = [];
    let slgrRows = [];
    let kedahChart = null;
    let slgrChart = null;

<<<<<<< Updated upstream
    <form action="#" method="POST">
        <label for="graphRange">Select Range:</label>
        <select name="graphRange" id="graphRange">
            <option value="Range1">&lt;1 day</option>
            <option value="Range2">&lt;1 week</option>
            <option value="Range3">&lt;2 weeks</option>
            <option value="Range4">&lt;4 weeks</option>
        </select>
    </form>
</div>

<div class="page-content">
    <div class="box-row" style="width: 100%;">
        <div class="box">
            <h2>RAINFALL GRAPHS PAGE</h2>

            <div class="chart-controls">
                <label for="graphState">State:</label>
                <select id="graphState">
                    <option value="kedah">Kedah</option>
                    <option value="selangor">Selangor</option>
                </select>

                <label for="graphLocation">Latitude / Longitude:</label>
                <select id="graphLocation">
                    <option>Loading data...</option>
                </select>

                <label for="graphScenario">Scenario:</label>
                <select id="graphScenario">
                    <option value="Ave_RCP2.6">RCP 2.6</option>
                    <option value="Ave_RCP4.5" selected>RCP 4.5</option>
                    <option value="Ave_RCP6.0">RCP 6.0</option>
                    <option value="Ave_RCP8.5">RCP 8.5</option>
                </select>
            </div>

            <div class="chart-container">
                <canvas id="graphsPageChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include 'BottomBar.php'; ?>

<script>
let graphsPageChart = null;
let kedahRows = [];
let selangorRows = [];

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
}

function getUniqueLocations(rows) {
    const map = new Map();

    rows.forEach(row => {
=======
    function formatDate(year, month, day) {
      return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    }

    function getUniqueLocations(rows) {
      const map = new Map();
      rows.forEach(row => {
>>>>>>> Stashed changes
        const lat = row.Latitude;
        const lon = row.Longitude;
        const key = `${lat},${lon}`;
        if (!map.has(key)) {
          map.set(key, { lat, lon });
        }
      });
      return Array.from(map.values());
    }

<<<<<<< Updated upstream
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

function renderGraphsPageChart() {
    const state = document.getElementById("graphState").value;
    const locationValue = document.getElementById("graphLocation").value;
    const scenario = document.getElementById("graphScenario").value;

    if (!locationValue) return;

    const [lat, lon] = locationValue.split(",");
    const rows = state === "kedah" ? kedahRows : selangorRows;
    const series = getLocationSeries(rows, lat, lon, scenario);

    const ctx = document.getElementById("graphsPageChart").getContext("2d");

    if (graphsPageChart) {
        graphsPageChart.destroy();
    }

    graphsPageChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: series.labels,
            datasets: [{
                label: `${state.charAt(0).toUpperCase() + state.slice(1)} Rainfall (${scenario.replace("Ave_", "").replace("RCP", "RCP ")})`,
                data: series.values,
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
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Rainfall"
                    }
                }
            }
=======
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
      const filtered = rows.filter(row => String(row.Latitude) === String(lat) && String(row.Longitude) === String(lon));
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
          plugins: { legend: { display: true } },
          scales: { x: { ticks: { maxTicksLimit: 8 } }, y: { beginAtZero: true } }
>>>>>>> Stashed changes
        }
      });
    }

<<<<<<< Updated upstream
function refreshLocationsAndChart() {
    const state = document.getElementById("graphState").value;
    const locations = state === "kedah" ? getUniqueLocations(kedahRows) : getUniqueLocations(selangorRows);

    populateLocationDropdown("graphLocation", locations);
    renderGraphsPageChart();
}
=======
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
>>>>>>> Stashed changes

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

    loadCsv('data/RF_Kedah_Mac_May_2026_AveRCP.csv', function(data) {
      kedahRows = data;
      const locations = getUniqueLocations(kedahRows);
      populateLocationDropdown('kedahLocation', locations);
      updateKedahChart();
      document.getElementById('kedahLocation').addEventListener('change', updateKedahChart);
      document.getElementById('kedahRcp').addEventListener('change', updateKedahChart);
    });
<<<<<<< Updated upstream
}

loadCsv("data/RF_Kedah_Mac_May_2026_AveRCP.csv", function(data) {
    kedahRows = data;

    loadCsv("data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv", function(data2) {
        selangorRows = data2;

        refreshLocationsAndChart();

        document.getElementById("graphState").addEventListener("change", refreshLocationsAndChart);
        document.getElementById("graphLocation").addEventListener("change", renderGraphsPageChart);
        document.getElementById("graphScenario").addEventListener("change", renderGraphsPageChart);
    });
});
</script>
=======
>>>>>>> Stashed changes

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