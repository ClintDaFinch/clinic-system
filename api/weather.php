<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $data): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    respond(405, ['success' => false, 'error' => 'Method not allowed']);
}
$config = require __DIR__ . '/../config/weather.php';
$lat = filter_var($config['latitude'], FILTER_VALIDATE_FLOAT);
$lon = filter_var($config['longitude'], FILTER_VALIDATE_FLOAT);
if ($lat === false || $lon === false || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    respond(500, ['success' => false, 'error' => 'Weather location is not configured correctly']);
}
if (!function_exists('curl_init')) {
    error_log('Weather API: PHP cURL extension is missing');
    respond(503, ['success' => false, 'error' => 'Weather service is unavailable']);
}
$query = http_build_query([
    'latitude' => $lat, 'longitude' => $lon,
    'current' => 'temperature_2m,weather_code',
    'timezone' => $config['timezone'],
]);
$handle = curl_init('https://api.open-meteo.com/v1/forecast?' . $query);
curl_setopt_array($handle, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 3,
    CURLOPT_TIMEOUT => max(3, min(15, (int)$config['timeout_seconds'])),
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
]);
$body = curl_exec($handle);
$errno = curl_errno($handle);
$status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);
if ($body === false || $errno !== 0) {
    error_log('Weather API transport error code: ' . $errno);
    respond(503, ['success' => false, 'error' => $errno === CURLE_OPERATION_TIMEDOUT ? 'Weather request timed out' : 'Weather service is unavailable']);
}
if ($status !== 200) {
    error_log('Weather API upstream HTTP status: ' . $status);
    respond(503, ['success' => false, 'error' => 'Weather service is temporarily unavailable']);
}
$data = json_decode($body, true);
$current = is_array($data) ? ($data['current'] ?? null) : null;
if (!is_array($current) || !isset($current['temperature_2m'], $current['weather_code']) || !is_numeric($current['temperature_2m']) || !is_numeric($current['weather_code'])) {
    respond(503, ['success' => false, 'error' => 'Weather data is currently unavailable']);
}
$code = (int)$current['weather_code'];
$conditions = [0=>'Clear sky',1=>'Mainly clear',2=>'Partly cloudy',3=>'Overcast',45=>'Fog',48=>'Depositing rime fog',51=>'Light drizzle',53=>'Moderate drizzle',55=>'Dense drizzle',61=>'Slight rain',63=>'Moderate rain',65=>'Heavy rain',71=>'Slight snow',73=>'Moderate snow',75=>'Heavy snow',80=>'Slight rain showers',81=>'Moderate rain showers',82=>'Violent rain showers',95=>'Thunderstorm',96=>'Thunderstorm with hail',99=>'Thunderstorm with heavy hail'];
respond(200, [
    'success'=>true,
    'temperature_c'=>round((float)$current['temperature_2m'],1),
    'condition'=>$conditions[$code] ?? 'Weather code ' . $code,
    'weather_code'=>$code,
    'observed_at'=>is_string($current['time'] ?? null) ? $current['time'] : null,
    'location'=>'Manila, Philippines',
]);
