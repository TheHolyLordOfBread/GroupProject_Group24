<?php

function db_connect(): SQLite3 {
    return $db = new SQLite3(filename: "DatabaseForGroupProject.db");
}

$db = db_connect();
if ($db) {
    echo "Connected to group project's database successfully.";
}
else {
    echo "Connection failed: " . $db->lastErrorMsg();
}
?>