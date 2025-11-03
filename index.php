<?php
if (isset($_POST['submit'])) {
    if (empty($_POST['city'])) {
        echo "<p>Bitte geben Sie einen Stadtnamen ein.</p>";
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
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
</head>

<body>
    <main>
        <header>
            <div class="weather-container">
                <div class="weather-header">
                    <h1>Weather App</h1>
                    <p>made by Sixten Klittich</p>
                </div>

                <div class="weather-container login-register-container">
                    <input type="button" name="anmeldung" id="anmelde_btn" value="Anmelden">
                    <input type="button" name="regi" id="regi_btn" value="Registrieren">

                </div>
        </header>
        <section>
            <form method="post">
                <div class="weather-container search-weather-container">
                    <input type="text" name="city" id="" placeholder="enter city">
                    <input type="submit" name="submit" id="search_btn" value="Suchen">
                </div>
            </form>


            <div class="icon-weather-container">
                 <i class="fa-solid fa-cloud-sun"></i>
            </div>
            <div class="weather-container weather-tmp">
                <input type="text"
                    name="weather-tmp" id="weather-tmp"
                    value=<?php echo htmlspecialchars($weather_celcius . "°C"); ?>
                    readonly>
            </div>
            <div class="weather-container weather-description">
                <input type="text"
                    name="weather-description"
                    id="weather-description"
                    value=<?php echo htmlspecialchars($weather_description); ?>
                    placeholder="Wetterbeschreibung"
                    readonly>
            </div>
            <div class="weather-container weather-zusatz">
                <input type="text"
                    name="weather-humidity" id="weather-humidity"
                    value=<?php echo 'Luftfeuchtigkeit: '.$weather_humidity. '%'; ?>
                    readonly
                    >
                <input type="text"
                    name="weather-wind-speed"
                    id="weather-wind-speed"
                    value=<?php echo htmlspecialchars('Windgeschwindigkeit: '.$weather_wind_speed. "m/s"); ?>
                    readonly
                    >
            </div>
            </div>
            </div>
        </section>
    </main>
</body>

</html