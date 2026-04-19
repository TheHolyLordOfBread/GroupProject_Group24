let combinedChart = null;

let nahrimKedahRows = [];
let nahrimSelangorRows = [];
let openMeteoRows = [];
let googleRows = [];

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
}

function loadCsv(filePath, callback, errorCallback = null) {
    Papa.parse(filePath, {
        download: true,
        header: true,
        skipEmptyLines: true,
        complete: function (results) {
            callback(results.data);
        },
        error: function (error) {
            console.error("Papa Parse error for " + filePath + ":", error);
            if (errorCallback) {
                errorCallback(error);
            }
        }
    });
}

function updateSourcePanel(sourceValue) {
    const panelTitle = document.getElementById("sourcePanelTitle");
    const chartBoxTitle = document.getElementById("chartBoxTitle");

    if (panelTitle) {
        panelTitle.textContent = `${sourceValue.toUpperCase()} PANEL`;
    }

    if (chartBoxTitle) {
        chartBoxTitle.textContent = sourceValue;
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
    }
}

function toggleScenarioVisibility(sourceValue) {
    const scenarioSelect = document.getElementById("Scenario");
    const scenarioLabel = document.querySelector('label[for="Scenario"]');

    if (!scenarioSelect || !scenarioLabel) return;

    if (sourceValue === "NAHRIM") {
        scenarioSelect.style.display = "";
        scenarioLabel.style.display = "";
    } else {
        scenarioSelect.style.display = "none";
        scenarioLabel.style.display = "none";
    }
}

function buildNahrimRegionalAverageSeries(rows, scenarioColumn) {
    const groupedByDate = {};

    rows.forEach(row => {
        const value = parseFloat(row[scenarioColumn]);
        if (isNaN(value)) return;

        const date = formatDate(row.Year, row.Month, row.Day);

        if (!groupedByDate[date]) {
            groupedByDate[date] = [];
        }

        groupedByDate[date].push(value);
    });

    const sortedDates = Object.keys(groupedByDate).sort();

    return {
        labels: sortedDates,
        values: sortedDates.map(date => {
            const arr = groupedByDate[date];
            const sum = arr.reduce((a, b) => a + b, 0);
            return sum / arr.length;
        })
    };
}

function buildOpenMeteoRegionalAverageSeries(rows, targetState) {
    const groupedByDate = {};

    rows.forEach(row => {
        const state = String(row.State || "").trim().toUpperCase();
        const rainfall = parseFloat(row["Rainfall (mm)"]);

        if (state !== targetState) return;
        if (isNaN(rainfall)) return;

        const date = formatDate(row.Year, row.Month, row.Day);

        if (!groupedByDate[date]) {
            groupedByDate[date] = [];
        }

        groupedByDate[date].push(rainfall);
    });

    const sortedDates = Object.keys(groupedByDate).sort();

    return {
        labels: sortedDates,
        values: sortedDates.map(date => {
            const arr = groupedByDate[date];
            const sum = arr.reduce((a, b) => a + b, 0);
            return sum / arr.length;
        })
    };
}

function buildGoogleRegionalAverageSeries(rows, targetState) {
    const groupedByDate = {};

    rows.forEach(row => {
        const state = String(row.State || "").trim().toUpperCase();
        const rainfall = parseFloat(row["Rainfall  (mm)"]);

        if (state !== targetState) return;
        if (isNaN(rainfall)) return;

        const date = formatDate(row.year, row.month, row.day);

        if (!groupedByDate[date]) {
            groupedByDate[date] = [];
        }

        groupedByDate[date].push(rainfall);
    });

    const sortedDates = Object.keys(groupedByDate).sort();

    return {
        labels: sortedDates,
        values: sortedDates.map(date => {
            const arr = groupedByDate[date];
            const sum = arr.reduce((a, b) => a + b, 0);
            return sum / arr.length;
        })
    };
}

function filterLastNDays(series, days) {
    return {
        labels: series.labels.slice(-days),
        values: series.values.slice(-days)
    };
}

function showNoDataMessage(canvas, message) {
    if (combinedChart) {
        combinedChart.destroy();
        combinedChart = null;
    }

    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();
    ctx.font = "18px Arial";
    ctx.fillStyle = "#333";
    ctx.textAlign = "center";
    ctx.fillText(message, canvas.width / 2, canvas.height / 2);
    ctx.restore();
}

function renderNahrimChart(rangeValue, scenarioValue, canvas) {
    const kedahSeriesFull = buildNahrimRegionalAverageSeries(nahrimKedahRows, scenarioValue);
    const selangorSeriesFull = buildNahrimRegionalAverageSeries(nahrimSelangorRows, scenarioValue);

    const kedahSeries = filterLastNDays(kedahSeriesFull, rangeValue);
    const selangorSeries = filterLastNDays(selangorSeriesFull, rangeValue);

    if (!kedahSeries.labels.length || !selangorSeries.labels.length) {
        showNoDataMessage(canvas, "No NAHRIM data available");
        return;
    }

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
                    label: `NAHRIM Kedah Average - ${scenarioValue}`,
                    data: kedahSeries.values,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                },
                {
                    label: `NAHRIM Selangor Average - ${scenarioValue}`,
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
                    text: "NAHRIM Rainfall (Kedah vs Selangor - Regional Average)"
                },
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxTicksLimit: 6
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
}

function renderOpenMeteoChart(rangeValue, canvas) {
    const kedahSeriesFull = buildOpenMeteoRegionalAverageSeries(openMeteoRows, "KEDAH");
    const selangorSeriesFull = buildOpenMeteoRegionalAverageSeries(openMeteoRows, "SELANGOR");

    const kedahSeries = filterLastNDays(kedahSeriesFull, rangeValue);
    const selangorSeries = filterLastNDays(selangorSeriesFull, rangeValue);

    if (!kedahSeries.labels.length || !selangorSeries.labels.length) {
        showNoDataMessage(canvas, "OpenMeteo data not found");
        return;
    }

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
                    label: "OpenMeteo Kedah Average",
                    data: kedahSeries.values,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                },
                {
                    label: "OpenMeteo Selangor Average",
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
                    text: "OpenMeteo Rainfall (Kedah vs Selangor - Regional Average)"
                },
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxTicksLimit: 6
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Rainfall (mm)"
                    }
                }
            }
        }
    });
}

function renderGoogleChart(rangeValue, canvas) {
    const kedahSeriesFull = buildGoogleRegionalAverageSeries(googleRows, "KEDAH");
    const selangorSeriesFull = buildGoogleRegionalAverageSeries(googleRows, "SELANGOR");

    const kedahSeries = filterLastNDays(kedahSeriesFull, rangeValue);
    const selangorSeries = filterLastNDays(selangorSeriesFull, rangeValue);

    if (!kedahSeries.labels.length || !selangorSeries.labels.length) {
        showNoDataMessage(canvas, "Google data not found");
        return;
    }

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
                    label: "Google Kedah Average",
                    data: kedahSeries.values,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                },
                {
                    label: "Google Selangor Average",
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
                    text: "Google Rainfall (Kedah vs Selangor - Regional Average)"
                },
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxTicksLimit: 6
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Rainfall (mm)"
                    }
                }
            }
        }
    });
}

function renderCombinedChart() {
    const sourceValue = document.getElementById("Source")?.value || "NAHRIM";
    const rangeValue = parseInt(document.getElementById("Range")?.value || "7", 10);
    const scenarioValue = document.getElementById("Scenario")?.value || "Ave_RCP4.5";
    const canvas = document.getElementById("combinedRainfallChart");

    if (!canvas) return;

    updateSourcePanel(sourceValue);
    updateSourceCheckboxes(sourceValue);
    toggleScenarioVisibility(sourceValue);

    if (sourceValue === "OpenMeteo") {
        renderOpenMeteoChart(rangeValue, canvas);
        return;
    }

    if (sourceValue === "Google") {
        renderGoogleChart(rangeValue, canvas);
        return;
    }

    renderNahrimChart(rangeValue, scenarioValue, canvas);
}

document.addEventListener("DOMContentLoaded", function () {
    loadCsv("data/RF_Kedah_Mac_May_2026_AveRCP.csv", function (kedahData) {
        nahrimKedahRows = kedahData;

        loadCsv("data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv", function (selangorData) {
            nahrimSelangorRows = selangorData;

            renderCombinedChart();

            loadCsv(
                "data/openmeteo.csv",
                function (openMeteoData) {
                    openMeteoRows = openMeteoData;
                },
                function () {
                    console.warn("OpenMeteo file could not be loaded.");
                }
            );

            loadCsv(
                "data/google.csv",
                function (googleData) {
                    googleRows = googleData;
                },
                function () {
                    console.warn("Google file could not be loaded.");
                }
            );

            document.getElementById("Source")?.addEventListener("change", renderCombinedChart);
            document.getElementById("Range")?.addEventListener("change", renderCombinedChart);
            document.getElementById("Scenario")?.addEventListener("change", renderCombinedChart);
        });
    });
});