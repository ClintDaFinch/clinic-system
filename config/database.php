<?php

/**
 * Creates the database connection used by the clinic system.
 */
function getDatabaseConnection(): mysqli
{
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "clinic_queue_db";

    $connection = new mysqli($host, $username, $password, $database);

    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    $connection->set_charset("utf8mb4");
    return $connection;
}
