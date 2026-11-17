# Final Demonstration Script

**Total time:** ~8 minutes
**Setup:** XAMPP running (Apache + MySQL), `clinic_queue.sql` imported, browser tabs open for all pages.

## 1. Intro (30 sec) — any member
"Our system lets patients register, receive queue numbers, and check
status; staff manage the queue. It also shows live weather. We used
PHP + MySQL + plain JS and a feature-branch Git workflow."

## 2. Database & Queue Status Data (Kim — 1.5 min)
- Show `database/clinic_queue.sql` — patients table, ENUM status, indexes
- Show `config/database.php` — connection factory
- Open `models/Patient.php` — point out `getWaitingPatients()`
- Open `queuestatus.php` — live waiting count, estimated wait, clinic status

## 3. Interface & Testing (Cesca — 1.5 min)
- Open `addpatient.php` — layout, form validation
- Show `assets/style.css` — variables, responsive breakpoints
- Switch browser to mobile width — demonstrate responsive layout
- Open `docs/Test Plan and Results.pdf` — show one failing case + fix

## 4. Weather API & Security (Member 3 — 1.5 min)
- Show weather widget on `queuestatus.php`
- Show `api/weather.php` — proxy + timeout handling
- Show `config/.htaccess` — blocks direct access to config
- Trigger a failure (disable network or wrong coords) → graceful error
- Show `docs/api_integration.md` — endpoint, params, sample response

## 5. Queue Logic & Integration (Member 4 — 2 min)
- Register a new patient → note queue number `A-00N`
- Open staff dashboard → click **Call Next Patient**
  - Point out: previous Serving → Completed, new Waiting → Serving
- Click **Skip** on a waiting patient → status changes
- Click **Complete** on the serving patient → status changes
- Open `queuestatus.php` → Now Serving / Waiting / Estimated Wait update
- Enter queue number in lookup → shows status

## 6. Git Workflow (any member — 30 sec)
- Show GitHub Insights → Contributors (all 4 visible)
- Show Issues list and one merged PR
- Show tag `v1.0.0` on Releases page

## 7. Wrap-up (30 sec)
- Recap: working app, all requirements met, repo with history
- Q&A