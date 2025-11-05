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
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repeat_password = $_POST['repeat_password'];

        $hash_password = password_hash($password, PASSWORD_BCRYPT);

        $erros = array();

        if (empty($username) || empty($email) || empty($password) || empty($repeat_password)) {
            array_push($erros, "Alle Felder muessen ausgefuellt sein");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($erros, "Ungültige E-Mail-Adresse");
        }
        if (strlen($password) < 8) {
            array_push($erros, "Passwort muss mindestens 8 Zeichen lang sein");
        }

        if (preg_match('/[0-9]/', $password) == 0) {
            array_push($erros, "Passwort muss mindestens eine Zahl enthalten");
        }

        if (preg_match('/[A-Z]/', $password) == 0) {
            array_push($erros, "Passwort muss mindestens einen Großbuchstaben enthalten");
        }

        if ($password !== $repeat_password) {
            array_push($erros, "Passwörter stimmen nicht überein");
        }

        $sql = "SELECT * FROM users WHERE email = '$email'";
        require_once 'database.php';
        $result = mysqli_query($conn,$sql);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            array_push($erros, "E-Mail-Adresse ist bereits registriert");
        }

        if (count($erros) > 0) {
            foreach ($erros as $error) {
                echo "<div class='form_fehler'>
                <div>$error</div>
                </div>";
            }
        } else {
            require_once 'database.php';
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash_password);
                mysqli_stmt_execute($stmt);
                echo "<div class='form_erfolg'>
                <div>Registrierung erfolgreich!</div>
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
                <input type="text" id="username" name="username">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="password">repeat Password:</label>
                <input type="password" id="repeat_password" name="repeat_password">
            </div>

            <div class="form-group">
                <input type="submit" value="Register" name="submit">
            </div>
        </form>
    </div>
</body>

</html>