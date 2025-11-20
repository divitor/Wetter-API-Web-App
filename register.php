<?php
// Session starten und prüfen, ob der Benutzer bereits eingeloggt ist
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: weatherapp.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="Styles/register.css">
</head>

<body>
    <div class="registration-header">
        <h1>Registration Page Weather App</h1>
        <h2>made by Sixten Klittich</h2>
    </div>
    <?php
    // Registrierungsformular verarbeiten
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repeat_password = $_POST['repeat_password'];

        $hash_password = password_hash($password, PASSWORD_BCRYPT); // Passwort hashen

        $erros = array();
        // Validierungen
        if (empty($username) || empty($email) || empty($password) || empty($repeat_password)) {
            array_push($erros, "Alle Felder muessen ausgefuellt sein"); // Überprüfen, ob alle Felder ausgefüllt sind
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($erros, "Ungültige E-Mail-Adresse"); // Überprüfen, ob die E-Mail-Adresse gültig ist
        }
        if (strlen($password) < 8) {
            array_push($erros, "Passwort muss mindestens 8 Zeichen lang sein");    // Überprüfen der Passwortlänge
        }

        if (preg_match('/[0-9]/', $password) == 0) {
            array_push($erros, "Passwort muss mindestens eine Zahl enthalten"); // Überprüfen, ob das Passwort mindestens eine Zahl enthält
        }

        if (preg_match('/[A-Z]/', $password) == 0) {
            array_push($erros, "Passwort muss mindestens einen Großbuchstaben enthalten"); // Überprüfen, ob das Passwort mindestens einen Großbuchstaben enthält
        }

        if ($password !== $repeat_password) {
            array_push($erros, "Passwörter stimmen nicht überein"); //Überprüfen, ob die Passwörter übereinstimmen
        }
        // Überprüfen, ob die E-Mail-Adresse bereits registriert ist
        $sql = "SELECT * FROM users WHERE email = '$email'";
        require_once 'database.php'; //Datenbankverbindung einbinden
        $result = mysqli_query($conn, $sql); //SQL-Abfrage ausführen
        $rowCount = mysqli_num_rows($result); //Anzahl der gefundenen Zeilen
        if ($rowCount > 0) {
            array_push($erros, "E-Mail-Adresse ist bereits registriert"); //E-Mail existiert bereits
        }
        //Wenn es Fehler gibt, diese anzeigen
        if (count($erros) > 0) {
            foreach ($erros as $error) {
                echo "<div class='form_fehler'>
                <div>$error</div>
                </div>";
            }
            //Wenn keine Fehler, Benutzer in die Datenbank einfügen
        } else {
            require_once 'database.php';
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"; //SQL-Abfrage zum Einfügen des Benutzers
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql); 
            if ($prepareStmt) { 
                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash_password); //Parameter binden
                mysqli_stmt_execute($stmt); //Abfrage ausführen
                echo "<div class='form_erfolg'>
                <div>Registrierung erfolgreich!</div>
                <div><a href='login.php'>Hier anmelden</a></div>
                </div>";
            } else {
                die("SQL Fehler");
            }
        }
    }
    ?>
    <div class="registration-form">
        <form method="post" action="register.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username"> <!--Benutzernamen-Eingabefeld-->
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email"> <!--E-Mail-Eingabefeld-->
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password"> <!--Passwort-Eingabefeld-->
            </div>

            <div class="form-group">
                <label for="password">repeat Password:</label>
                <input type="password" id="repeat_password" name="repeat_password"> <!--Passwort-Wiederholungs-Eingabefeld-->
            </div>

            <div class="form-group">
                <input type="submit" value="Register" name="submit"> <!--Registrierungs-Button-->
            </div>
        </form>
    </div>
</body>

</html>