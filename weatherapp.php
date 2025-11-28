<?php
// weatherapp.php: Hauptseite der Wetter-App, die Wetterdaten anzeigt und Lieblingsstadt speichert
// Session starten und prüfen, ob der Benutzer bereits eingeloggt ist
session_start();
require_once 'database.php';
$logged_in_user = $_SESSION['username'] ?? 'Gast';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Benutzer-ID aus der Session
$message = "";
$favorite_city = "";


$weather_data = "";
$weather_celcius   = "";
$weather_description = "";
$weather_humidity = "";
$weather_wind_speed = "";
$weather_condition = "";

// Prüfen, ob das Formular gesendet wurde
if (isset($_POST['submit'])) {
    if (empty($_POST['city'])) {
        echo "<script>alert('Bitte geben Sie einen Stadtnamen ein.');</script>";
    } else {
        $city = $_POST['city']; // Stadtname aus dem Formular
        $apiKey = "0ab81cf6fadc0728247c7904050b3b9f"; // Ihr OpenWeatherMap API-Schlüssel
        $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey"; // API-URL mit Stadtname und API-Schlüssel
        $api_data = file_get_contents($apiUrl); // API-Daten abrufen
        $weather_data = json_decode($api_data, true); // JSON-Daten in ein assoziatives Array umwandeln
        $weather_celcius = $weather_data['main']['temp'] - 273.15; // Temperatur in Kelvin in Celsius umrechnen
        $weather_celcius = round($weather_celcius, 2); // Temperatur in Celsius runden 
        $weather_description = $weather_data['weather'][0]['description']; // Wetterbeschreibung
        $weather_humidity = $weather_data['main']['humidity']; // Luftfeuchtigkeit in %
        $weather_wind_speed = $weather_data['wind']['speed']; // Windgeschwindigkeit in m/s
        $weather_condition = $weather_data['weather'][0]['main']; // Wetterbedingung
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
                $message = "Lieblingsstadt (" . htmlspecialchars($favorite_city) . ") erfolgreich gespeichert!";
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
// Lieblingsstadt des Benutzers abrufen
$sql_select = "SELECT favorite_city FROM users WHERE id = ?"; // SQL-SELECT-Abfrage
$stmt_select = $conn->prepare($sql_select); // Vorbereiten der Abfrage
$stmt_select->bind_param("i", $user_id); // Parameter binden
$stmt_select->execute();
$result_select = $stmt_select->get_result(); // Ergebnis der Abfrage holen
$user_data = $result_select->fetch_assoc(); // Benutzerdaten als assoziatives Array holen

$favorite_city_name = $user_data['favorite_city'] ?? 'unbekannt'; // Holt den gespeicherten Favoriten

$stmt_select->close();

// Wetter-Icon basierend auf der Wetterbedingung auswählen
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
    <link rel="stylesheet" href="Styles/style.css"> <!-- Einbinden der CSS-Datei -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- Einbinden der Font Awesome Icons -->
</head>

<body>
    <main>
        <header>
            <div class="weather-container">
                <div class="weather-header">
                    <h1>Weather App</h1>
                    <h2>Welcome, <?php echo htmlspecialchars($logged_in_user); ?>!</h2> <!-- Begrüßung des eingeloggten Benutzers -->
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
                        value="<?php echo htmlspecialchars($favorite_city) ?>"> <!-- Eingabefeld für Lieblingsstadt -->
                    <input
                        type="submit"
                        name="add_favorite"
                        value="Zu Favoriten hinzufügen"> <!-- Button zum Speichern der Lieblingsstadt -->
                </div>
            </form>
            </div>
            <form method="post">
                <div class="weather-container search-weather-container">
                    <input type="text"
                        name="city" id=""
                        placeholder="enter city"
                        value="<?php echo htmlspecialchars($favorite_city_name); ?>"> <!-- Eingabefeld für Stadtnamen -->
                    <input type="submit" name="submit" id="search_btn" value="Suchen">
                </div>
            </form>
            <div class="icon_weather-container">
                <i class="fa-solid fa-xl <?php echo htmlspecialchars($icon_class); ?>"></i> <!-- Wetter-Icon basierend auf der Wetterbedingung -->
            </div>
            <div class="weather-results-container">
                <input type="text"
                    name="weather-city"
                    id="weather-city"
                    value="<?php echo isset($weather_data['name']) ? htmlspecialchars($weather_data['name']) : ''; ?>"
                    placeholder="Stadtname"
                    readonly> <!-- Anzeige des Stadtnamens -->
            </div>
            <div class="weather-container weather-tmp">
                <input type="text"
                    name="weather-tmp" id="weather-tmp"
                    value="<?php echo htmlspecialchars($weather_celcius . "°C") ?>"
                    placeholder="0°C"
                    readonly;> <!-- Anzeige der Temperatur in Celsius -->
            </div>
            <div class="weather-container weather-description">
                <input type="text"
                    name="weather-description"
                    id="weather-description"
                    value="<?php echo htmlspecialchars($weather_description) ?>"
                    placeholder="Wetterbeschreibung"
                    readonly> <!-- Anzeige der Wetterbeschreibung -->
            </div>
            <div class="weather-container weather-zusatz">
                <input type="text"
                    name="weather-humidity"
                    id="weather-humidity"
                    value="<?php echo 'Luftfeuchtigkeit: ' . htmlspecialchars($weather_humidity) . '%'; ?>"
                    placveholder="0$"
                    readonly> <!-- Anzeige der Luftfeuchtigkeit -->
                <input type="text"
                    name="weather-wind-speed"
                    id="weather-wind-speed"
                    value="<?php echo 'Windgeschwindigkeit: ' . htmlspecialchars($weather_wind_speed) . " m/s"; ?>"
                    placeholder="0 m/s"
                    readonly> <!-- Anzeige der Windgeschwindigkeit -->
            </div>
            <div class="weather-container login-register-container">
                <input type="button" name="logout" id="logout_btn" value="Abmelden" onclick="location.href='logout.php'"> <!-- Button zum Abmelden -->
            </div>
        </section>
    </main>
</body>

</html>