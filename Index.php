<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAHRIM Dashboard</title>

    <link rel="stylesheet" href="css/style.css?v=11">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="js/index.js"></script>
    <title>Index</title>

</head>
<body>

<?php include 'navbar.php'; ?>

<div class="actionbar">
    <div class="imageBox">
    


        <div class="page-content">
            <div class="nahrim-banner">
                <img src="images/nahrim-banner.png" alt="NAHRIM Banner">
            </div>

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
    </div>
    <div class="box-row">

        <div class="box">
            <h2>SELANGOR DATA</h2>
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