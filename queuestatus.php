<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Queue Status</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <span class="clinic-icon">✚</span>
                Clinic Queue System
            </div>

            <nav>
                <a href="addpatient.php">Register</a>
                <a href="queuestatus.php">Queue Status</a>
                <a href="clinic_queue.php">Staff Dashboard</a>
            </nav>
        </div>
    </header>

    <main>
        <h1>Queue Status</h1>
        <p class="muted">
            View the live queue or check your patient status.
        </p>

        <div class="grid">
            <section class="card stat">
                <span>Now Serving</span>
                <strong id="currentNumber">Loading...</strong>
            </section>

            <section class="card stat">
                <span>Patients Waiting</span>
                <strong id="waitingCount">Loading...</strong>
            </section>

            <section class="card stat">
                <span>Estimated Wait</span>
                <strong id="estimatedWait">Loading...</strong>
            </section>

            <section class="card stat">
                <span>Clinic Status</span>
                <strong id="clinicStatus">Loading...</strong>
            </section>
        </div>

        <section class="card" aria-label="Current weather" style="margin-top: 20px;">
            <h2>Current Weather — Manila</h2>
            <p><strong id="weatherTemperature" aria-live="polite">Loading...</strong></p>
            <p id="weatherCondition" class="muted" aria-live="polite">Checking current weather</p>
            <button type="button" id="refreshWeather">Refresh Weather</button>
        </section>

        <div class="actions">
            <button id="refreshQueue">
                Refresh Queue
            </button>
        </div>

        <div id="queueMessage" class="message"></div>

        <div style="height: 24px;"></div>

        <section class="card">
            <h2>Check My Patient Status</h2>

            <form id="lookupForm">
                <div class="field">
                    <label for="queueNumber">
                        Queue Number
                    </label>

                    <input
                        id="queueNumber"
                        placeholder="Example: A-001"
                        required
                    >
                </div>

                <div class="actions">
                    <button type="submit">
                        Check Status
                    </button>
                </div>
            </form>

            <div id="patientResult" class="message"></div>
        </section>

        <section class="card">
            <h2>Current Queue</h2>

            <table>
                <thead>
                    <tr>
                        <th>Queue Number</th>
                        <th>Department</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody id="publicQueueRows">
                    <tr>
                        <td colspan="3">
                            Loading queue...
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <script src="queue-status-connected.js"></script>
    <script src="assets/weather.js"></script>
</body>
</html>