<?php
$host = "b5cofmy86wzgn1ahjtte-mysql.services.clever-cloud.com";
$username = "uad8nlevce6hpwnc";
$password = "gFz67gLWbpYjq02JvXlt";
$database = "b5cofmy86wzgn1ahjtte";

$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
