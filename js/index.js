let kedahChart;
let selangorChart;

function parseCSV(text) {
    const lines = text.trim().split("\n");
    const headers = lines[0].split(",");

    return lines.slice(1).map(line => {
        const values = line.split(",");
        const row = {};

        headers.forEach((header, index) => {
            row[header.trim()] = values[index].trim();
        });

        return row;
    });
}

function groupByLocation(data) {
    const grouped = {};

    data.forEach(row => {
        const key = `${row.Latitude}, ${row.Longitude}`;

        if (!grouped[key]) {
            grouped[key] = [];
        }

        grouped[key].push({
            date: `${row.Year}-${String(row.Month).padStart(2, "0")}-${String(row.Day).padStart(2, "0")}`,
            rcp26: parseFloat(row["Ave_RCP2.6"]),
            rcp45: parseFloat(row["Ave_RCP4.5"]),
            rcp60: parseFloat(row["Ave_RCP6.0"]),
            rcp85: parseFloat(row["Ave_RCP8.5"])
        });
    });

    return grouped;
}

function fillLocationDropdown(selectElement, groupedData) {
    selectElement.innerHTML = "";

    Object.keys(groupedData).forEach(locationKey => {
        const option = document.createElement("option");
        option.value = locationKey;
        option.textContent = locationKey;
        selectElement.appendChild(option);
    });
}

function createOrUpdateChart(chartRef, canvasId, locationKey, groupedData, chartTitle) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return chartRef;

    const ctx = canvas.getContext("2d");
    const rows = groupedData[locationKey];

    const labels = rows.map(item => item.date);
    const rcp26 = rows.map(item => item.rcp26);
    const rcp45 = rows.map(item => item.rcp45);
    const rcp60 = rows.map(item => item.rcp60);
    const rcp85 = rows.map(item => item.rcp85);

    if (chartRef) {
        chartRef.destroy();
    }

    return new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Ave_RCP2.6",
                    data: rcp26,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false
                },
                {
                    label: "Ave_RCP4.5",
                    data: rcp45,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false
                },
                {
                    label: "Ave_RCP6.0",
                    data: rcp60,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false
                },
                {
                    label: "Ave_RCP8.5",
                    data: rcp85,
                    borderWidth: 2,
                    tension: 0.2,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: chartTitle
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
}

async function loadStateChart(csvPath, selectId, canvasId, stateName) {
    const selectElement = document.getElementById(selectId);

    try {
        const response = await fetch(csvPath);
        const csvText = await response.text();

        const data = parseCSV(csvText);
        const groupedData = groupByLocation(data);

        fillLocationDropdown(selectElement, groupedData);

        const firstLocation = selectElement.value;

        if (stateName === "Kedah") {
            kedahChart = createOrUpdateChart(
                kedahChart,
                canvasId,
                firstLocation,
                groupedData,
                `Kedah Rainfall - ${firstLocation}`
            );
        } else if (stateName === "Selangor") {
            selangorChart = createOrUpdateChart(
                selangorChart,
                canvasId,
                firstLocation,
                groupedData,
                `Selangor Rainfall - ${firstLocation}`
            );
        }

        selectElement.addEventListener("change", function () {
            if (stateName === "Kedah") {
                kedahChart = createOrUpdateChart(
                    kedahChart,
                    canvasId,
                    this.value,
                    groupedData,
                    `Kedah Rainfall - ${this.value}`
                );
            } else if (stateName === "Selangor") {
                selangorChart = createOrUpdateChart(
                    selangorChart,
                    canvasId,
                    this.value,
                    groupedData,
                    `Selangor Rainfall - ${this.value}`
                );
            }
        });
    } catch (error) {
        console.error(`Error loading ${stateName} CSV:`, error);
        if (selectElement) {
            selectElement.innerHTML = `<option>Failed to load ${stateName} data</option>`;
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    loadStateChart(
        "data/RF_Kedah_Mac_May_2026_AveRCP.csv",
        "kedahLocation",
        "kedahChart",
        "Kedah"
    );

    loadStateChart(
        "data/RF_Slgr_Wlyh_Mac_May_2026_AveRCP.csv",
        "selangorLocation",
        "selangorChart",
        "Selangor"
    );
});