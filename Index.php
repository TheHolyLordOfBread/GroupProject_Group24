<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAHRIM Dashboard</title>
<<<<<<< Updated upstream
    <link rel="stylesheet" href="css/style.css">
=======

    <link rel="stylesheet" href="css/style.css?v=14">
>>>>>>> Stashed changes
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
    <script defer src="js/index.js"></script>
<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="actionbar">
    <div class="imageBox" style="display: inline-flex;">
        <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
    </div>
   
        <div class="page-content">
            <div class="nahrim-banner">
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
    </div>
=======
    <form action="#" method="POST">
        <label for="Range">Select Range:</label>
        <select name="Range" id="Range">
            <option value="Range1">&lt;1 day</option>
            <option value="Range2">&lt;1 week</option>
            <option value="Range3">&lt;2 weeks</option>
            <option value="Range4">&lt;4 weeks</option>
        </select>
    </form>
>>>>>>> Stashed changes
</div>

<div class="page-content">
    <div class="grid">
        <div class="NAHRIM" id="sourcePanel">
            <h2 id="sourcePanelTitle">COMBINED PANEL</h2>
        </div>

        <div class="Sources">
            <form action="#" method="POST">
                <div class="source-item">
                    <input type="checkbox" id="NAHRIM" name="Source[]" value="NAHRIM">
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

<<<<<<< Updated upstream


    <div class="box-row">
        <div class="box">
            <h2>NAHRIM</h2>

            <div class="chart-controls">
                <label for="kedahLocation">Kedah:</label>
                <select id="kedahLocation"></select>

                <label for="selangorLocation">Selangor:</label>
                <select id="selangorLocation"></select>
            </div>

=======
    <div class="box-row">
        <div class="box">
            <h2>COMBINED RAINFALL DATA</h2>

            <div class="chart-controls">
                <label for="kedahLocation">Kedah:</label>
                <select id="kedahLocation">
                    <option>Loading Kedah data...</option>
                </select>

                <label for="selangorLocation">Selangor:</label>
                <select id="selangorLocation">
                    <option>Loading Selangor data...</option>
                </select>
            </div>

>>>>>>> Stashed changes
            <div class="chart-container">
                <canvas id="combinedRainfallChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include 'BottomBar.php'; ?>

</body>
</html>