<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Hospital</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php

    if (isset($_POST['updateHospital'])) {
        $id = $_POST['HOSPITAL_ID'];
        $name = $_POST['NAME'];
        $address = $_POST['ADDRESS'];
        $state = $_POST['STATE'];
        $longitude = $_POST['LONGITUDE'];
        $latitude = $_POST['LATITUDE'];
        $type_of_hospital = $_POST['TYPE_OF_HOSPITAL'];
        updateHospital($id, $name, $address, $state, $longitude, $latitude, $type_of_hospital);
        header("Location: viewHospital.php");
    }
    
    
       $id = $_GET['id'];
       
      $hospital = viewHospital($id);
      ?>
      <div>
         <h2 class="centered-header">Update <?php echo $hospital[0]['NAME']; ?>Details?</h2>
      </div>
      <div class="main">
        <form method="post">
            <input type="hidden" name="HOSPITAL_ID" value="<?php echo $hospital[0]['HOSPITAL_ID']; ?>">
            <input type="text" name="NAME" value="<?php echo $hospital[0]['NAME']; ?>">
            <input type="text" name="ADDRESS" value="<?php echo $hospital[0]['ADDRESS']; ?>">
            <input type="text" name="STATE" value="<?php echo $hospital[0]['STATE']; ?>">
            <input type="text" name="LONGITUDE" value="<?php echo $hospital[0]['LONGITUDE']; ?>">
            <input type="text" name="LATITUDE" value="<?php echo $hospital[0]['LATITUDE']; ?>">
            <input type="text" name="TYPE_OF_HOSPITAL" value="<?php echo $hospital[0]['TYPE_OF_HOSPITAL']; ?>">

            <input type="submit" name="updateHospital" value="Update Hospital">
        </form>
      </div>
      <footer>
        <div class=firstfooter style="text-align: center;">
        </div>
      </footer>
</body>     
</html>