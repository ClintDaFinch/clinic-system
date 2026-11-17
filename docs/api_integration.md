# External API Integration

## 1. API Information

The Clinic Queuing System uses the Open-Meteo Weather API to display the current temperature and weather condition on the queue status page.

**API Details:**
- API Provider: Open-Meteo
- API Endpoint: https://api.open-meteo.com/v1/forecast
- Local Endpoint: `api/weather.php`
- HTTP Method: GET
- Data Format: JSON

## 2. API Parameters

The following parameters are used to retrieve weather information for Manila, Philippines.

| Parameter | Value | Description |
|---|---|---|
| latitude | 14.5995 | Latitude of Manila |
| longitude | 120.9842 | Longitude of Manila |
| current | temperature_2m,weather_code | Retrieves current temperature and weather code |
| timezone | Asia/Manila | Sets the timezone |

**Sample API Request:**

```text
GET https://api.open-meteo.com/v1/forecast?latitude=14.5995&longitude=120.9842&current=temperature_2m,weather_code&timezone=Asia%2FManila
```

The request is sent using PHP cURL. The weather information is then processed and displayed on the queue status page.

## 3. Sample API Response

The system processes the response from Open-Meteo and returns weather information in JSON format.

**Example successful response:**

```json
{
  "success": true,
  "temperature_c": 28.6,
  "condition": "Overcast",
  "weather_code": 3,
  "observed_at": "2026-11-16T18:45",
  "location": "Manila, Philippines"
}
```

The values shown are examples. The actual weather information changes depending on the API response.

## 4. Error Handling

The system includes error handling in case the weather API cannot retrieve information.

| HTTP Status | Description 
| 405         | The request method is not allowed. 
| 500         | The configured latitude or longitude is invalid. 
| 503         | The API is unavailable, the request times out, or the returned data is invalid. 

If an error occurs, the system displays a weather unavailable message. Users can click the Refresh Weather button to request updated information.

## 5. Validation and Security

The weather API validates the configured latitude and longitude before sending a request. It also uses HTTPS and prevents redirects to other URLs.

The configuration folder is protected using `.htaccess` to prevent direct browser access. The weather feature does not require an API key and does not access patient information.

## 6. Implementation Result

The Open-Meteo API was successfully connected to the Clinic Queuing System using PHP and JavaScript.

During local testing with XAMPP, the system was able to retrieve weather information and display the temperature and weather condition on the queue status page. The API also returned an error message when a request timed out.