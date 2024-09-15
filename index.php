<?php
function getWeatherData($latitude, $longitude) {
    $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=Asia%2FTokyo";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

function processWeatherData($data) {
    if ($data) {
        $today = date("Y/m/d");
        $todayIndex = array_search($today, $data['daily']['time']);
        
        return [
            'date' => $today,
            'maxTemp' => $data['daily']['temperature_2m_max'][$todayIndex],
            'minTemp' => $data['daily']['temperature_2m_min'][$todayIndex],
            'precipitation' => $data['daily']['precipitation_sum'][$todayIndex]
        ];
    }
    return null;
}

$locations = [
    '東京' => ['lat' => 35.6895, 'lon' => 139.6917],
    '愛媛（松山市）' => ['lat' => 33.8416, 'lon' => 132.7678]
];

$weatherData = [];

foreach ($locations as $city => $coords) {
    $data = getWeatherData($coords['lat'], $coords['lon']);
    $weatherData[$city] = processWeatherData($data);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>東京と愛媛の天気</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .weather-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .weather-info {
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            width: 45%;
            min-width: 250px;
        }
    </style>
</head>
<body>
    <h1>今日の天気</h1>
    <div class="weather-container">
    <?php foreach ($weatherData as $city => $weather): ?>
        <?php if ($weather): ?>
            <div class="weather-info">
                <h2><?php echo $city; ?></h2>
                <p>日付: <?php echo $weather['date']; ?></p>
                <p>最高気温: <?php echo $weather['maxTemp']; ?>°C</p>
                <p>最低気温: <?php echo $weather['minTemp']; ?>°C</p>
                <p>降水量: <?php echo $weather['precipitation']; ?>mm</p>
            </div>
        <?php else: ?>
            <div class="weather-info">
                <h2><?php echo $city; ?></h2>
                <p>天気データの取得に失敗しました。</p>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
</body>
</html>