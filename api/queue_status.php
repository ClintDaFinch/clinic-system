<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../models/Patient.php";

try {
    $patient = new Patient();

    $summary = $patient->getQueueStatusSummary(5);

    echo json_encode(array_merge(["success" => true], $summary));
} catch (Throwable $error) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Queue information could not be loaded."
    ]);
}