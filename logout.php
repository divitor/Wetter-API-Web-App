// Logout-Skript: Session beenden und zur Login-Seite weiterleiten
<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>