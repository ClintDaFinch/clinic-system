<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Status</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand">Clinic Queue System</div>

            <nav>
                <a href="addpatient.php">Register</a>
                <a href="queuestatus.php">Queue Status</a>
                <a href="clinic_queue.php">Staff Dashboard</a>
            </nav>
        </div>
    </header>

    <main>
        <h1>Check Patient Status</h1>
        <p class="muted">
            Enter your queue number to see your current status.
        </p>

        <section class="card">
            <form id="lookupForm">
                <div class="field">
                    <label for="queueNumber">Queue Number</label>
                    <input
                        id="queueNumber"
                        placeholder="Example: A-001"
                        required
                    >
                </div>

                <div class="actions">
                    <button>Check Status</button>
                </div>
            </form>

            <div id="result" class="message"></div>
        </section>
    </main>

    <script>
        document
            .getElementById('lookupForm')
            .addEventListener('submit', async (event) => {
                event.preventDefault();

                const result = document.getElementById('result');

                try {
                    const response = await fetch(
                        'api/queue_status.php'
                    );

                    const data = await response.json();

                    const wantedNumber = document
                        .getElementById('queueNumber')
                        .value
                        .trim()
                        .toUpperCase();

                    const patient = (data.patients || []).find((item) => {
                        return item.queue_number.toUpperCase()
                            === wantedNumber;
                    });

                    if (!patient) {
                        throw new Error('Queue number not found.');
                    }

                    result.className = 'message show success';
                    result.textContent =
                        `${patient.queue_number} - ${patient.status}. ` +
                        `Department: ${patient.department}.`;
                } catch (error) {
                    result.className = 'message show error';
                    result.textContent = error.message;
                }
            });
    </script>
</body>
</html>