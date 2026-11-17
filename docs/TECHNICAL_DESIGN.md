# Technical Design

## 1. Architecture

Three-tier, with a JS layer calling PHP APIs:
┌────────────────────────────────────────────┐
│ Presentation (HTML + CSS + JS) │
│ addpatient.php, queuestatus.php, │
│ clinic_queue.php, patient.php │
└──────────────┬─────────────────────────────┘
│ fetch() JSON
┌──────────────▼─────────────────────────────┐
│ API Layer (PHP) │
│ api/add_patient.php │
│ api/queue_action.php │
│ api/queue_status.php │
│ api/weather.php │
└──────────────┬─────────────────────────────┘
│ method calls
┌──────────────▼─────────────────────────────┐
│ Domain / Repository │
│ models/Patient.php │
└──────────────┬─────────────────────────────┘
│ mysqli
┌──────────────▼─────────────────────────────┐
│ MySQL │
│ clinic_queue_db.patients │
└────────────────────────────────────────────┘


## 2. Modules

| Module | Files | Purpose |
|---|---|---|
| Configuration | `config/database.php`, `config/weather.php`, `config/.htaccess` | Connection + API config factory |
| Model / Repository | `models/Patient.php` | All patient data operations |
| Registration | `addpatient.php`, `api/add_patient.php` | Form + queue-number generation |
| Queue management | `clinic_queue.php`, `api/queue_action.php` | Call next, complete, skip |
| Queue display | `queuestatus.php`, `patient.php`, `api/queue_status.php`, `queue-status-connected.js` | Public queue + patient lookup |
| Weather | `api/weather.php`, `assets/weather.js` | Open-Meteo proxy and display |
| Styling | `assets/style.css` | Responsive UI |
| Database | `database/clinic_queue.sql` | Schema + seed |

## 3. Data Flow — Registering a Patient

1. User submits `addpatient.php` form
2. JS `FormData` → `POST api/add_patient.php`
3. API validates name, contact, department
4. API opens transaction, queries MAX queue number `FOR UPDATE`
5. Computes `A-00N`, calls `Patient::addPatient()`
6. Commits, returns `{ success, message, queue_number }`
7. JS displays success message with queue number

## 4. Data Flow — Call Next

1. Staff clicks **Call Next Patient**
2. JS `POST api/queue_action.php` with `action=call_next`
3. API opens transaction
4. Sets any `Serving` → `Completed`
5. Selects oldest `Waiting` row
6. Sets that row → `Serving`
7. Commits, returns `{ success, message }`
8. Dashboard reloads list via `api/queue_status.php`

## 5. Data Flow — Weather API

1. Page loads `assets/weather.js`
2. JS fetches `api/weather.php` (server-side proxy)
3. `api/weather.php` reads `config/weather.php` (endpoint, default coords)
4. Validates coordinates, calls Open-Meteo
5. On success: returns temperature + condition
6. On failure/timeout: returns `{ success: false, message }`
7. JS renders either the widget or a graceful error

The proxy prevents exposing secrets and normalizes Open-Meteo's response.

## 6. Class / Component Relationships
getDatabaseConnection() ◄── used by ── Patient
│
├── addPatient()
├── getAllPatients()
├── getWaitingPatients()
├── getCurrentServingPatient()
├── getNextWaitingPatient()
├── markServingAsCompleted()
├── findById()
├── getQueueStatusSummary()
├── getClinicStatus()
└── updateStatus()


## 7. Key Implementation Decisions

| Decision | Rationale |
|---|---|
| Feature-branch Git flow | Clear per-member ownership, easy review |
| Repository pattern (not full MVC) | Small app; SQL isolation is enough |
| JSON APIs + JS fetch | Avoids full-page reloads; matches demo flow |
| `mysqli` prepared statements | Prevents SQL injection with zero extra deps |
| `FOR UPDATE` + transaction | Prevents duplicate queue numbers under concurrency |
| Queue number resets daily (by `DATE(created_at)`) | Real clinic behavior |
| 5 minutes/patient estimate | Simple, tunable via method param |
| Status ENUM in schema | DB-level correctness guarantee |
| Weather via server-side proxy | Hides API config; normalizes errors |