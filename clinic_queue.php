<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard</title>
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
        <h1>Staff Dashboard</h1>
        <p class="muted">
            Manage the clinic waiting list.
        </p>

        <section class="card">
            <div class="actions">
                <button id="callNext" class="call-next">
                    Call Next Patient
                </button>

                <button class="secondary" id="refresh">
                    Refresh List
                </button>
            </div>

            <div id="message" class="message"></div>

            <table>
                <thead>
                    <tr>
                        <th>Queue</th>
                        <th>Patient</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="patientRows">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        const rows = document.getElementById('patientRows');
        const message = document.getElementById('message');

        function notice(text, type = 'success') {
            message.className = `message show ${type}`;
            message.textContent = text;
        }

        async function loadPatients() {
            try {
                const response = await fetch(
                    'api/queue_status.php',
                    { cache: 'no-store' }
                );

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.error || 'Unable to load patients.'
                    );
                }

                rows.innerHTML = (data.patients || [])
                    .map((patient) => {
                        let action = '-';

                        if (patient.status === 'Waiting') {
                            action =
                                `<button class="danger" ` +
                                `onclick="changeStatus(` +
                                `${patient.id}, 'skip')">Skip</button>`;
                        }

                        if (patient.status === 'Serving') {
                            action =
                                `<button ` +
                                `onclick="changeStatus(` +
                                `${patient.id}, 'complete')">Complete</button>`;
                        }

                        return `
                            <tr>
                                <td>${patient.queue_number}</td>
                                <td>${patient.patient_name}</td>
                                <td>${patient.department}</td>
                                <td>
                                    <span class="status ${patient.status}">
                                        ${patient.status}
                                    </span>
                                </td>
                                <td>${action}</td>
                            </tr>
                        `;
                    })
                    .join('')
                    || '<tr><td colspan="5">No patients yet.</td></tr>';
            } catch (error) {
                notice(
                    error.message || 'Unable to load patients.',
                    'error'
                );
            }
        }

        async function changeStatus(id, action) {
            try {
                const response = await fetch(
                    'api/queue_action.php',
                    {
                        method: 'POST',
                        body: new URLSearchParams({ id, action })
                    }
                );

                const data = await response.json();

                notice(
                    data.message || data.error,
                    data.success ? 'success' : 'error'
                );
            } catch (err) {
                notice('Unable to update patient status.', 'error');
            } finally {
                loadPatients();
            }
        }

        document
            .getElementById('callNext')
            .addEventListener('click', async (event) => {
                const btn = event.currentTarget;
                btn.disabled = true;

                try {
                    const response = await fetch(
                        'api/queue_action.php',
                        {
                            method: 'POST',
                            body: new URLSearchParams({
                                action: 'call_next'
                            })
                        }
                    );

                    const data = await response.json();

                    notice(
                        data.message || data.error,
                        data.success ? 'success' : 'error'
                    );
                } catch (err) {
                    notice('Unable to call next patient.', 'error');
                } finally {
                    btn.disabled = false;
                    loadPatients();
                }
            });

        document
            .getElementById('refresh')
            .addEventListener('click', loadPatients);

        loadPatients();
    </script>
</body>
</html>