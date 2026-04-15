let combinedChart = null;
let kedahRows = [];
let selangorRows = [];

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
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

function updateSourcePanel(sourceValue) {
    const panelTitle = document.getElementById("sourcePanelTitle");

    if (!panelTitle) return;

    if (sourceValue === "NAHRIM") {
        panelTitle.textContent = "NAHRIM PANEL";
    } else if (sourceValue === "OpenMeteo") {
        panelTitle.textContent = "OPENMETEO PANEL";
    } else if (sourceValue === "Google") {
        panelTitle.textContent = "GOOGLE PANEL";
    } else {
        panelTitle.textContent = "COMBINED PANEL";
    }
}

function updateSourceCheckboxes(sourceValue) {
    const nahrim = document.getElementById("NAHRIM");
    const openMeteo = document.getElementById("OpenMeteo");
    const google = document.getElementById("Google");

    if (!nahrim || !openMeteo || !google) return;

    nahrim.checked = false;
    openMeteo.checked = false;
    google.checked = false;

    if (sourceValue === "NAHRIM") {
        nahrim.checked = true;
    } else if (sourceValue === "OpenMeteo") {
        openMeteo.checked = true;
    } else if (sourceValue === "Google") {
        google.checked = true;
    } else {
        nahrim.checked = true;
        openMeteo.checked = true;
        google.checked = true;
    }
}

function renderCombinedChart() {
    const sourceValue = document.getElementById("Source").value;
    const kedahValue = document.getElementById("kedahLocation").value;
    const selangorValue = document.getElementById("selangorLocation").value;

    if (!kedahValue || !selangorValue) return;

    const [kedahLat, kedahLon] = kedahValue.split(",");
    const [selangorLat, selangorLon] = selangorValue.split(",");

    const kedahSeries = getLocationSeries(kedahRows, kedahLat, kedahLon, "Ave_RCP4.5");
    const selangorSeries = getLocationSeries(selangorRows, selangorLat, selangorLon, "Ave_RCP4.5");

    const canvas = document.getElementById("combinedRainfallChart");
    const ctx = canvas.getContext("2d");

    if (combinedChart) {
        combinedChart.destroy();
    }

    combinedChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: kedahSeries.labels,
            datasets: [
                {
                    label: `${sourceValue} Kedah - Ave_RCP4.5`,
                    data: kedahSeries.values,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                },
                {
                    label: `${sourceValue} Selangor - Ave_RCP4.5`,
                    data: selangorSeries.values,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: "Combined Rainfall Comparison"
                },
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
        }
    });

    updateSourcePanel(sourceValue);
    updateSourceCheckboxes(sourceValue);
}

function loadCsv(filePath, callback) {
    Papa.parse(filePath, {
        download: true,
        header: true,
        skipEmptyLines: true,
        complete: function(results) {
            callback(results.data);
        },
        error: function(error) {
            console.error("Papa Parse error:", error);
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    loadCsv("data/RF_Kedah_Mac_May_2026_AveRCP.csv", function(kedahData) {
        kedahRows = kedahData;

        loadCsv("data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv", function(selangorData) {
            selangorRows = selangorData;

            const kedahLocations = getUniqueLocations(kedahRows);
            const selangorLocations = getUniqueLocations(selangorRows);

            populateLocationDropdown("kedahLocation", kedahLocations);
            populateLocationDropdown("selangorLocation", selangorLocations);

            renderCombinedChart();

            document.getElementById("kedahLocation").addEventListener("change", renderCombinedChart);
            document.getElementById("selangorLocation").addEventListener("change", renderCombinedChart);
            document.getElementById("Source").addEventListener("change", renderCombinedChart);
        });
    });
});