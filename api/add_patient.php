<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Patient.php";

$patientName = trim($_POST["patient_name"] ?? "");
$contactNumber = trim($_POST["contact_number"] ?? "");
$department = trim($_POST["department"] ?? "");

if (
    $patientName === "" ||
    $contactNumber === "" ||
    $department === ""
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "All patient fields are required."
    ]);

    exit;
}

if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $contactNumber)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Contact number format is invalid."
    ]);

    exit;
}

$allowedDepartments = ["General Medicine", "Pediatrics", "Dental", "Pharmacy"];

if (!in_array($department, $allowedDepartments, true)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid department selected."
    ]);

    exit;
}

try {
    $connection = getDatabaseConnection();

    $connection->begin_transaction();

    $result = $connection->query(
        "SELECT MAX(
            CAST(SUBSTRING_INDEX(queue_number, '-', -1) AS UNSIGNED)
        ) AS maximum_number
        FROM patients"
    );

    $row = $result->fetch_assoc();

    $nextNumber = ((int) ($row["maximum_number"] ?? 0)) + 1;
    $queueNumber = "A-" . str_pad(
        (string) $nextNumber,
        3,
        "0",
        STR_PAD_LEFT
    );

    $patient = new Patient();

    $saved = $patient->addPatient(
        $patientName,
        $contactNumber,
        $department,
        $queueNumber
    );

    if ($saved) {
        $connection->commit();
    } else {
        $connection->rollback();
    }

    echo json_encode([
        "success" => $saved,
        "message" => $saved
            ? "Patient added successfully."
            : "Patient could not be added.",
        "queue_number" => $queueNumber
    ]);
} catch (Throwable $error) {
    if (isset($connection) && $connection instanceof mysqli) {
        $connection->rollback();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "A database error occurred."
    ]);
}