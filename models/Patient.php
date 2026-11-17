<?php

require_once __DIR__ . "/../config/database.php";

class Patient
{
    private mysqli $connection;

    public function __construct()
    {
        $this->connection = getDatabaseConnection();
    }

    public function addPatient(
        string $patientName,
        string $contactNumber,
        string $department,
        string $queueNumber
    ): bool {
        $sql = "INSERT INTO patients
                (queue_number, patient_name, contact_number, department)
                VALUES (?, ?, ?, ?)";

        $statement = $this->connection->prepare($sql);
        $statement->bind_param(
            "ssss",
            $queueNumber,
            $patientName,
            $contactNumber,
            $department
        );

        return $statement->execute();
    }

    public function getAllPatients(): array
    {
        $sql = "SELECT * FROM patients ORDER BY created_at ASC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getWaitingPatients(): array
    {
        $sql = "SELECT * FROM patients
                WHERE status = 'Waiting'
                ORDER BY created_at ASC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCurrentServingPatient(): ?array
    {
        $sql = "SELECT * FROM patients
                WHERE status = 'Serving'
                ORDER BY created_at DESC
                LIMIT 1";
        $result = $this->connection->query($sql);
        $patient = $result->fetch_assoc();

        return $patient ?: null;
    }

    public function getNextWaitingPatient(): ?array
    {
        $result = $this->connection->query(
            "SELECT * FROM patients
             WHERE status = 'Waiting'
             ORDER BY created_at ASC
             LIMIT 1"
        );

        $patient = $result->fetch_assoc();

        return $patient ?: null;
    }

    public function markServingAsCompleted(): bool
    {
        return $this->connection->query(
            "UPDATE patients SET status = 'Completed'
             WHERE status = 'Serving'"
        );
    }

    public function findById(int $patientId): ?array
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM patients WHERE id = ? LIMIT 1"
        );

        $stmt->bind_param("i", $patientId);
        $stmt->execute();

        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();

        return $patient ?: null;
    }

    public function getQueueStatusSummary(int $minutesPerPatient = 5): array
    {
        $serving = $this->getCurrentServingPatient();
        $waiting = $this->getWaitingPatients();
        $waitingCount = count($waiting);

        return [
            "nowServing" => $serving["queue_number"] ?? "None",
            "patientsWaiting" => $waitingCount,
            "estimatedWaitMinutes" => $waitingCount * $minutesPerPatient,
            "clinicStatus" => $this->getClinicStatus(),
            "patients" => $this->getAllPatients()
        ];
    }

    public function getClinicStatus(): string
    {
        // Clinic open 8:00-17:00, Mon-Sat; adjust as needed
        $hour = (int) date("G");
        $day = (int) date("N"); // 1=Mon ... 7=Sun
        $open = ($day <= 6 && $hour >= 8 && $hour < 17);

        return $open ? "Open" : "Closed";
    }

    public function updateStatus(int $patientId, string $status): bool
    {
        $allowedStatuses = ["Waiting", "Serving", "Completed", "Skipped"];

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $sql = "UPDATE patients SET status = ? WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("si", $status, $patientId);

        return $statement->execute();
    }
}