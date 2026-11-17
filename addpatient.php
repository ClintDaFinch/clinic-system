<?php
require_once __DIR__ . '/config/database.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Patient</title>
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
        <h1>Register a Patient</h1>
        <p class="muted">
            Enter the patient information to receive a queue number.
        </p>

        <section class="card">
            <form id="patientForm" action="api/add_patient.php" method="post">
                <div class="form-grid">
                    <div class="field full">
                        <label for="patient_name">Patient Name</label>
                        <input
                            id="patient_name"
                            name="patient_name"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="">Select a department</option>
                            <option>General Medicine</option>
                            <option>Pediatrics</option>
                            <option>Dental</option>
                            <option>Pharmacy</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="contact">Contact Number</label>
                        <input
                            id="contact"
                            name="contact_number"
                            maxlength="20"
                            required
                        >
                    </div>
                </div>

                <div class="actions">
                    <button id="submitButton">
                        Register Patient
                    </button>
                </div>

                <div id="message" class="message"></div>
            </form>
        </section>
    </main>

    <script>
        const form = document.getElementById('patientForm');
        const message = document.getElementById('message');
        const submitButton = document.getElementById('submitButton');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!form.reportValidity()) {
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Registering...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(
                        result.message || 'Registration failed.'
                    );
                }

                message.className = 'message show success';
                message.textContent =
                    `${result.message} Queue number: ` +
                    `${result.queue_number}`;

                form.reset();
            } catch (error) {
                message.className = 'message show error';
                message.textContent = error.message;
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Register Patient';
            }
        });
    </script>
</body>
</html>