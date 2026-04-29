let combinedChart = null;

let nahrimKedahRows = [];
let nahrimSelangorRows = [];
let openMeteoRows = [];
let googleRows = [];

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
}

function getTodayDate() {
    const today = new Date();
    return today.toISOString().split("T")[0];
}

function getStartDateFromToday(days) {
    const today = new Date();
    today.setDate(today.getDate() - (days - 1));
    return today.toISOString().split("T")[0];
}

function loadCsv(filePath) {
    return new Promise((resolve) => {
        Papa.parse(filePath, {
            download: true,
            header: true,
            skipEmptyLines: true,
            complete: function (results) {
                resolve(results.data);
            },
            error: function (error) {
                console.error("Failed to load:", filePath, error);
                resolve([]);
            }
        });
    });
}

function updateSourcePanel(sourceValue) {
    document.getElementById("sourcePanelTitle").textContent = `${sourceValue.toUpperCase()} PANEL`;
    document.getElementById("chartBoxTitle").textContent = sourceValue;
}

function updateSourceCheckboxes(sourceValue) {
    document.getElementById("NAHRIM").checked = sourceValue === "NAHRIM";
    document.getElementById("OpenMeteo").checked = sourceValue === "OpenMeteo";
    document.getElementById("Google").checked = sourceValue === "Google";
}

function toggleScenarioVisibility(sourceValue) {
    const scenarioSelect = document.getElementById("Scenario");
    const scenarioLabel = document.getElementById("scenarioLabel");

    if (sourceValue === "NAHRIM") {
        scenarioSelect.style.display = "";
        scenarioLabel.style.display = "";
    } else {
        scenarioSelect.style.display = "none";
        scenarioLabel.style.display = "none";
    }
}

function filterFromTodayRange(series, days) {
    const startDate = getStartDateFromToday(days);
    const endDate = getTodayDate();

    const labels = [];
    const values = [];

    series.labels.forEach((date, index) => {
        if (date >= startDate && date <= endDate) {
            labels.push(date);
            values.push(series.values[index]);
        }
    });

    return { labels, values };
}

function buildNahrimRegionalAverageSeries(rows, scenarioColumn) {
    const groupedByDate = {};

    rows.forEach(row => {
        const value = parseFloat(row[scenarioColumn]);
        if (isNaN(value)) return;

        const date = formatDate(row.Year, row.Month, row.Day);

        if (!groupedByDate[date]) groupedByDate[date] = [];
        groupedByDate[date].push(value);
    });

    const sortedDates = Object.keys(groupedByDate).sort();

    return {
        labels: sortedDates,
        values: sortedDates.map(date => {
            const arr = groupedByDate[date];
            return +(arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(2);
        })
    };
}

function buildOpenMeteoRegionalAverageSeries(rows, targetState) {
    const groupedByDate = {};

    rows.forEach(row => {
        const state = String(row.State || "").trim().toUpperCase();
        const rainfall = parseFloat(row["Rainfall (mm)"]);

        if (state !== targetState || isNaN(rainfall)) return;

        const date = formatDate(row.Year, row.Month, row.Day);

        if (!groupedByDate[date]) groupedByDate[date] = [];
        groupedByDate[date].push(rainfall);
    });

    const sortedDates = Object.keys(groupedByDate).sort();

    return {
        labels: sortedDates,
        values: sortedDates.map(date => {
            const arr = groupedByDate[date];
            return +(arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(2);
        })
    };
}

function buildGoogleRegionalAverageSeries(rows, targetState) {
    const groupedByDate = {};

    rows.forEach(row => {
        const state = String(row.State || "").trim().toUpperCase();
        const rainfall = parseFloat(row["Rainfall  (mm)"] || row["Rainfall (mm)"] || row["rainfall_mm"]);

        if (state !== targetState || isNaN(rainfall)) return;

        const date = formatDate(row.year || row.Year, row.month || row.Month, row.day || row.Day);

        if (!groupedByDate[date]) groupedByDate[date] = [];
        groupedByDate[date].push(rainfall);
    });

    const sortedDates = Object.keys(groupedByDate).sort();

    return {
        labels: sortedDates,
        values: sortedDates.map(date => {
            const arr = groupedByDate[date];
            return +(arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(2);
        })
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

function buildChartOptions(titleText) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: titleText
            },
            legend: {
                display: true
            }
        },
        scales: {
            x: {
                ticks: {
                    autoSkip: false,
                    maxRotation: 45,
                    minRotation: 45
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
    };
}

function drawCombinedChart(canvas, title, labels, kedahValues, selangorValues) {
    if (!labels.length) {
        showNoDataMessage(canvas, "No data available for selected range");
        return;
    }

    const ctx = canvas.getContext("2d");

    if (combinedChart) {
        combinedChart.destroy();
    }

    combinedChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: `${title} Kedah Average`,
                    data: kedahValues,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                },
                {
                    label: `${title} Selangor Average`,
                    data: selangorValues,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                }
            ]
        },
        options: buildChartOptions(`${title} Rainfall (Kedah vs Selangor - Regional Average)`)
    });
}

function renderCombinedChart() {
    const sourceValue = document.getElementById("Source").value;
    const rangeValue = parseInt(document.getElementById("Range").value, 10);
    const scenarioValue = document.getElementById("Scenario").value;
    const canvas = document.getElementById("combinedRainfallChart");

    updateSourcePanel(sourceValue);
    updateSourceCheckboxes(sourceValue);
    toggleScenarioVisibility(sourceValue);

    let kedahFull;
    let selangorFull;

    if (sourceValue === "OpenMeteo") {
        kedahFull = buildOpenMeteoRegionalAverageSeries(openMeteoRows, "KEDAH");
        selangorFull = buildOpenMeteoRegionalAverageSeries(openMeteoRows, "SELANGOR");
    } else if (sourceValue === "Google") {
        kedahFull = buildGoogleRegionalAverageSeries(googleRows, "KEDAH");
        selangorFull = buildGoogleRegionalAverageSeries(googleRows, "SELANGOR");
    } else {
        kedahFull = buildNahrimRegionalAverageSeries(nahrimKedahRows, scenarioValue);
        selangorFull = buildNahrimRegionalAverageSeries(nahrimSelangorRows, scenarioValue);
    }

    const kedahSeries = filterFromTodayRange(kedahFull, rangeValue);
    const selangorSeries = filterFromTodayRange(selangorFull, rangeValue);

    drawCombinedChart(
        canvas,
        sourceValue,
        kedahSeries.labels,
        kedahSeries.values,
        selangorSeries.values
    );
}

document.addEventListener("DOMContentLoaded", async function () {
    nahrimKedahRows = await loadCsv("data/RF_Kedah_Mac_May_2026_AveRCP.csv?v=2000");
    nahrimSelangorRows = await loadCsv("data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv?v=2000");
    openMeteoRows = await loadCsv("data/openmeteo.csv?v=2000");
    googleRows = await loadCsv("data/daily_rainfall_data_Google.csv?v=2000");

    renderCombinedChart();

    document.getElementById("Source").addEventListener("change", renderCombinedChart);
    document.getElementById("Range").addEventListener("change", renderCombinedChart);
    document.getElementById("Scenario").addEventListener("change", renderCombinedChart);
});