<?php
// Session starten und prüfen, ob der Benutzer bereits eingeloggt ist
session_start();
if (isset($_SESSION['user_id'])) { // Wenn ja, weiterleiten zur Hauptseite
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
    <link rel="stylesheet" href="Styles/login.css">
</head>

<body>
    <div class="login-header">
        <h1>Login Page Weather App</h1>
        <h2>made by Sixten Klittich</h2>
    </div>
    <div class='cotainer'>
        <?php
        // Login-Formular verarbeiten
        if (isset($_POST['submit'])) { //Wenn das Formular abgeschickt wurde
            $email = $_POST['email'];  //E-Mail aus dem Formular
            $password = $_POST['password']; //Passwort aus dem Formular

            require_once 'database.php'; //Datenbankverbindung einbinden

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?"); //SQL-Abfrage vorbereiten
            $stmt->bind_param("s", $email); //Parameter binden
            $stmt->execute(); //Abfrage ausführen
            $result = $stmt->get_result(); //Ergebnis holen
            $user = $result->fetch_assoc(); //Benutzerdaten als assoziatives Array holen

            //Überprüfen, ob der Benutzer existiert und das Passwort korrekt ist
            if ($user) {
                if (password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id']; 
                    $_SESSION['username'] = $user['username'];
                    header("Location: weatherapp.php");
                    exit();
                } else {
                    echo "<div class='form_fehler'>
                    <div>Falsches Passwort.</div>
                    <div class='form_group'>
                    <label for='register_link'>Noch kein Konto? <a href='register.php' id='register_link'>Registrieren</a></label>
                    </div>";
                }
            } else {
                echo "<div class='form_fehler'>
                <div>Keine Benutzer mit dieser E-Mail-Adresse gefunden.</div>
                <div class='form_group'>
                <label for='register_link'>Noch kein Konto? <a href='register.php' id='register_link'>Registrieren</a></label>
                </div>";
            }
        } 
        ?>
    </div>
    <div class='login-form'>
        <form action="login.php" method="post" name="login">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email"
                    placeholder="enter Email..."
                    id="email"
                    name="email">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password"
                    placeholder="enter Password..."
                    id="password"
                    name="password">
            </div>
            <div class="form-group">
                <input type="submit"
                    value="Login"
                    name="submit">
                    </div>
        </form>
    </div>

</body>


</html