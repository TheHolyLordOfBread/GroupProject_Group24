<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rainfall Comparison Dashboard</title>

    <link rel="stylesheet" href="css/style.css?v=999999">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script defer src="js/comparegraphs.js?v=2000000"></script>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="actionbar">
    <div class="imageBox" style="display: inline-flex;">
        <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
    </div>

    <form action="#" method="POST">
        <label for="compareRange">Select Range:</label>
        <select id="compareRange">
            <option value="3">3 days</option>
            <option value="7">7 days</option>
<option value="14" selected>14 days</option>
        </select>
    </form>
</div>

<div class="compare-page">

    <h1 class="graphs-title">Rainfall Comparison Dashboard</h1>

    <p style="color:#555; margin-bottom:20px; font-size:16px;">
        Comparative analysis of rainfall data across NAHRIM, OpenMeteo, and Google datasets.
    </p>

    <div class="compare-grid">

        <div class="compare-card">
            <h2>NAHRIM</h2>

            <div class="chart-controls">
                <label for="compareScenario">Scenario:</label>
                <select id="compareScenario">
                    <option value="Ave_RCP2.6">RCP 2.6</option>
                    <option value="Ave_RCP4.5" selected>RCP 4.5</option>
                    <option value="Ave_RCP6.0">RCP 6.0</option>
                    <option value="Ave_RCP8.5">RCP 8.5</option>
                </select>
            </div>

            <div class="chart-container compare-chart-container">
                <canvas id="compareNahrimChart"></canvas>
            </div>
        </div>

        <div class="compare-card">
            <h2>OpenMeteo</h2>

            <div class="chart-container compare-chart-container">
                <canvas id="compareOpenMeteoChart"></canvas>
            </div>
        </div>

        <div class="compare-card">
            <h2>Google</h2>

            <div class="chart-container compare-chart-container">
                <canvas id="compareGoogleChart"></canvas>
            </div>
        </div>

    </div>
</div>

<?php include 'BottomBar.php'; ?>

</body>
</html>