<?php

function db_connect(): SQLite3 {
    return $db = new SQLite3(filename: "DatabaseForGroupProject.db");
}

$db = db_connect();
if ($db) {
<<<<<<< Updated upstream
    echo "Connected to group project's database successfully.";
=======
    echo "Connected to group project database successfully.";
>>>>>>> Stashed changes
}
else {
    echo "Connection failed: " . $db->lastErrorMsg();
}
<<<<<<< Updated upstream
?>
=======
?>
>>>>>>> Stashed changes
