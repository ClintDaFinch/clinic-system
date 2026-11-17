<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

$action = $_POST["action"] ?? "";
$patientId = (int) ($_POST["id"] ?? 0);

$allowedActions = ["call_next", "complete", "skip"];

if (!in_array($action, $allowedActions, true)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid queue action."
    ]);

    exit;
}

if ($action !== "call_next" && $patientId <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "A valid patient ID is required."
    ]);

    exit;
}

try {
    $connection = getDatabaseConnection();

    if ($action === "call_next") {
        $connection->begin_transaction();

        $connection->query(
            "UPDATE patients
             SET status = 'Completed'
             WHERE status = 'Serving'"
        );

        $result = $connection->query(
            "SELECT id
             FROM patients
             WHERE status = 'Waiting'
             ORDER BY created_at ASC
             LIMIT 1"
        );

        $nextPatient = $result->fetch_assoc();

        if (!$nextPatient) {
            $connection->commit();

            echo json_encode([
                "success" => false,
                "message" => "No waiting patients."
            ]);

            exit;
        }

        $statement = $connection->prepare(
            "UPDATE patients
             SET status = 'Serving'
             WHERE id = ?"
        );

        $statement->bind_param("i", $nextPatient["id"]);
        $success = $statement->execute();

        $connection->commit();
    } else {
        $newStatus = $action === "complete"
            ? "Completed"
            : "Skipped";

        $statement = $connection->prepare(
            "UPDATE patients
             SET status = ?
             WHERE id = ?"
        );

        $statement->bind_param(
            "si",
            $newStatus,
            $patientId
        );

        $success = $statement->execute();
    }

    echo json_encode([
        "success" => $success,
        "message" => $success
            ? "Queue updated successfully."
            : "Queue could not be updated."
    ]);
} catch (Throwable $error) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "A database error occurred while updating the queue."
    ]);
}