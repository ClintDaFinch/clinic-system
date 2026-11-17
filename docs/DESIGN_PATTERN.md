# Repository Pattern Implementation

## What Is the Repository Pattern?

The Repository Pattern mediates between the domain (business logic) and data mapping layers using a collection-like interface for accessing domain objects. In practice: **all database access goes through a repository class**, and pages/APIs never write raw SQL.

## Where It Lives in Our Code

| Layer | File | Responsibility |
|---|---|---|
| Configuration | `config/database.php` | Connection factory (`getDatabaseConnection()`) |
| Repository | `models/Patient.php` | All patient SQL: add, query, update status |
| Controllers (API) | `api/*.php` | Parse request, validate, call repository, return JSON |
| Views | `*.php` (root), `assets/style.css` | Render HTML, call APIs via JS |

## Diagram

```
Browser (JS)
    |
    | fetch() JSON
    v
api/*.php  (validates input, no SQL)
    |
    | calls
    v
Patient Repository  (all SQL lives here)
    |
    | uses
    v
database.php  (mysqli connection)
    |
    v
MySQL
```

## Code Walkthrough

### Repository method

```php
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
```

### Controller using it

```php
$patient = new Patient();
$next = $patient->getNextWaitingPatient();

if (!$next) {
    echo json_encode([
        "success" => false,
        "message" => "No waiting patients."
    ]);
    exit;
}
```

The controller never sees a SQL string. Swapping MySQL for another store means editing only `Patient.php`.

## Comparison: Repository vs. MVC

| Aspect | Repository Pattern (ours) | Full MVC |
|---|---|---|
| Separation | DB access isolated in `models/` | Three-way split: Model, View, Controller |
| View coupling | Views call APIs via JS | Controller injects model into view |
| Complexity | Low — suits small PHP apps | Higher — needs routing layer |
| Our choice | Chosen | — |
| Reason | The project scope is CRUD plus a few queries; full MVC would add routing boilerplate with no clear benefit. Repository keeps SQL in one place and lets each page stay a plain PHP file. |

## Test Evidence

- Register patient → row inserted via `Patient::addPatient()` → visible on dashboard
- Call next → `Patient::getNextWaitingPatient()` returns correct row
- Update status → `Patient::updateStatus()` changes only the target row
- Weather proxy returns normalized shape regardless of upstream errors

Screenshots in `evidence/testing/`.

### Repository Pattern verification

- All SQL lives in `models/Patient.php` (verified by inspection)