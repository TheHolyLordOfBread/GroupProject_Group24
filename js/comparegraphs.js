let compareNahrimChart = null;
let compareOpenMeteoChart = null;
let compareGoogleChart = null;

let nahrimKedahRows = [];
let nahrimSelangorRows = [];
let openMeteoRows = [];
let googleRows = [];

const fixedStartDate = "2026-04-24";

function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
}

function loadCsv(filePath, callback) {
    Papa.parse(filePath, {
        download: true,
        header: true,
        skipEmptyLines: true,
        complete: function (results) {
            callback(results.data);
        },
        error: function (error) {
            console.error("Papa Parse error:", error);
        }
    });
}

function filterFromFixedStartDate(series, days, startDate) {
    const startIndex = series.labels.indexOf(startDate);

    if (startIndex === -1) {
        return { labels: [], values: [] };
    }

    const endIndex = startIndex + days;

    return {
        labels: series.labels.slice(startIndex, endIndex),
        values: series.values.slice(startIndex, endIndex)
    };
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

        const rainfall =
            parseFloat(row["Rainfall  (mm)"]) ||
            parseFloat(row["Rainfall (mm)"]) ||
            parseFloat(row["rainfall_mm"]);

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

function buildChartOptions(titleText) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: { display: true, text: titleText },
            legend: { display: true }
        },
        scales: {
            x: {
                ticks: { maxRotation: 45, minRotation: 45 }
            },
            y: {
                beginAtZero: true,
                title: { display: true, text: "Rainfall (mm)" }
            }
        }
    };
}

function drawChart(canvasId, oldChart, title, labels, kedahValues, selangorValues) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    const ctx = canvas.getContext("2d");

    if (oldChart) oldChart.destroy();

    return new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: `${title} Kedah`,
                    data: kedahValues,
                    borderWidth: 2,
                    tension: 0.2
                },
                {
                    label: `${title} Selangor`,
                    data: selangorValues,
                    borderWidth: 2,
                    tension: 0.2
                }
            ]
        },
        options: buildChartOptions(`${title} Rainfall`)
    });
}

function renderCompareCharts() {
    const rangeValue = parseInt(document.getElementById("compareRange").value, 10);
    const scenarioValue = document.getElementById("compareScenario").value;

    const nahrimKedah = filterFromFixedStartDate(
        buildNahrimRegionalAverageSeries(nahrimKedahRows, scenarioValue),
        rangeValue,
        fixedStartDate
    );

    const nahrimSelangor = filterFromFixedStartDate(
        buildNahrimRegionalAverageSeries(nahrimSelangorRows, scenarioValue),
        rangeValue,
        fixedStartDate
    );

    const openKedah = filterFromFixedStartDate(
        buildOpenMeteoRegionalAverageSeries(openMeteoRows, "KEDAH"),
        rangeValue,
        fixedStartDate
    );

    const openSelangor = filterFromFixedStartDate(
        buildOpenMeteoRegionalAverageSeries(openMeteoRows, "SELANGOR"),
        rangeValue,
        fixedStartDate
    );

    const googleKedah = filterFromFixedStartDate(
        buildGoogleRegionalAverageSeries(googleRows, "KEDAH"),
        rangeValue,
        fixedStartDate
    );

    const googleSelangor = filterFromFixedStartDate(
        buildGoogleRegionalAverageSeries(googleRows, "SELANGOR"),
        rangeValue,
        fixedStartDate
    );

    compareNahrimChart = drawChart(
        "compareNahrimChart",
        compareNahrimChart,
        "NAHRIM",
        nahrimKedah.labels,
        nahrimKedah.values,
        nahrimSelangor.values
    );

    compareOpenMeteoChart = drawChart(
        "compareOpenMeteoChart",
        compareOpenMeteoChart,
        "OpenMeteo",
        openKedah.labels,
        openKedah.values,
        openSelangor.values
    );

    compareGoogleChart = drawChart(
        "compareGoogleChart",
        compareGoogleChart,
        "Google",
        googleKedah.labels,
        googleKedah.values,
        googleSelangor.values
    );
}

document.addEventListener("DOMContentLoaded", function () {
    loadCsv("data/RF_Kedah_Mac_May_2026_AveRCP.csv?v=1000", function (kedahData) {
        nahrimKedahRows = kedahData;

        loadCsv("data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv?v=1000", function (selangorData) {
            nahrimSelangorRows = selangorData;

            loadCsv("data/openmeteo.csv?v=1000", function (openData) {
                openMeteoRows = openData;

                loadCsv("data/daily_rainfall_data_Google.csv?v=1000", function (googleData) {
                    googleRows = googleData;

                    renderCompareCharts();

                    document.getElementById("compareRange").addEventListener("change", renderCompareCharts);
                    document.getElementById("compareScenario").addEventListener("change", renderCompareCharts);
                });
            });
        });
    });
});