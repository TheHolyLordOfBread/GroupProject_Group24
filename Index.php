<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link rel="stylesheet" href="css/style.css?v=1">


    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

    <script defer src="js/index.js"></script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="actionbar">

<div class="imageBox" style="display: inline-flex;"><img src="nahrim-banner.png"></img></div>
<h1>        <!--Form for choosing the graphs range-->
        <form action="Index.php" method="POST">
            <label for="Range">Select Range:</label>
                <select name="Range" id="Range">
                  <option value="Range1"><1 day</option>
                  <option value="Range2"><1 week</option>
                  <option value="Range3"><2 weeks</option>
                  <option value="Range4"><4 weeks</option>
                </select>
        </form></h1>
</div>

<div class="page-content">


    <div class="grid">
        <div class="NAHRIM">
            
        </div>    
        <div class="Sources"> 
        

            <form action="/action_page.php">


              <div>
              <input type="checkbox" id="NAHRIM" name="Source" value="NAHRIM" checked="true">
              <label for="NAHRIM">NAHRIM</label><br>
              </div>

              <div>
              <input type="checkbox" id="OpenMeteo" name="Source" value="OpenMeteo" checked="true">
              <label for="OpenMeteo">OpenMeteo</label><br>
              </div>

              <div>
              <input type="checkbox" id="Google" name="Source" value="Google" checked="true">
              <label for="Google">Google</label>
              </div>

            </form>


        </div>  

        <div class="Map">Map</div>  
    </div>





<!-- data for graphs go here -->
    <div class="box-row">
        <div class="box" name="box" id="NAHRIM"> <h1>NAHRIM DATA</h1> </div>
        <div class="box" name="box" id="OpenMeteo"> <h1>OPEN METEO DATA</h1> </div>
        <div class="box" name="box" id="Google"> <h1>SOMETHING DATA</h1> </div>
    </div>
</div>

<?php include 'BottomBar.php'; ?>
</body>
</html>