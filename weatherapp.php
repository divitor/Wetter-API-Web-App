<?php
session_start();
require_once 'database.php';
$logged_in_user = $_SESSION['username'] ?? 'Gast';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$favorite_city = "";


$weather_data = "";
$weather_celcius   = "";
$weather_description = "";
$weather_humidity = "";
$weather_wind_speed = "";
$weather_condition = "";

if (isset($_POST['submit'])) {
    if (empty($_POST['city'])) {
        echo "<script>alert('Bitte geben Sie einen Stadtnamen ein.');</script>";
    } else {
        $city = $_POST['city'];
        $apiKey = "0ab81cf6fadc0728247c7904050b3b9f";
        $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey";
        $api_data = file_get_contents($apiUrl);
        $weather_data = json_decode($api_data, true);
        $weather_celcius = $weather_data['main']['temp'] - 273.15;
        $weather_celcius = round($weather_celcius, 2);
        $weather_description = $weather_data['weather'][0]['description'];
        $weather_humidity = $weather_data['main']['humidity'];
        $weather_wind_speed = $weather_data['wind']['speed'];
        $weather_condition = $weather_data['weather'][0]['main'];
    }
}

// Prüfen, ob das Formular gesendet wurde
if (isset($_POST['add_favorite'])) {
    $favorite_city = trim($_POST['fav_city']);

    if (!empty($favorite_city)) {
        // SQL-Statement: UPDATE-Abfrage, um nur die Spalte 'favorite_city' für diesen Benutzer zu ändern
        $sql = "UPDATE users SET favorite_city = ? WHERE id = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Parameter binden: 's' für String (Stadtname), 'i' für Integer (User-ID)
            $stmt->bind_param("si", $favorite_city, $user_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $message = "✅ Lieblingsstadt (" . htmlspecialchars($favorite_city) . ") erfolgreich gespeichert!";
            } else {
                $message = "Hinweis: Stadt war bereits als Favorit gespeichert.";
            }

            $stmt->close();
        } else {
            $message = "Ein Datenbankfehler ist aufgetreten.";
        }
    } else {
        $message = "Bitte geben Sie einen Stadtnamen ein.";
    }
}
$sql_select = "SELECT favorite_city FROM users WHERE id = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $user_id);
$stmt_select->execute();
$result_select = $stmt_select->get_result();
$user_data = $result_select->fetch_assoc();

$favorite_city_name = $user_data['favorite_city'] ?? 'unbekannt'; // Holt den gespeicherten Favoriten

$stmt_select->close();


$icon_class = "";
switch ($weather_condition) {
    case 'Clear':
        $icon_class = 'fa-solid fa-sun';
        break;
    case 'Clouds':
        $icon_class = 'fa-solid fa-cloud';
        break;
    case 'Rain':
        $icon_class = 'fa-solid fa-cloud-rain';
        break;
    case 'Snow':
        $icon_class = 'fa-solid fa-cloud-bolt';
        break;
    case 'Thunderstorm':
        $icon_class = 'fa-solid fa-snowflake';
        break;
    default:
        $icon_class = 'fa-solid fa-smog';
        break;
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <main>
        <header>
            <div class="weather-container">
                <div class="weather-header">
                    <h1>Weather App</h1>
                    <h2>Welcome, <?php echo htmlspecialchars($logged_in_user); ?>! </h2>
                </div>

        </header>
        <section>
            <form method="post">
                <div class='weather-container favorite-city'>
                    <h3>Lieblingsstadt einspeichern?</h3>
                    <input type="text"
                        name="fav_city"
                        id="fav_city"
                        placeholder="Geben sie Ihre Lieblingsstadt ein"
                        value="<?php echo htmlspecialchars($favorite_city) ?>">
                    <input
                        type="submit"
                        name="add_favorite"
                        value="Zu Favoriten hinzufügen">
                </div>
            </form>
            </div>
            <form method="post">
                <div class="weather-container search-weather-container">
                    <input type="text"
                        name="city" id=""
                        placeholder="enter city"
                        value="<?php echo htmlspecialchars($favorite_city_name); ?>">
                    <input type="submit" name="submit" id="search_btn" value="Suchen">
                </div>
            </form>
            <div class="icon_weather-container">
                <i class="fa-solid fa-xl <?php echo htmlspecialchars($icon_class); ?>"></i>
            </div>
            <div class="weather-results-container">
                <input type="text"
                    name="weather-city"
                    id="weather-city"
                    value="<?php echo isset($weather_data['name']) ? htmlspecialchars($weather_data['name']) : ''; ?>"
                    placeholder="Stadtname"
                    readonly>
            </div>
            <div class="weather-container weather-tmp">
                <input type="text"
                    name="weather-tmp" id="weather-tmp"
                    value="<?php echo htmlspecialchars($weather_celcius . "°C") ?>"
                    placeholder="0°C"
                    readonly;>
            </div>
            <div class="weather-container weather-description">
                <input type="text"
                    name="weather-description"
                    id="weather-description"
                    value="<?php echo htmlspecialchars($weather_description) ?>"
                    placeholder="Wetterbeschreibung"
                    readonly>
            </div>
            <div class="weather-container weather-zusatz">
                <input type="text"
                    name="weather-humidity"
                    id="weather-humidity"
                    value="<?php echo 'Luftfeuchtigkeit: ' . htmlspecialchars($weather_humidity) . '%'; ?>"
                    placveholder="0$"
                    readonly>
                <input type="text"
                    name="weather-wind-speed"
                    id="weather-wind-speed"
                    value="<?php echo 'Windgeschwindigkeit: ' . htmlspecialchars($weather_wind_speed) . " m/s"; ?>"
                    placeholder="0 m/s"
                    readonly>
            </div>
            <div class="weather-container login-register-container">
                <input type="button" name="logout" id="logout_btn" value="Abmelden" onclick="location.href='logout.php'">
            </div>
        </section>
    </main
        </body>

</html>