<?php
//Verbindungsaufbau zur MySQL-Datenbank

$hostName = "localhost"; //Servername
$dbUser = "root";  //Datenbankbenutzer
$dbPassword = "";  //Datenbankpasswort
$dbName = "weatherapp_register"; //Datenbankname

$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error()); //Fehlermeldung bei Verbindungsfehler, falls einer der Parameter falsch ist.
}
?>