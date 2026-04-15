<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< Updated upstream
    <title>NAHRIM Dashboard</title>

    <link rel="stylesheet" href="css/style.css?v=11">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/index.js"></script>
=======
    <title>Index</title>
    <link rel="stylesheet" href="css/style.css?v=12">
>>>>>>> Stashed changes
</head>
<body>

<?php include 'navbar.php'; ?>

<<<<<<< Updated upstream
<div class="actionbar">
    <div class="imageBox" style="display: inline-flex;">
=======
<div class="page-content">

    <div class="nahrim-banner">
>>>>>>> Stashed changes
        <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
    </div>

<<<<<<< Updated upstream
    <form action="Index.php" method="POST">
        <label for="Range">Select Range:</label>
        <select name="Range" id="Range">
            <option value="Range1">&lt;1 day</option>
            <option value="Range2">&lt;1 week</option>
            <option value="Range3">&lt;2 weeks</option>
            <option value="Range4">&lt;4 weeks</option>
        </select>
    </form>
</div>

<div class="page-content">

    <div class="grid">
        <div class="NAHRIM">
            <h2>NAHRIM PANEL</h2>
        </div>

        <div class="Sources">
            <form action="#" method="POST">
                <div class="source-item">
                    <input type="checkbox" id="NAHRIM" name="Source[]" value="NAHRIM" checked>
                    <label for="NAHRIM">NAHRIM</label>
                </div>

                <div class="source-item">
                    <input type="checkbox" id="OpenMeteo" name="Source[]" value="OpenMeteo">
                    <label for="OpenMeteo">OpenMeteo</label>
                </div>

                <div class="source-item">
                    <input type="checkbox" id="Google" name="Source[]" value="Google">
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
            <h2>KEDAH DATA</h2>

            <div class="chart-controls">
                <label for="kedahLocation">Select Latitude / Longitude:</label>
                <select id="kedahLocation">
                    <option>Loading Kedah data...</option>
                </select>
            </div>

            <div class="chart-container">
                <canvas id="kedahChart"></canvas>
            </div>
        </div>

        <div class="box">
            <h2>SELANGOR DATA</h2>
=======
    <div class="box-row">
        <div class="box-group">
            <select>
                <option>Select Hospital</option>
                <option>Hospital A</option>
                <option>Hospital B</option>
            </select>
            <div class="box"></div>
        </div>

        <div class="box-group">
            <select>
                <option>Select Rainfall</option>
                <option>RCP 2.6</option>
                <option>RCP 4.5</option>
                <option>RCP 6.0</option>
                <option>RCP 8.5</option>
            </select>
            <div class="box"></div>
        </div>
>>>>>>> Stashed changes

            <div class="chart-controls">
                <label for="selangorLocation">Select Latitude / Longitude:</label>
                <select id="selangorLocation">
                    <option>Loading Selangor data...</option>
                </select>
            </div>

            <div class="chart-container">
                <canvas id="selangorChart"></canvas>
            </div>
        </div>
    </div>

</div>

<?php include 'BottomBar.php'; ?>

</body>
</html>