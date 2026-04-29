let compareNahrimChart = null;
let compareOpenMeteoChart = null;
let compareGoogleChart = null;

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
            callback([]);
        }
    });
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
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                },
                {
                    label: `${title} Selangor`,
                    data: selangorValues,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 3
                }
            ]
        },
        options: buildChartOptions(`${title} Rainfall`)
    });
}

function renderCompareCharts() {
    const rangeValue = parseInt(document.getElementById("compareRange").value, 10);
    const scenarioValue = document.getElementById("compareScenario").value;

    const nahrimKedah = filterFromTodayRange(
        buildNahrimRegionalAverageSeries(nahrimKedahRows, scenarioValue),
        rangeValue
    );

    const nahrimSelangor = filterFromTodayRange(
        buildNahrimRegionalAverageSeries(nahrimSelangorRows, scenarioValue),
        rangeValue
    );

    const openKedah = filterFromTodayRange(
        buildOpenMeteoRegionalAverageSeries(openMeteoRows, "KEDAH"),
        rangeValue
    );

    const openSelangor = filterFromTodayRange(
        buildOpenMeteoRegionalAverageSeries(openMeteoRows, "SELANGOR"),
        rangeValue
    );

    const googleKedah = filterFromTodayRange(
        buildGoogleRegionalAverageSeries(googleRows, "KEDAH"),
        rangeValue
    );

    const googleSelangor = filterFromTodayRange(
        buildGoogleRegionalAverageSeries(googleRows, "SELANGOR"),
        rangeValue
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
    loadCsv("data/RF_Kedah_Mac_May_2026_AveRCP.csv?v=2000", function (kedahData) {
        nahrimKedahRows = kedahData;

        loadCsv("data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv?v=2000", function (selangorData) {
            nahrimSelangorRows = selangorData;

            loadCsv("data/openmeteo.csv?v=2000", function (openData) {
                openMeteoRows = openData;

                loadCsv("data/daily_rainfall_data_Google.csv?v=2000", function (googleData) {
                    googleRows = googleData;

                    renderCompareCharts();

                    document.getElementById("compareRange").addEventListener("change", renderCompareCharts);
                    document.getElementById("compareScenario").addEventListener("change", renderCompareCharts);
                });
            });
        });
    });
});