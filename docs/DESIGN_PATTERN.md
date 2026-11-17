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
┌──────────────────┐
│ Browser (JS) │
└────────┬─────────┘
│ fetch()
▼
┌──────────────────┐
│ api/*.php │ ← validates input, no SQL
└────────┬─────────┘
│ calls
▼
┌──────────────────┐
│ Patient (Repo) │ ← all SQL lives here
└────────┬─────────┘
│ uses
▼
┌──────────────────┐
│ database.php │ ← mysqli connection
└────────┬─────────┘
│
▼
┌──────────────────┐
│ MySQL │
└──────────────────┘

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