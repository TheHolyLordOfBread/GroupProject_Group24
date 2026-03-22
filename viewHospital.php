<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div>
        <h2 class="centered-header">List of Hospitals</h2>
    </div>
    <div class="main">
        <?php
        $db = new SQLite3('DatabaseForGroupProject.db');
        $select_query = "SELECT * FROM HOSPITAL";
        $result = $db->query($select_query);
        if (!$result) 
        {
            echo "An error occurred while retrieving hospital data.";
            exit();
        }
        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>Hospital ID</td> 
                <td>Name</td> 
                <td>Address</td> 
                <td>State</td>
                <td>Longitude</td>
                <td>Langitude</td>
                <td></td>
                <td>Home Address</td>
                <td>City</td>
                <td>Postcode</td>
                <td>Email</td>
                <td>Phone Number</td>
                <td style ='text-align: center' colspan='2'> Action </td> 
            </tr>
        </thead>";
        echo "<tbody>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) 
        {
            $id = $row['patientID'];
            $fname = $row['firstName'];
            $mname = $row['middleName'];
            $lname = $row['lastName'];
            $userType = $row['userType'];
            $username = $row['username'];
            $password = $row['password'];
            $homeaddress = $row['homeAddress'];
            $city = $row['city'];
            $postcode = $row['postcode'];
            $emailaddress = $row['email'];
            $phoneNo = $row['phoneNumber'];
            echo "<tr> 
                  <td>$id</td> 
                  <td>$fname</td>
                  <td>$mname</td>
                  <td>$lname</td>
                  <td>$userType</td>
                  <td>$username</td>
                  <td>$password</td>
                  <td>$homeaddress</td>
                  <td>$city</td>
                  <td>$postcode</td>
                  <td>$emailaddress</td>
                  <td>$phoneNo</td>
                  <td><a href='updatePatient.php?id=$id'><button>Update</button></a></td>
                  <td><a href='deletePatient.php?id=$id' onclick='return confirm(\"Are you sure you want to delete this patient?\")'><button>Delete</button></a></td>
                </tr>";
        }
        echo "</tbody></table>";
        $db->close();
        ?>
    </div>
    <footer>
        <div class=footer style="text-align: center;">
            &copy; 2026 Alex Hospital Management System. All rights reserved.
        </div>
    </footer>
</body>
</html>