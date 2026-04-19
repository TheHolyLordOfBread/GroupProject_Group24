<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAHRIM Dashboard</title>

    <link rel="stylesheet" href="css/style.css?v=40">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script defer src="js/index.js?v=40"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="actionbar" role="banner">
    <div class="imageBox" style="display: inline-flex;">
        <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
    </div>

    <form action="#" method="POST">
        <label for="Source">Source:</label>
        <select name="Source" id="Source">
            <option value="NAHRIM" selected>NAHRIM</option>
            <option value="OpenMeteo">OpenMeteo</option>
            <option value="Google">Google</option>
        </select>
    </form>

    <form action="#" method="POST">
        <label for="Range">Select Range:</label>
        <select name="Range" id="Range">
            <option value="3">3 days</option>
            <option value="7" selected>7 days</option>
            <option value="14">14 days</option>
        </select>
    </form>
</div>

<div class="page-content">
    <div class="grid">
        <div class="NAHRIM" id="sourcePanel">
            <h2 id="sourcePanelTitle">NAHRIM PANEL</h2>
        </div>

        <div class="Sources">
            <form action="#" method="POST">
                <div class="source-item">
                    <input type="checkbox" id="NAHRIM" checked>
                    <label for="NAHRIM">NAHRIM</label>
                </div>

                <div class="source-item">
                    <input type="checkbox" id="OpenMeteo">
                    <label for="OpenMeteo">OpenMeteo</label>
                </div>

                <div class="source-item">
                    <input type="checkbox" id="Google">
                    <label for="Google">Google</label>
                </div>
            </form>
        </div>

        <div class="Map">
            Map
        </div>
    </div>

    <div class="box-row">
        <div class="box">
            <h2 id="chartBoxTitle">NAHRIM</h2>

            <div class="chart-controls">
                <label for="Scenario">Scenario:</label>
                <select id="Scenario">
                    <option value="Ave_RCP2.6">RCP 2.6</option>
                    <option value="Ave_RCP4.5" selected>RCP 4.5</option>
                    <option value="Ave_RCP6.0">RCP 6.0</option>
                    <option value="Ave_RCP8.5">RCP 8.5</option>
                </select>
            </div>

            <div class="chart-container">
                <canvas id="combinedRainfallChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include 'BottomBar.php'; ?>

</body>
</html>