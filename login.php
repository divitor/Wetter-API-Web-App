<?php
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
    <link rel="stylesheet" href="Styles/login.css">
</head>

<body>
    <div class="login-header">
        <h1>Login Page Weather App</h1>
        <h2>made by Sixten Klittich</h2>
    </div>
    <div class='cotainer'>
        <?php
        if (isset($_POST['submit'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            require_once 'database.php';

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

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
                    name="submit"
                    </div>
        </form>
    </div>

</body>


</html