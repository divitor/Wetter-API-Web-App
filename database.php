<?php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "weatherapp_register";

$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}
?>