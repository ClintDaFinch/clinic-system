async function loadQueueStatus() {
    const queueMessage = document.getElementById("queueMessage");
    const queueRows = document.getElementById("publicQueueRows");

    try {
        const response = await fetch("api/queue_status.php", {
            cache: "no-store"
        });

        const data = await response.json();

        if (!response.ok || data.success !== true) {
            throw new Error(
                data.message || "Queue data could not be loaded."
            );
        }

        document.getElementById("currentNumber").textContent =
            data.nowServing || "None";

        document.getElementById("waitingCount").textContent =
            data.patientsWaiting ?? 0;

        document.getElementById("estimatedWait").textContent =
            `${data.estimatedWaitMinutes ?? 0} minutes`;

        document.getElementById("clinicStatus").textContent =
            data.clinicStatus || "Unknown";

        queueRows.innerHTML = (data.patients || [])
            .map((patient) => {
                return `
                    <tr>
                        <td>${patient.queue_number}</td>
                        <td>${patient.department}</td>
                        <td>
                            <span class="status ${patient.status}">
                                ${patient.status}
                            </span>
                        </td>
                    </tr>
                `;
            })
            .join("")
            || `
                <tr>
                    <td colspan="3">
                        No queue entries available.
                    </td>
                </tr>
            `;

        queueMessage.className = "message";
        queueMessage.textContent = "";
    } catch (error) {
        queueMessage.className = "message show error";
        queueMessage.textContent = error.message;
    }
}

document
    .getElementById("refreshQueue")
    .addEventListener("click", loadQueueStatus);

document
    .getElementById("lookupForm")
    .addEventListener("submit", async (event) => {
        event.preventDefault();

        const result = document.getElementById("patientResult");
        const queueNumber = document
            .getElementById("queueNumber")
            .value
            .trim()
            .toUpperCase();

        try {
            const response = await fetch("api/queue_status.php", {
                cache: "no-store"
            });

            const data = await response.json();

            const patient = (data.patients || []).find((item) => {
                return item.queue_number.toUpperCase() === queueNumber;
            });

            if (!patient) {
                throw new Error("Queue number not found.");
            }

            result.className = "message show success";
            result.textContent =
                `${patient.queue_number} is ${patient.status}. ` +
                `Department: ${patient.department}.`;
        } catch (error) {
            result.className = "message show error";
            result.textContent = error.message;
        }
    });

loadQueueStatus();