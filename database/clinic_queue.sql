CREATE DATABASE IF NOT EXISTS clinic_queue_db;
USE clinic_queue_db;

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_number VARCHAR(20) NOT NULL UNIQUE,
    patient_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    department VARCHAR(50) NOT NULL,
    status ENUM('Waiting', 'Serving', 'Completed', 'Skipped') NOT NULL DEFAULT 'Waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_status_created ON patients (status, created_at);
CREATE INDEX idx_created_date ON patients (created_at);

INSERT INTO patients
    (queue_number, patient_name, contact_number, department, status)
VALUES
    ('A-001', 'Sample Patient', '09123456789', 'General Medicine', 'Serving');