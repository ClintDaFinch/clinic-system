(() => {
  const temperature = document.getElementById('weatherTemperature');
  const condition = document.getElementById('weatherCondition');
  const refresh = document.getElementById('refreshWeather');
  if (!temperature || !condition) return;
  async function loadWeather() {
    if (refresh) refresh.disabled = true;
    temperature.textContent = 'Loading...';
    condition.textContent = 'Checking current weather';
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 8000);
    try {
      const response = await fetch('api/weather.php', { signal: controller.signal, cache: 'no-store' });
      const data = await response.json();
      if (!response.ok || !data.success || typeof data.temperature_c !== 'number') {
        throw new Error(data.error || 'Weather unavailable');
      }
      temperature.textContent = `${data.temperature_c.toFixed(1)} °C`;
      condition.textContent = data.condition || 'Condition unavailable';
    } catch (error) {
      temperature.textContent = 'Unavailable';
      condition.textContent = error.name === 'AbortError' ? 'Weather request timed out' : 'Unable to load weather right now';
    } finally {
      clearTimeout(timer);
      if (refresh) refresh.disabled = false;
    }
  }
  if (refresh) refresh.addEventListener('click', loadWeather);
  loadWeather();
})();
