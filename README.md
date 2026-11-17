CLINIC QUEUE SYSTEM

The Clinic Queue System is a web-based application that allows patients to register, receive queue numbers, and check their queue status.

Clinic staff can manage the waiting list by calling the next patient, completing patients, and skipping patients.

FEATURES

- Patient registration
- Automatic queue-number generation
- Patient queue-status checking
- Staff dashboard
- Call next patient
- Complete patient
- Skip patient
- Estimated waiting time
- Weather API integration
- Input validation
- Error handling

TECHNOLOGIES USED:
HTML
CSS
JavaScript
PHP
MySQL
XAMPP
Open-Meteo Weather API
Visual Studio Code
Git
GitHub

INSTALLATION

1. Install XAMPP.
2. Copy the project folder into:

C:\xampp\htdocs\

3. Open XAMPP.
4. Start Apache and MySQL.
5. Open:

http://localhost/phpmyadmin

6. Import:

database/clinic_queue.sql

7. Confirm that the database is named:

clinic_queue_db

8. Open the application:

http://localhost/clinic-system/addpatient.php

MAIN PAGES

Registration page:

addpatient.php

Queue Status page:

queuestatus.php

Staff Dashboard:

clinic_queue.php

DATABASE

Database name:

clinic_queue_db

Main table:

patients

Patient statuses:

Waiting
Serving
Completed
Skipped

WEATHER API

The system uses the Open-Meteo Weather API.

Weather endpoint:

api/weather.php

TROUBLESHOOTING

If Apache is not running, start Apache in XAMPP.

If MySQL is not running, start MySQL in XAMPP.

If a database error appears, confirm that the database is named clinic_queue_db.

If registration fails, check that all required fields are completed.

If weather is unavailable, check the internet connection, PHP cURL, and weather configuration.